<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Review;
use App\Models\Grove;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Báo cáo tổng quan.
     *
     * Phạm vi TV3:
     * - Doanh thu
     * - Đơn hàng
     * - Sản phẩm
     * - Customer
     * - Tồn kho
     * - Voucher
     * - Grove
     * - Review
     *
     * Không thay đổi logic Dashboard hiện tại.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | KHOẢNG THỜI GIAN
        |--------------------------------------------------------------------------
        */

        $from = $request->query(
            'from',
            now()->startOfMonth()->toDateString()
        );

        $to = $request->query(
            'to',
            now()->toDateString()
        );

        /*
        |--------------------------------------------------------------------------
        | QUERY ĐƠN HÀNG TRONG KHOẢNG THỜI GIAN
        |--------------------------------------------------------------------------
        */

        $ordersQuery = Order::query()
            ->whereBetween(
                'created_at',
                [
                    $from . ' 00:00:00',
                    $to . ' 23:59:59',
                ]
            );

        /*
        |--------------------------------------------------------------------------
        | TỔNG ĐƠN HÀNG
        |--------------------------------------------------------------------------
        */

        $orderCount = (clone $ordersQuery)->count();

        /*
        |--------------------------------------------------------------------------
        | ĐƠN HOÀN THÀNH
        |--------------------------------------------------------------------------
        */

        $completedOrderCount = (clone $ordersQuery)
            ->where(
                'order_status',
                'completed'
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | ĐƠN ĐÃ HỦY
        |--------------------------------------------------------------------------
        */

        $cancelledOrderCount = (clone $ordersQuery)
            ->where(
                'order_status',
                'cancelled'
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | DOANH THU
        |--------------------------------------------------------------------------
        |
        | Chỉ tính các đơn đã hoàn thành.
        |
        */

        $revenue = (clone $ordersQuery)
            ->where(
                'order_status',
                'completed'
            )
            ->sum(
                'total_amount'
            );

        /*
        |--------------------------------------------------------------------------
        | TIỀN GIẢM GIÁ
        |--------------------------------------------------------------------------
        */

        $discountAmount = (clone $ordersQuery)
            ->where(
                'order_status',
                'completed'
            )
            ->sum(
                'discount_amount'
            );

        /*
        |--------------------------------------------------------------------------
        | PHÍ VẬN CHUYỂN
        |--------------------------------------------------------------------------
        */

        $shippingRevenue = (clone $ordersQuery)
            ->where(
                'order_status',
                'completed'
            )
            ->sum(
                'shipping_fee'
            );

        /*
        |--------------------------------------------------------------------------
        | ĐƠN THEO TRẠNG THÁI
        |--------------------------------------------------------------------------
        */

        $ordersByStatus = (clone $ordersQuery)
            ->select(
                'order_status',
                DB::raw(
                    'COUNT(*) as total'
                )
            )
            ->groupBy(
                'order_status'
            )
            ->orderByDesc(
                'total'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DOANH THU THEO NGÀY
        |--------------------------------------------------------------------------
        */

        $revenueByDate = (clone $ordersQuery)
            ->where(
                'order_status',
                'completed'
            )
            ->select(
                DB::raw(
                    'DATE(created_at) as report_date'
                ),
                DB::raw(
                    'SUM(total_amount) as revenue'
                ),
                DB::raw(
                    'COUNT(*) as order_count'
                )
            )
            ->groupBy(
                DB::raw(
                    'DATE(created_at)'
                )
            )
            ->orderBy(
                'report_date'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | SẢN PHẨM BÁN CHẠY
        |--------------------------------------------------------------------------
        */

        $topProducts = OrderDetail::query()
            ->join(
                'orders',
                'orders.id',
                '=',
                'order_details.order_id'
            )
            ->where(
                'orders.order_status',
                'completed'
            )
            ->whereBetween(
                'orders.created_at',
                [
                    $from . ' 00:00:00',
                    $to . ' 23:59:59',
                ]
            )
            ->select(
                'order_details.item_name',
                DB::raw(
                    'SUM(order_details.quantity) as total_sold'
                ),
                DB::raw(
                    'SUM(order_details.subtotal) as total_revenue'
                )
            )
            ->groupBy(
                'order_details.item_name'
            )
            ->orderByDesc(
                'total_sold'
            )
            ->limit(10)
            ->get();

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
        | VOUCHER
        |--------------------------------------------------------------------------
        */

        $voucherUsageCount = DB::table(
            'voucher_usages'
        )
            ->where(
                'status',
                'used'
            )
            ->whereBetween(
                'used_at',
                [
                    $from . ' 00:00:00',
                    $to . ' 23:59:59',
                ]
            )
            ->count();

        $voucherDiscount = DB::table(
            'voucher_usages'
        )
            ->where(
                'status',
                'used'
            )
            ->whereBetween(
                'used_at',
                [
                    $from . ' 00:00:00',
                    $to . ' 23:59:59',
                ]
            )
            ->sum(
                'discount_amount'
            );

        /*
        |--------------------------------------------------------------------------
        | REVIEW
        |--------------------------------------------------------------------------
        */

        $reviewCount = Review::whereBetween(
            'created_at',
            [
                $from . ' 00:00:00',
                $to . ' 23:59:59',
            ]
        )->count();

        $averageRating = Review::whereBetween(
            'created_at',
            [
                $from . ' 00:00:00',
                $to . ' 23:59:59',
            ]
        )->avg(
            'rating'
        );

        $reviewsByRating = Review::query()
            ->whereBetween(
                'created_at',
                [
                    $from . ' 00:00:00',
                    $to . ' 23:59:59',
                ]
            )
            ->select(
                'rating',
                DB::raw(
                    'COUNT(*) as total'
                )
            )
            ->groupBy(
                'rating'
            )
            ->orderBy(
                'rating'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | TỒN KHO
        |--------------------------------------------------------------------------
        */

        $totalProducts = Product::count();

        $totalStockQuantity = Product::sum(
            'stock_quantity'
        );

        $outOfStockProducts = Product::where(
            'stock_quantity',
            0
        )->count();

        $lowStockProducts = Product::query()
            ->where(
                'stock_quantity',
                '>',
                0
            )
            ->whereColumn(
                'stock_quantity',
                '<=',
                'low_stock_threshold'
            )
            ->count();

        $stockSummary = Product::query()
            ->select(
                'id',
                'name',
                'sku',
                'stock_quantity',
                'low_stock_threshold'
            )
            ->orderBy(
                'stock_quantity'
            )
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GROVE
        |--------------------------------------------------------------------------
        */

        $groveCount = Grove::count();

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.reports.index',
            compact(
                'from',
                'to',
                'orderCount',
                'completedOrderCount',
                'cancelledOrderCount',
                'revenue',
                'discountAmount',
                'shippingRevenue',
                'ordersByStatus',
                'revenueByDate',
                'topProducts',
                'customerCount',
                'voucherUsageCount',
                'voucherDiscount',
                'reviewCount',
                'averageRating',
                'reviewsByRating',
                'totalProducts',
                'totalStockQuantity',
                'outOfStockProducts',
                'lowStockProducts',
                'stockSummary',
                'groveCount'
            )
        );
    }
}