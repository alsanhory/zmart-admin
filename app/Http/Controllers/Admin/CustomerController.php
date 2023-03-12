<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Conversation;
use App\Model\Newsletter;
use App\Model\Order;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;

class CustomerController extends Controller
{
    public function customer_list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $customers = User::with(['orders'])->
                    where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('f_name', 'like', "%{$value}%")
                                ->orWhere('l_name', 'like', "%{$value}%")
                                ->orWhere('phone', 'like', "%{$value}%")
                                ->orWhere('email', 'like', "%{$value}%");
                        }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $customers = User::with(['orders']);
        }
        $customers = $customers->latest()->paginate(Helpers::getPagination())->appends($query_param);

        return view('admin-views.customer.list', compact('customers','search'));
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $customers=User::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view'=>view('admin-views.customer.partials._table',compact('customers'))->render()
        ]);
    }

    public function view(Request $request,$id)
    {
        $customer = User::find($id);
        if (isset($customer)) {
            $query_param = [];
            $search = $request['search'];
            if($request->has('search'))
            {
                $key = explode(' ', $request['search']);
                $orders = Order::where(['user_id' => $id])
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('id', 'like', "%{$value}%")
                                ->orWhere('order_amount', 'like', "%{$value}%");
                        }
                });
                $query_param = ['search' => $request['search']];
            }else{
                $orders = Order::where(['user_id' => $id]);
            }
            $orders = $orders->latest()->paginate(Helpers::getPagination())->appends($query_param);

            //$orders = Order::latest()->where(['user_id' => $id])->paginate(2);
            return view('admin-views.customer.customer-view', compact('customer', 'orders', 'search'));
        }
        Toastr::error(translate('Customer not found!'));
        return back();
    }

    public function subscribed_emails(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $newsletters = Newsletter::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('email', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $newsletters = new Newsletter();
        }

        $newsletters = $newsletters->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.customer.subscribed-list', compact('newsletters', 'search'));
    }

    public function delete(Request $request)
    {
        $customer = User::find($request->id);
        //return $customer;
        if (Storage::disk('public')->exists('customer/' . $customer['image'])) {
            Storage::disk('public')->delete('customer/' . $customer['image']);
        }

        $conversations = Conversation::where('user_id', $request->id)->get();
        foreach ($conversations as $conversation){
            if ($conversation->checked == 0){
                $conversation->checked = 1;
                $conversation->save();
            }
        }

        $customer->delete();
        Toastr::success(translate('Customer removed!'));
        return back();
    }

    public function status(Request $request)
    {
        $user = User::find($request->id);
        $user->is_block = $request->status;
        $user->save();
        Toastr::success(translate('Block status updated!'));
        return back();
    }


    public function export_customer(Request $request)
    {
        $storage = [];
        $query_param = [];
        $search = $request['search'];

        $customers = User::when($request->has('search'), function ($query) use ($request) {
                $key = explode(' ', $request['search']);
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%");
                    }
                });
                $query_param = ['search' => $request['search']];
            })
            ->get();

        foreach($customers as $customer){

            $storage[] = [
                'first_name' => $customer['f_name'],
                'last_name' => $customer['l_name'],
                'phone' => $customer['phone'],
                'email' => $customer['email'],
            ];
        }
        return (new FastExcel($storage))->download('customers.xlsx');
    }
}
