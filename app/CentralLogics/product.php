<?php

namespace App\CentralLogics;


use App\Model\FavoriteProduct;
use App\Model\Product;
use App\Model\Review;
use App\User;

class ProductLogic
{
    public static function get_product($id)
    {
        return Product::active()->withCount(['wishlist'])->with(['rating', 'active_reviews', 'active_reviews.customer'])->where('id', $id)->first();
    }

    public static function get_latest_products($limit = 10, $offset = 1)
    {
        $paginator = Product::active()->withCount(['wishlist'])->with(['rating', 'active_reviews'])->latest()->paginate($limit, ['*'], 'page', $offset);
        /*$paginator->count();*/
        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_favorite_products($limit, $offset, $user_id)
    {
        $limit = is_null($limit) ? 10 : $limit;
        $offset = is_null($offset) ? 1 : $offset;

        $ids = User::with('favorite_products')->find($user_id)->favorite_products->pluck('product_id')->toArray();
        $favorite_products = Product::whereIn('id', $ids)->paginate($limit, ['*'], 'page', $offset);

        $formatted_products = Helpers::product_data_formatting($favorite_products, true);

        return [
            'total_size' => $favorite_products->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $formatted_products
        ];
    }

    public static function get_related_products($product_id)
    {
        $product = Product::find($product_id);
        return Product::active()->withCount(['wishlist'])->with(['rating', 'active_reviews'])->where('category_ids', $product->category_ids)
            ->where('id', '!=', $product->id)
            ->limit(10)
            ->get();
    }

    public static function search_products($name, $limit = 10, $offset = 1)
    {
        $key = explode(' ', $name);
        $paginator = Product::active()->withCount(['wishlist'])->with(['rating', 'active_reviews'])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_product_review($id)
    {
        $reviews = Review::active()->where('product_id', $id)->get();
        return $reviews;
    }

    public static function get_rating($reviews)
    {
        $rating5 = 0;
        $rating4 = 0;
        $rating3 = 0;
        $rating2 = 0;
        $rating1 = 0;
        foreach ($reviews as $key => $review) {
            if ($review->rating == 5) {
                $rating5 += 1;
            }
            if ($review->rating == 4) {
                $rating4 += 1;
            }
            if ($review->rating == 3) {
                $rating3 += 1;
            }
            if ($review->rating == 2) {
                $rating2 += 1;
            }
            if ($review->rating == 1) {
                $rating1 += 1;
            }
        }
        return [$rating5, $rating4, $rating3, $rating2, $rating1];
    }

    public static function get_overall_rating($reviews)
    {
        $totalRating = count($reviews);
        $rating = 0;
        foreach ($reviews as $key => $review) {
            $rating += $review->rating;
        }
        if ($totalRating == 0) {
            $overallRating = 0;
        } else {
            $overallRating = number_format($rating / $totalRating, 2);
        }

        return [$overallRating, $totalRating];
    }

    public static function get_popular_products($limit = 10, $offset = 1)
    {
        $paginator = Product::active()->with(['rating', 'active_reviews'])->orderBy('popularity_count', 'desc')->paginate($limit, ['*'], 'page', $offset);
        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }

}
