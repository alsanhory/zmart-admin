<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function order_index()
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }
        return view('admin-views.report.order-index');
    }

    public function earning_index()
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }
        return view('admin-views.report.earning-index');
    }

    public function set_date(Request $request)
    {
        $fromDate = Carbon::parse($request['from'])->startOfDay();
        $toDate = Carbon::parse($request['to'])->endOfDay();

        session()->put('from_date', $fromDate);
        session()->put('to_date', $toDate);
        return back();
    }

    public function sale_report()
    {
        return view('admin-views.report.sale-report');
    }

    public function sale_filter(Request $request)
    {
        $branch_id = $request['branch_id'];
        $from = $to = null;
        if (!is_null($request->from) && !is_null($request->to))
        {
            $from = Carbon::parse($request->from)->format('Y-m-d');
            $to = Carbon::parse($request->to)->format('Y-m-d');
        }


        if ($branch_id == 'all') {
            $orders = Order::
                when((!is_null($from) && !is_null($to)), function ($query) use ($from, $to) {
                    //return $query->whereBetween('created_at', [$from, $to]);
                    return $query->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to);
                })->pluck('id')->toArray();
        } else {
            $orders = Order::where(['branch_id' => $branch_id])
                ->when((!is_null($from) && !is_null($to)), function ($query) use ($from, $to) {
                    //return $query->whereBetween('created_at', [$from, $to]);
                    return $query->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to);
                })->pluck('id')->toArray();
        }

        $data = [];
        $total_sold = 0;
        $total_qty = 0;

        foreach (OrderDetail::whereIn('order_id', $orders)->get() as $detail) {
            $price = $detail['price'] - $detail['discount_on_product'];
            $ord_total = $price * $detail['quantity'];

            $product = json_decode($detail->product_details, true);
            $images = $product['image'] != null ? (gettype($product['image'])!='array'?json_decode($product['image'],true):$product['image']) : [];
            $product_image = count($images) > 0 ? $images[0] : null;

            $data[] = [
                'product_id' => $product['id'],
                'product_name' => $product['name'],
                'product_image' => $product_image,
                'order_id' => $detail['order_id'],
                'date' => $detail['created_at'],
                'price' => $ord_total,
                'quantity' => $detail['quantity'],
            ];

            $total_sold += $ord_total;
            $total_qty += $detail['quantity'];
        }

        return response()->json([
            'order_count' => count($data),
            'item_qty' => $total_qty,
            'order_sum' => \App\CentralLogics\Helpers::set_symbol($total_sold),
            'view' => view('admin-views.report.partials._table', compact('data'))->render(),
        ]);
    }

    public function export_sale_report()
    {
        $data = session('export_sale_data');
        $pdf = PDF::loadView('admin-views.report.partials._report', compact('data'));
        return $pdf->download('sale_report_'.rand(00001,99999) . '.pdf');
    }


    public function new_sale_report(Request $request)
    {
        $query_param = [];
        $branches = Branch::all();
        $branch_id = $request['branch_id'];
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];

        if ($branch_id == 'all') {
            $orders = Order::
            when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                return $query->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<=', $end_date);
            })->pluck('id')->toArray();
            $query_param = ['branch_id' => $branch_id, 'start_date' => $start_date,'end_date' => $end_date ];

        } else {
            $orders = Order::where(['branch_id' => $branch_id])
                ->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                })->pluck('id')->toArray();
            $query_param = ['branch_id' => $branch_id, 'start_date' => $start_date,'end_date' => $end_date ];
        }
        $order_details = OrderDetail::withCount(['order'])->whereIn('order_id', $orders)->paginate(Helpers::getPagination())->appends($query_param);

        $data = [];
        $total_sold = 0;
        $total_qty = 0;
        foreach (OrderDetail::whereIn('order_id', $orders)->get() as $detail) {
            $price = $detail['price'] - $detail['discount_on_product'];
            $ord_total = $price * $detail['quantity'];

            $product = json_decode($detail->product_details, true);

            $data[] = [
                'product_id' => $product['id'],
            ];
            $total_sold += $ord_total;
            $total_qty += $detail['quantity'];
        }

        $total_order = count($data);
       // dd($total_order, $total_sold, $total_qty);

        return view('admin-views.report.new-sale-report', compact( 'orders', 'total_order', 'total_sold', 'total_qty', 'order_details', 'branches', 'branch_id', 'start_date', 'end_date'));
    }
}
