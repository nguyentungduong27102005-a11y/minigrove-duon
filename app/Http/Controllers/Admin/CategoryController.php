<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Danh sách Category.
     */
    public function index(Request $request)
    {
        $keyword = trim((string) $request->keyword);

        $categories = Category::withCount('products')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('slug', 'like', '%' . $keyword . '%');
                });
            })
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.categories.index',
            compact('categories', 'keyword')
        );
    }

    /**
     * Form thêm Category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Lưu Category mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'display_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ], [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.max' => 'Tên danh mục không được vượt quá 100 ký tự.',

            'image.image' => 'File tải lên phải là hình ảnh.',
            'image.mimes' => 'Ảnh chỉ chấp nhận JPG, JPEG, PNG hoặc WEBP.',
            'image.max' => 'Dung lượng ảnh không được vượt quá 5MB.',

            'display_order.integer' => 'Thứ tự hiển thị phải là số nguyên.',
            'display_order.min' => 'Thứ tự hiển thị không được âm.',
        ]);

        $slug = $this->createUniqueSlug(
            $validated['name']
        );

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('categories', 'public');
        }

        Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,

            'is_active' => $request->boolean('is_active'),

            'display_order' => $validated['display_order'] ?? 0,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'Thêm danh mục thành công.'
            );
    }

    /**
     * Form sửa Category.
     */
    public function edit(Category $category)
    {
        return view(
            'admin.categories.edit',
            compact('category')
        );
    }

    /**
     * Cập nhật Category.
     */
    public function update(
        Request $request,
        Category $category
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'display_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ], [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.max' => 'Tên danh mục không được vượt quá 100 ký tự.',

            'image.image' => 'File tải lên phải là hình ảnh.',
            'image.mimes' => 'Ảnh chỉ chấp nhận JPG, JPEG, PNG hoặc WEBP.',
            'image.max' => 'Dung lượng ảnh không được vượt quá 5MB.',

            'display_order.integer' => 'Thứ tự hiển thị phải là số nguyên.',
            'display_order.min' => 'Thứ tự hiển thị không được âm.',
        ]);

        $slug = $this->createUniqueSlug(
            $validated['name'],
            $category->id
        );

        $imagePath = $category->image;

        /*
         * Nếu upload ảnh mới:
         * xóa ảnh cũ và lưu ảnh mới.
         */
        if ($request->hasFile('image')) {

            if (
                $category->image &&
                Storage::disk('public')
                    ->exists($category->image)
            ) {
                Storage::disk('public')
                    ->delete($category->image);
            }

            $imagePath = $request->file('image')
                ->store('categories', 'public');
        }

        $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,

            'is_active' => $request->boolean('is_active'),

            'display_order' => $validated['display_order'] ?? 0,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'Cập nhật danh mục thành công.'
            );
    }

    /**
     * Xóa Category.
     */
    public function destroy(Category $category)
    {
        /*
         * Không xóa Category nếu đã có Product.
         */
        if ($category->products()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with(
                    'error',
                    'Không thể xóa danh mục vì đang có sản phẩm thuộc danh mục này.'
                );
        }

        /*
         * Xóa ảnh trong Storage nếu có.
         */
        if (
            $category->image &&
            Storage::disk('public')
                ->exists($category->image)
        ) {
            Storage::disk('public')
                ->delete($category->image);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'Xóa danh mục thành công.'
            );
    }

    /**
     * Ẩn / hiện Category.
     */
    public function toggleStatus(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active,
        ]);

        return redirect()
    ->route('admin.categories.index')
    ->with(
        'success',
        $category->is_active
            ? 'Đã chuyển danh mục sang Active.'
            : 'Đã chuyển danh mục sang Inactive.'
    );
    }

    /**
     * Tạo slug không trùng.
     */
    private function createUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'category';
        }

        $slug = $baseSlug;
        $number = 1;

        while (
            Category::where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) =>
                        $query->where('id', '!=', $ignoreId)
                )
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $number;
            $number++;
        }

        return $slug;
    }
}