<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\ProductCompatibilityRule;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;



class CompatibilityRuleController extends Controller
{


    /**
     * Danh sách quy tắc tương thích
     */
    public function index(Request $request)
    {

        $keyword = $request->keyword;

        $compatibility = $request->compatibility;

        $status = $request->status;



        $rules = ProductCompatibilityRule::query()

            ->with([
                'sourceProduct:id,name,sku',
                'targetProduct:id,name,sku',
            ])


            ->when(
                $keyword,
                function ($query) use ($keyword) {

                    $query->where(function ($q) use ($keyword) {


                        $q->whereHas(
                            'sourceProduct',
                            function ($productQuery) use ($keyword) {

                                $productQuery
                                    ->where(
                                        'name',
                                        'like',
                                        "%{$keyword}%"
                                    )
                                    ->orWhere(
                                        'sku',
                                        'like',
                                        "%{$keyword}%"
                                    );

                            }
                        )


                        ->orWhereHas(
                            'targetProduct',
                            function ($productQuery) use ($keyword) {

                                $productQuery
                                    ->where(
                                        'name',
                                        'like',
                                        "%{$keyword}%"
                                    )
                                    ->orWhere(
                                        'sku',
                                        'like',
                                        "%{$keyword}%"
                                    );

                            }
                        );


                    });


                }
            )


            ->when(
                $compatibility !== null
                &&
                $compatibility !== '',
                function ($query) use ($compatibility) {

                    $query->where(
                        'is_compatible',
                        $compatibility
                    );

                }
            )


            ->when(
                $status !== null
                &&
                $status !== '',
                function ($query) use ($status) {

                    $query->where(
                        'is_active',
                        $status
                    );

                }
            )


            ->latest('id')

            ->paginate(15)

            ->withQueryString();



        return view(
            'admin.compatibility-rules.index',
            compact(
                'rules',
                'keyword',
                'compatibility',
                'status'
            )
        );

    }





    /**
     * Form thêm mới
     */
    public function create()
    {

        $products = Product::query()

            ->where(
                'is_active',
                true
            )

            ->orderBy(
                'name'
            )

            ->get();



        return view(
            'admin.compatibility-rules.create',
            compact('products')
        );

    }





    /**
     * Lưu quy tắc mới
     */
    public function store(Request $request)
    {


        $validated = $request->validate(

            [

                'source_product_id' => [

                    'required',

                    'exists:products,id',

                ],



                'target_product_id' => [

                    'required',

                    'exists:products,id',

                    'different:source_product_id',


                    Rule::unique(
                        'product_compatibility_rules'
                    )
                    ->where(
                        function ($query) use ($request) {

                            return $query->where(
                                'source_product_id',
                                $request->source_product_id
                            );

                        }
                    ),

                ],



                'is_compatible' => [

                    'required',

                    'boolean',

                ],



                'reason' => [

                    'nullable',

                    'string',

                    'max:2000',

                    'required_if:is_compatible,0',

                ],



                'is_active' => [

                    'required',

                    'boolean',

                ],

            ],

            [

                'source_product_id.required'
                    =>
                    'Vui lòng chọn sản phẩm nguồn.',


                'target_product_id.required'
                    =>
                    'Vui lòng chọn sản phẩm đích.',


                'target_product_id.different'
                    =>
                    'Sản phẩm nguồn và đích phải khác nhau.',


                'target_product_id.unique'
                    =>
                    'Quy tắc giữa hai sản phẩm đã tồn tại.',


                'reason.required_if'
                    =>
                    'Vui lòng nhập lý do khi chọn Không tương thích.',

            ]

        );




        ProductCompatibilityRule::create(
            $validated
        );




        return redirect()

            ->route(
                'admin.compatibility-rules.index'
            )

            ->with(
                'success',
                'Thêm quy tắc tương thích thành công.'
            );


    }







    /**
     * Xem chi tiết
     */
    public function show(
        ProductCompatibilityRule $compatibilityRule
    )
    {

        return redirect()

            ->route(
                'admin.compatibility-rules.edit',
                $compatibilityRule
            );

    }







    /**
     * Form chỉnh sửa
     */
    public function edit(
        ProductCompatibilityRule $compatibilityRule
    )
    {


        $products = Product::query()

            ->where(
                'is_active',
                true
            )

            ->orderBy(
                'name'
            )

            ->get();



        return view(
            'admin.compatibility-rules.edit',
            compact(
                'compatibilityRule',
                'products'
            )
        );


    }







    /**
     * Cập nhật
     */
    public function update(
        Request $request,
        ProductCompatibilityRule $compatibilityRule
    )
    {


        $validated = $request->validate(

            [

                'source_product_id' => [

                    'required',

                    'exists:products,id',

                ],



                'target_product_id' => [

                    'required',

                    'exists:products,id',

                    'different:source_product_id',



                    Rule::unique(
                        'product_compatibility_rules'
                    )

                    ->ignore(
                        $compatibilityRule->id
                    )

                    ->where(
                        function ($query) use ($request) {

                            return $query->where(
                                'source_product_id',
                                $request->source_product_id
                            );

                        }
                    ),

                ],



                'is_compatible' => [

                    'required',

                    'boolean',

                ],



                'reason' => [

                    'nullable',

                    'string',

                    'max:2000',

                    'required_if:is_compatible,0',

                ],



                'is_active' => [

                    'required',

                    'boolean',

                ],

            ],

            [

                'reason.required_if'
                    =>
                    'Vui lòng nhập lý do khi chọn Không tương thích.',

            ]

        );




        $compatibilityRule->update(
            $validated
        );




        return redirect()

            ->route(
                'admin.compatibility-rules.index'
            )

            ->with(
                'success',
                'Cập nhật quy tắc tương thích thành công.'
            );


    }







    /**
     * Bật / tắt trạng thái
     */
    public function toggleStatus(
        ProductCompatibilityRule $compatibilityRule
    )
    {


        $compatibilityRule->update([

            'is_active'
                =>
                !$compatibilityRule->is_active,

        ]);



        return redirect()

            ->route(
                'admin.compatibility-rules.index'
            )

            ->with(
                'success',
                'Đã cập nhật trạng thái quy tắc tương thích.'
            );


    }







    /**
     * Xóa
     */
    public function destroy(
        ProductCompatibilityRule $compatibilityRule
    )
    {


        $compatibilityRule->delete();



        return redirect()

            ->route(
                'admin.compatibility-rules.index'
            )

            ->with(
                'success',
                'Xóa quy tắc tương thích thành công.'
            );


    }


}