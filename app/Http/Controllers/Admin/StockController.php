<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockHistory;
use App\Services\StockService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


class StockController extends Controller
{

    public function __construct(
        private readonly StockService $stockService
    ) {
    }



    /**
     * Danh sách tồn kho Product.
     */
    public function index(Request $request)
    {

        $status = trim(
            (string) $request->query('status', '')
        );


        if (!in_array(
            $status,
            [
                '',
                'in_stock',
                'low_stock',
                'out_of_stock',
            ],
            true
        )) {

            $status = '';

        }



        $keyword = trim(
            (string) $request->query('keyword', '')
        );



        /*
        |--------------------------------------------------------------------------
        | PRODUCT
        |--------------------------------------------------------------------------
        */

        $stocks = DB::table('products')
            ->select([
                DB::raw("'PRODUCT' as stock_type"),

                'products.id as item_id',
                'products.name',
                'products.sku',
                'products.stock_quantity',
                'products.low_stock_threshold',

                DB::raw("'Sản phẩm' as item_type_name"),

                'products.is_active',
                'products.updated_at',
            ]);




        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */


        if ($keyword !== '') {


            $stocks->where(function ($query) use ($keyword) {


                $query
                    ->where(
                        'products.name',
                        'like',
                        '%' . $keyword . '%'
                    )
                    ->orWhere(
                        'products.sku',
                        'like',
                        '%' . $keyword . '%'
                    );


            });

        }



        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */


        $this->applyStockStatusFilter(
            $stocks,
            'products',
            $status
        );




        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */


        $stocks = $stocks
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();




        /*
        |--------------------------------------------------------------------------
        | STATISTICS
        |--------------------------------------------------------------------------
        */


        $productCount = Product::count();


        $outOfStockCount =
            Product::where(
                'stock_quantity',
                0
            )->count();



        $lowStockCount =
            Product::where(
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




        return view(
            'admin.stocks.index',
            compact(
                'stocks',
                'status',
                'keyword',
                'productCount',
                'outOfStockCount',
                'lowStockCount'
            )
        );

    }




    /**
     * Nhập / xuất / điều chỉnh tồn kho.
     */
    public function change(
        Request $request,
        string $stockType,
        int $itemId
    ) {


        $stockType = strtoupper(
            trim($stockType)
        );



        if ($stockType !== 'PRODUCT') {

            throw ValidationException::withMessages([
                'stock_type'
                =>
                'Chỉ hỗ trợ tồn kho sản phẩm.'
            ]);

        }




        $request->validate([

            'operation' => [

                'required',

                Rule::in([
                    'import',
                    'export',
                    'adjust',
                ]),

            ],


            'quantity' => [

                'required',
                'integer',
                'min:0',
                'max:1000000',

            ],


            'note' => [

                'required',
                'string',
                'max:1000',

            ],

        ]);




        $operation =
            $request->string(
                'operation'
            )->toString();



        $quantity =
            (int)
            $request->input('quantity');



        $note =
            trim(
                (string)
                $request->input('note')
            );





        if (
            in_array(
                $operation,
                [
                    'import',
                    'export'
                ],
                true
            )
            &&
            $quantity <= 0
        ) {


            throw ValidationException::withMessages([

                'quantity'
                =>
                'Số lượng nhập hoặc xuất kho phải lớn hơn 0.',

            ]);

        }





        if ($operation === 'import') {


            $this->stockService->import(

                stockType: 'PRODUCT',

                itemId: $itemId,

                quantity: $quantity,

                updatedBy: auth()->id(),

                note: $note

            );


            $message =
                'Nhập kho thành công.';



        } elseif ($operation === 'export') {



            $this->stockService->export(

                stockType: 'PRODUCT',

                itemId: $itemId,

                quantity: $quantity,

                updatedBy: auth()->id(),

                note: $note

            );


            $message =
                'Xuất kho thành công.';



        } else {


            $this->stockService->adjust(

                stockType: 'PRODUCT',

                itemId: $itemId,

                newQuantity: $quantity,

                updatedBy: auth()->id(),

                note: $note

            );


            $message =
                'Điều chỉnh tồn kho thành công.';


        }




        return back()->with(
            'success',
            $message
        );

    }





    /**
     * Cập nhật ngưỡng cảnh báo.
     */
    public function updateThreshold(
        Request $request,
        string $stockType,
        int $itemId
    ) {


        $validated =
            $request->validate([

                'low_stock_threshold' => [

                    'required',
                    'integer',
                    'min:0',
                    'max:1000000',

                ],

            ]);




        $item =
            Product::findOrFail(
                $itemId
            );



        $item->low_stock_threshold =
            (int)
            $validated[
                'low_stock_threshold'
            ];



        $item->save();



        return back()->with(
            'success',
            'Cập nhật ngưỡng cảnh báo tồn kho thành công.'
        );

    }





    /**
     * Lịch sử biến động kho.
     */
    public function history(
        string $stockType,
        int $itemId
    ) {


        $item =
            Product::findOrFail(
                $itemId
            );



        $histories =
            StockHistory::query()

            ->with([
                'order:id,order_code',
                'updater:id,name',
            ])

            ->where(
                'stock_type',
                'PRODUCT'
            )

            ->where(
                'product_id',
                $itemId
            )

            ->latest('id')

            ->paginate(15);



        return view(
    'admin.stocks.history',
    compact(
        'item',
        'histories',
        'stockType'
    )
);

    }





    private function applyStockStatusFilter(
        $query,
        string $table,
        string $status
    ): void {


        if ($status === 'out_of_stock') {


            $query->where(
                $table.'.stock_quantity',
                0
            );


            return;

        }




        if ($status === 'low_stock') {


            $query

                ->where(
                    $table.'.stock_quantity',
                    '>',
                    0
                )

                ->whereColumn(
                    $table.'.stock_quantity',
                    '<=',
                    $table.'.low_stock_threshold'
                );


            return;

        }





        if ($status === 'in_stock') {


            $query->whereColumn(

                $table.'.stock_quantity',

                '>',

                $table.'.low_stock_threshold'

            );

        }

    }





    private function findStockItem(
        string $stockType,
        int $itemId
    ): Model {


        return Product::findOrFail(
            $itemId
        );

    }


}