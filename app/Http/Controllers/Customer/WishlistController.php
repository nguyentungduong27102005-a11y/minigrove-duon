<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product; // Lưu thông tin sản phẩm
use App\Models\Wishlist; // Danh sách yêu thích của một người dùng
use App\Models\WishlistItem; // Các sản phẩm nằm trong Wishlist
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm yêu thích.
     */
    public function index(Request $request): View // Nhận yêu cầu từ người dùng. Trả về một trang giao diện, nên kiểu trả về là View.
    {
        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id, // Lấy ID người dùng đang đăng nhập,Tìm Wishlist có user_id đó.Nếu chưa có, Laravel tự tạo một Wishlist mới.
        ]);

        $wishlist->load([ // lấy sản  phẩm và hình ảnh
            'items.product.images',
        ]);

        return view('wishlist.index', [ // trả dữ liệu ra giao diện wishlist.index
            'wishlist' => $wishlist, // truyền dữ liệu wishlist vào giao diện
        ]);
    }

    /**
     * Thêm sản phẩm vào yêu thích.
     */
    public function store(
        Request $request,
        Product $product
    ): RedirectResponse|JsonResponse {

        $wishlist = Wishlist::firstOrCreate([ // Nếu người dùng chưa có Wishlist thì hệ thống tạo mới.
            'user_id' => $request->user()->id,
        ]);

        WishlistItem::firstOrCreate([ // Hệ thống tìm cặp dữ liệu: wishlist_id + product_id
            'wishlist_id' => $wishlist->id, // Nếu đã tồn tại thì giữ nguyên. Nếu chưa có thì tạo mới.
            'product_id' => $product->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true, // thao tác thành công.
                'wishlisted' => true, // sản phẩm đang được yêu thích.
                'count' => $wishlist // tổng số sản phẩm yêu thích.
                    ->items()
                    ->count(),
            ]);
        }

        return back()->with(
            'success',
            'Đã thêm sản phẩm vào danh sách yêu thích.'
        );
    }

    /**
     * Xóa sản phẩm khỏi yêu thích.
     */
    public function destroy(
        Request $request,
        Product $product
    ): RedirectResponse|JsonResponse {

        $wishlist = Wishlist::where( // Tìm Wishlist của đúng người đang đăng nhập
            'user_id',
            $request->user()->id
        )->first();

        if ($wishlist) { // Xóa đúng sản phẩm
            WishlistItem::where(
                'wishlist_id', // Bản ghi chỉ bị xóa khi đồng thời đúng:
                $wishlist->id // wishlist_id của người dùng và product_id được chọn
            )
                ->where(
                    'product_id',
                    $product->id
                )
                ->delete();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'wishlisted' => false, // sản phẩm không còn trong Wishlist.
                'count' => $wishlist // số sản phẩm còn lại.
                    ? $wishlist->items()->count() // Nếu Wishlist không tồn tại thì count bằng 0.
                    : 0,
            ]);
        }

        return back()->with(
            'success',
            'Đã xóa sản phẩm khỏi danh sách yêu thích.'
        );
    }
}
