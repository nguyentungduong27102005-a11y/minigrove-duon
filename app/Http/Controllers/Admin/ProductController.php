<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Danh sách Product.
     */
    public function index(Request $request)
    {
        $keyword = trim((string) $request->keyword);
        $categoryId = $request->category_id;
        $status = $request->status;
        $products = Product::with([
            'category',
            'images' => function ($query) {
                $query->orderByDesc('is_primary')
                    ->orderBy('sort_order');
            },
        ])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where(
                        'name',
                        'like',
                        '%'.$keyword.'%'
                    )
                        ->orWhere(
                            'sku',
                            'like',
                            '%'.$keyword.'%'
                        );
                });
            })
            ->when(
                $categoryId,
                function ($query) use ($categoryId) {
                    $query->where(
                        'category_id',
                        $categoryId
                    );
                }
            )
            ->when(
                $status !== null && $status !== '',
                function ($query) use ($status) {
                    $query->where(
                        'is_active',
                        (int) $status
                    );
                }
            )
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();
        $categories = Category::orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view(
            'admin.products.index',
            compact(
                'products',
                'categories',
                'keyword',
                'categoryId',
                'status'
            )
        );
    }

    /**
     * Form thêm Product.
     */
    public function create()
    {
        $categories = Category::where(
            'is_active',
            true
        )
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view(
            'admin.products.create',
            compact('categories')
        );
    }

    /**
     * Lưu Product mới.
     */
    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        $studioProductType = $this->resolveStudioProductType(
            (int) $validated['category_id']
        );

        DB::transaction(
            function () use (
                $request,
                $validated,
                $studioProductType
            ) {
                $product = Product::create([
                    'category_id' => $validated['category_id'],

                    // Danh mục Studio tự quyết định loại thành phần.
                    'product_type' => $studioProductType,
                    'is_studio_item' => $studioProductType !== null,

                    // Bình mới mặc định sức chứa 8 thành phần.
                    'studio_capacity' => $studioProductType === 'container'
                            ? 8
                            : null,

                    'name' => $validated['name'],
                    'sku' => strtoupper($validated['sku']),
                    'ecosystem_type' => $validated['ecosystem_type'],
                    'list_price' => $validated['list_price'],
                    'sale_price' => $validated['sale_price'] ?? null,
                    'size' => $validated['size'] ?? null,
                    'care_level' => $validated['care_level'],
                    'light_requirement' => $validated['light_requirement'] ?? null,
                    'care_cycle_days' => $validated['care_cycle_days'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'concept_story' => $validated['concept_story'] ?? null,
                    'care_instructions' => $validated['care_instructions'] ?? null,
                    'stock_quantity' => $validated['stock_quantity'],
                    'is_active' => $request->boolean('is_active'),
                    'is_featured' => $request->boolean('is_featured'),

                    // Chọn danh mục Studio thì tự động được dùng trong Studio.
                    'is_grove_eligible' => $studioProductType !== null,
                ]);

                /*
                 * Upload nhiều ảnh.
                 * Ảnh đầu tiên là ảnh chính.
                 */
                if ($request->hasFile('images')) {
                    $primaryImageIndex = (int) (
                        $validated['primary_image_index'] ?? 0
                    );

                    foreach (
                        $request->file('images') as $index => $image
                    ) {
                        $path = $image->store(
                            'products',
                            'public'
                        );

                        $product->images()->create([
                            'image_path' => $path,
                            'is_primary' => $index === $primaryImageIndex,
                            'sort_order' => $index,
                        ]);
                    }
                }
            }
        );

        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                'Thêm sản phẩm thành công.'
            );
    }

    /**
     * Form sửa Product.
     */
    public function edit(Product $product)
    {
        $product->load([
            'images' => function ($query) {
                $query->orderByDesc('is_primary')
                    ->orderBy('sort_order');
            },
        ]);
        $categories = Category::orderBy(
            'display_order'
        )
            ->orderBy('name')
            ->get();

        return view(
            'admin.products.edit',
            compact(
                'product',
                'categories'
            )
        );
    }

    /**
     * Cập nhật Product.
     */
    public function update(
        Request $request,
        Product $product
    ) {
        $validated = $this->validateProduct(
            $request,
            $product
        );

        $studioProductType = $this->resolveStudioProductType(
            (int) $validated['category_id']
        );

        DB::transaction(
            function () use (
                $request,
                $validated,
                $product,
                $studioProductType
            ) {
                $product->update([
                    'category_id' => $validated['category_id'],

                    // Danh mục Studio tự quyết định loại thành phần.
                    'product_type' => $studioProductType,
                    'is_studio_item' => $studioProductType !== null,

                    // Nếu vẫn là Bình thì giữ capacity cũ;
                    // nếu vừa chuyển sang Bình thì mặc định 8.
                    'studio_capacity' => $studioProductType === 'container'
                            ? ($product->studio_capacity ?: 8)
                            : null,

                    'name' => $validated['name'],
                    'sku' => strtoupper($validated['sku']),
                    'ecosystem_type' => $validated['ecosystem_type'],
                    'list_price' => $validated['list_price'],
                    'sale_price' => $validated['sale_price'] ?? null,
                    'size' => $validated['size'] ?? null,
                    'care_level' => $validated['care_level'],
                    'light_requirement' => $validated['light_requirement'] ?? null,
                    'care_cycle_days' => $validated['care_cycle_days'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'concept_story' => $validated['concept_story'] ?? null,
                    'care_instructions' => $validated['care_instructions'] ?? null,
                    'stock_quantity' => $validated['stock_quantity'],
                    'is_active' => $request->boolean('is_active'),
                    'is_featured' => $request->boolean('is_featured'),

                    // Chọn danh mục Studio thì tự động được dùng trong Studio.
                    'is_grove_eligible' => $studioProductType !== null,
                ]);

                /*
                 * Upload thêm ảnh.
                 */
                if ($request->hasFile('images')) {
                    $hasPrimaryImage =
                        $product->images()
                            ->where(
                                'is_primary',
                                true
                            )
                            ->exists();

                    $newPrimaryImageIndex =
                        array_key_exists(
                            'primary_image_index',
                            $validated
                        )
                            ? (int) $validated[
                                'primary_image_index'
                            ]
                            : null;

                    if ($newPrimaryImageIndex !== null) {
                        $product->images()->update([
                            'is_primary' => false,
                        ]);

                        $hasPrimaryImage = false;
                    }

                    $currentMaxOrder =
                        $product->images()
                            ->max('sort_order') ?? -1;

                    foreach (
                        $request->file('images') as $index => $image
                    ) {
                        $path = $image->store(
                            'products',
                            'public'
                        );

                        $product->images()->create([
                            'image_path' => $path,

                            'is_primary' => $newPrimaryImageIndex !== null
                                    ? $index ===
                                        $newPrimaryImageIndex
                                    : (
                                        ! $hasPrimaryImage &&
                                        $index === 0
                                    ),

                            'sort_order' => $currentMaxOrder
                                + $index
                                + 1,
                        ]);
                    }
                }
            }
        );

        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                'Cập nhật sản phẩm thành công.'
            );
    }

    /**
     * Ẩn / hiện Product.
     */
    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                $product->is_active
                    ? 'Đã chuyển sản phẩm sang Active.'
                    : 'Đã chuyển sản phẩm sang Inactive.'
            );
    }

    /**
     * Đặt một ảnh làm ảnh chính.
     */
    public function setPrimaryImage(
        Product $product,
        int $image
    ) {
        $productImage = $product->images()
            ->findOrFail($image);
        DB::transaction(
            function () use (
                $product,
                $productImage
            ) {
                $product->images()->update([
                    'is_primary' => false,
                ]);
                $productImage->update([
                    'is_primary' => true,
                ]);
            }
        );

        return back()->with(
            'success',
            'Đã đặt ảnh đại diện sản phẩm.'
        );
    }

    /**
     * Thay file ảnh nhưng giữ nguyên vai trò
     * ảnh chính và thứ tự hiển thị.
     */
    public function replaceImage(
        Request $request,
        Product $product,
        int $image
    ) {
        $productImage = $product->images()
            ->findOrFail($image);
        $validated = $request->validate(
            [
                'image' => [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:4096',
                ],
            ],
            [
                'image.required' => 'Bạn phải chọn ảnh thay thế.',
                'image.image' => 'File thay thế phải là hình ảnh.',
                'image.mimes' => 'Ảnh chỉ chấp nhận JPG, JPEG, PNG hoặc WEBP.',
                'image.max' => 'Ảnh thay thế không được vượt quá 4MB.',
            ]
        );
        $oldPath = $productImage->image_path;
        $newPath = $validated['image']->store(
            'products',
            'public'
        );
        try {
            $productImage->update([
                'image_path' => $newPath,
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($newPath);
            throw $exception;
        }
        if (
            $oldPath &&
            Storage::disk('public')->exists($oldPath)
        ) {
            Storage::disk('public')->delete($oldPath);
        }

        return back()->with(
            'success',
            'Đã thay ảnh sản phẩm.'
        );
    }

    /**
     * Xóa một ảnh Product.
     */
    public function deleteImage(
        Product $product,
        int $image
    ) {
        $productImage = $product->images()
            ->findOrFail($image);
        $wasPrimary = $productImage->is_primary;
        DB::transaction(
            function () use (
                $product,
                $productImage,
                $wasPrimary
            ) {
                if (
                    Storage::disk('public')
                        ->exists(
                            $productImage->image_path
                        )
                ) {
                    Storage::disk('public')
                        ->delete(
                            $productImage->image_path
                        );
                }
                $productImage->delete();
                /*
                 * Nếu xóa ảnh chính,
                 * lấy ảnh đầu tiên còn lại
                 * làm ảnh chính.
                 */
                if ($wasPrimary) {
                    $nextImage =
                        $product->images()
                            ->orderBy('sort_order')
                            ->first();
                    if ($nextImage) {
                        $nextImage->update([
                            'is_primary' => true,
                        ]);
                    }
                }
            }
        );

        return back()->with(
            'success',
            'Đã xóa ảnh sản phẩm.'
        );
    }

    /**
     * Xóa Product.
     */
    public function destroy(Product $product)
    {
        /*
         * Nếu Product đã xuất hiện trong Order
         * thì không xóa cứng.
         */
        if ($product->orderDetails()->exists()) {
            $product->update([
                'is_active' => false,
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with(
                    'error',
                    'Sản phẩm đã phát sinh đơn hàng nên không thể xóa. Sản phẩm đã được chuyển sang trạng thái không hoạt động.'
                );
        }
        /*
         * Nếu Product đang có dữ liệu nghiệp vụ
         * liên quan thì cũng không xóa cứng.
         */
        if (
            $product->wishlistItems()->exists() ||
            $product->cartItems()->exists() ||
            $product->warranties()->exists() ||
            $product->stockHistories()->exists() ||
            $product->careRules()->exists() ||
            $product->reviews()->exists() ||
            $product->topQuizSessions()->exists()
        ) {
            $product->update([
                'is_active' => false,
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with(
                    'error',
                    'Sản phẩm đang có dữ liệu liên quan nên không thể xóa cứng. Sản phẩm đã được chuyển sang trạng thái không hoạt động.'
                );
        }
        DB::transaction(
            function () use ($product) {
                foreach ($product->images as $image) {
                    if (
                        Storage::disk('public')
                            ->exists(
                                $image->image_path
                            )
                    ) {
                        Storage::disk('public')
                            ->delete(
                                $image->image_path
                            );
                    }
                }
                $product->delete();
            }
        );

        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                'Xóa sản phẩm thành công.'
            );
    }

    private function resolveStudioProductType(int $categoryId): ?string
    {
        $slug = Category::query()
            ->whereKey($categoryId)
            ->value('slug');

        return [
            'binh' => 'container',
            'cay' => 'plant',
            'reu' => 'moss',
            'nen' => 'substrate',
            'da' => 'stone',
            'phu-kien' => 'accessory',
        ][$slug] ?? null;
    }

    /**
     * Validation dùng chung
     * cho Store + Update.
     *  */
    private function validateProduct(
        Request $request,
        ?Product $product = null
    ): array {
        $validated = $request->validate(
            [
                'category_id' => [
                    'required',
                    'integer',
                    'exists:categories,id',
                ],
                'name' => [
                    'required',
                    'string',
                    'min:5',
                    'max:150',
                ],
                'sku' => [
                    'required',
                    'string',
                    'max:50',
                    'regex:/^[A-Za-z0-9-]+$/',
                    Rule::unique(
                        'products',
                        'sku'
                    )->ignore($product?->id),
                ],
                'ecosystem_type' => [
                    'required',
                    Rule::in([
                        'closed',
                        'open',
                        'dry',
                    ]),
                ],
                'list_price' => [
                    'required',
                    'numeric',
                    'gt:0',
                ],
                'sale_price' => [
                    'nullable',
                    'numeric',
                    'gte:0',
                    'lt:list_price',
                ],
                'size' => [
                    'nullable',
                    'string',
                    'max:50',
                ],
                'care_level' => [
                    'required',
                    Rule::in([
                        'easy',
                        'medium',
                        'high',
                    ]),
                ],
                'light_requirement' => [
                    'nullable',
                    'string',
                    'max:150',
                ],
                /*
                 * Sản phẩm thuộc một trong 6 danh mục Studio
                 * bắt buộc có chu kỳ chăm sóc.
                 */
                'care_cycle_days' => [
                    Rule::requiredIf(
                        fn () => $this->resolveStudioProductType(
                            (int) $request->input(
                                'category_id'
                            )
                        ) !== null
                    ),
                    'nullable',
                    'integer',
                    'min:1',
                ],
                'description' => [
                    'nullable',
                    'string',
                ],
                'concept_story' => [
                    'nullable',
                    'string',
                ],
                'care_instructions' => [
                    'nullable',
                    'string',
                ],
                'stock_quantity' => [
                    'required',
                    'integer',
                    'min:0',
                ],
                'images' => [
                    'nullable',
                    'array',
                    'max:8',
                ],
                'images.*' => [
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:4096',
                ],
                'primary_image_index' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:7',
                ],
            ],
            [
                'category_id.required' => 'Bạn phải chọn danh mục.',
                'category_id.exists' => 'Danh mục không tồn tại.',
                'name.required' => 'Tên sản phẩm không được để trống.',
                'name.min' => 'Tên sản phẩm phải có ít nhất 5 ký tự.',
                'name.max' => 'Tên sản phẩm không được vượt quá 150 ký tự.',
                'sku.required' => 'SKU không được để trống.',
                'sku.unique' => 'SKU đã tồn tại.',
                'sku.regex' => 'SKU chỉ được chứa chữ, số và dấu gạch ngang (-).',
                'ecosystem_type.required' => 'Bạn phải chọn loại hệ sinh thái.',
                'ecosystem_type.in' => 'Loại hệ sinh thái không hợp lệ.',
                'list_price.required' => 'Giá niêm yết không được để trống.',
                'list_price.numeric' => 'Giá niêm yết phải là số.',
                'list_price.gt' => 'Giá niêm yết phải lớn hơn 0.',
                'sale_price.numeric' => 'Giá khuyến mãi phải là số.',
                'sale_price.gte' => 'Giá khuyến mãi không được âm.',
                'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá niêm yết.',
                'stock_quantity.required' => 'Tồn kho không được để trống.',
                'stock_quantity.integer' => 'Tồn kho phải là số nguyên.',
                'stock_quantity.min' => 'Tồn kho không được âm.',
                'care_level.required' => 'Bạn phải chọn mức độ chăm sóc.',
                'care_cycle_days.required' => 'Sản phẩm dùng trong Studio phải có chu kỳ chăm sóc.',
                'care_cycle_days.integer' => 'Chu kỳ chăm sóc phải là số nguyên.',
                'care_cycle_days.min' => 'Chu kỳ chăm sóc phải từ 1 ngày trở lên.',
                'images.max' => 'Mỗi lần chỉ được tải tối đa 8 ảnh.',
                'images.*.image' => 'File tải lên phải là hình ảnh.',
                'images.*.mimes' => 'Ảnh chỉ chấp nhận JPG, JPEG, PNG hoặc WEBP.',
                'images.*.max' => 'Mỗi ảnh không được vượt quá 4MB.',
                'primary_image_index.integer' => 'Ảnh chính được chọn không hợp lệ.',
            ]
        );
        $newImages = $request->file(
            'images',
            []
        );
        $newImageCount = count($newImages);
        if (
            $product &&
            $product->images()->count()
                + $newImageCount > 8
        ) {
            throw ValidationException::withMessages([
                'images' => 'Mỗi sản phẩm chỉ được có tối đa 8 ảnh. '
                    .'Hãy xóa hoặc thay thế ảnh hiện có trước khi thêm ảnh mới.',
            ]);
        }
        if (
            array_key_exists(
                'primary_image_index',
                $validated
            ) &&
            (
                $newImageCount === 0 ||
                (int) $validated[
                    'primary_image_index'
                ] >= $newImageCount
            )
        ) {
            throw ValidationException::withMessages([
                'primary_image_index' => 'Ảnh chính phải thuộc các ảnh mới bạn vừa chọn.',
            ]);
        }

        return $validated;
    }
}
