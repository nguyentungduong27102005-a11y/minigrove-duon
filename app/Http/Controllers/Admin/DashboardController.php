<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;


use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\User;


use Illuminate\Support\Facades\DB;



class DashboardController extends Controller
{

    public function index()
    {


        /*
        |--------------------------------------------------------------------------
        | THỐNG KÊ CƠ BẢN
        |--------------------------------------------------------------------------
        */


        $productCount = Product::count();


        $categoryCount = Category::count();



        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */


        $customerCount = User::whereHas(
            'role',
            function ($query) {

                $query->where(
                    'name',
                    'customer'
                );

            }
        )->count();




        /*
        |--------------------------------------------------------------------------
        | STAFF
        |--------------------------------------------------------------------------
        */


        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */


        $orderCount = Order::count();



        $processingOrderCount = Order::whereIn(
            'order_status',
            [
                'pending',
                'confirmed',
                'preparing',
                'shipping',
            ]
        )->count();



        $completedRevenue = Order::where(
            'order_status',
            'completed'
        )
        ->sum(
            'total_amount'
        );





        /*
        |--------------------------------------------------------------------------
        | RETURN REQUEST
        |--------------------------------------------------------------------------
        */


        $pendingReturnCount = ReturnRequest::where(
            'status',
            'pending'
        )
        ->count();





        /*
        |--------------------------------------------------------------------------
        | ORDER RECENT
        |--------------------------------------------------------------------------
        */


        $recentOrders = Order::query()

            ->latest()

            ->limit(5)

            ->get();





        /*
        |--------------------------------------------------------------------------
        | TOP PRODUCT
        |--------------------------------------------------------------------------
        */


        $topProducts = OrderDetail::query()

            ->select(
                'item_name',

                DB::raw(
                    'SUM(quantity) as total_sold'
                ),

                DB::raw(
                    'SUM(subtotal) as total_revenue'
                )
            )

            ->groupBy(
                'item_name'
            )

            ->orderByDesc(
                'total_sold'
            )

            ->limit(5)

            ->get();





        /*
        |--------------------------------------------------------------------------
        | LOW STOCK
        |--------------------------------------------------------------------------
        */


        $lowStockProducts = Product::query()

            ->whereColumn(
                'stock_quantity',
                '<=',
                'low_stock_threshold'
            )

            ->limit(5)

            ->get();





        return view(

            'admin.dashboard',

            compact(


                'productCount',

                'categoryCount',

                'customerCount',

                'orderCount',



                'processingOrderCount',

                'completedRevenue',

                'pendingReturnCount',

                'recentOrders',

                'topProducts',

                'lowStockProducts'


            )

        );


    }

}