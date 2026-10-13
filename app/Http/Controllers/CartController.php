<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Wishlist;
use App\Services\CartService;
use App\Services\CustomDesignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private CustomDesignService $customDesignService
    ) {
    }

    public function index(Request $request): View
    {
        $cart = $this->cartService
            ->getOrCreateCart($request->user());

        $cart->load([
            'items.product.category',
            'items.product.images' => function ($query) {
                $query
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order')
                    ->orderBy('id');
            },
            'items.customDesign.items.product',
        ]);

        $total = 0;

        foreach ($cart->items as $item) {
            if ($item->item_type === 'PRODUCT' && $item->product) {
                $unitPrice = $item->product->sale_price !== null
                    ? (float) $item->product->sale_price //Thành tiền một mục = Đơn giá × Số lượng
                    : (float) $item->product->list_price; //Tổng giỏ hàng = Tổng thành tiền của tất cả mục

                $subtotal = $unitPrice * $item->quantity; //tính tiền
            } elseif (
                $item->item_type === 'CUSTOM_DESIGN'
                && $item->customDesign
            ) {
                try {
                    $recalculated = $this->customDesignService// tính tiền thiết kế
                        ->recalculate($item->customDesign);

                    $unitPrice =
                        (float) $recalculated['pricing']['estimated_price'];

                    $subtotal = $unitPrice * $item->quantity;
                    $item->availability_error = null;
                } catch (\Throwable $exception) {
                    $unitPrice = 0;
                    $subtotal = 0;
                    $item->availability_error =
                        'Thiết kế cần được điều chỉnh trước khi thanh toán.';
                }
            } else {
                continue;
            }

            $item->unit_price = $unitPrice;
            $item->subtotal = $subtotal;

            $total += $subtotal;
        }

        $productItems = $cart->items
            ->where('item_type', 'PRODUCT')
            ->values();

        $designItems = $cart->items
            ->where('item_type', 'CUSTOM_DESIGN')
            ->values();

        $wishlist = Wishlist::query()
            ->where('user_id', $request->user()->id)
            ->first();

        $wishlistProductIds = $wishlist
            ? $wishlist->items()
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->all()
            : [];

        return view('cart.index', compact(
            'cart',
            'total',
            'productItems',
            'designItems',
            'wishlistProductIds'
        ));
    }

    public function add(
        Request $request,
        Product $product
    ): RedirectResponse {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $this->cartService->addProduct(
            $request->user(),
            $product,
            (int) ($validated['quantity'] ?? 1)
        );

        return back()->with(
            'success',
            'Đã thêm sản phẩm vào giỏ hàng.'
        );
    }

    public function update(
        Request $request,
        CartItem $cartItem
    ) {
        if ($cartItem->item_type !== 'PRODUCT') {
            return back()->with(
                'error',
                'Thiết kế MiniGrove Studio luôn có số lượng 1 trong giỏ hàng.'
            );
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $updatedItem = $this->cartService
            ->updateProductQuantity(
                $request->user(),
                $cartItem,
                (int) $validated['quantity']
            );

        $product = Product::findOrFail(
            $updatedItem->product_id
        );

        $unitPrice = $product->sale_price !== null
            ? (float) $product->sale_price
            : (float) $product->list_price;

        $subtotal =
            $unitPrice * $updatedItem->quantity;

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật giỏ hàng.',
                'item' => [
                    'id' => $updatedItem->id,
                    'quantity' => $updatedItem->quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'stock_quantity' => $product->stock_quantity,
                ],
            ]);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã cập nhật giỏ hàng.');
    }

    public function destroy(
        Request $request,
        CartItem $cartItem
    ): RedirectResponse {
        $this->cartService->removeItem(
            $request->user(),
            $cartItem
        );

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Đã xóa mục khỏi giỏ hàng.'
            );
    }

    public function destroySelected(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'selected_items' => [
                'required',
                'array',
                'min:1',
            ],
            'selected_items.*' => [
                'required',
                'integer',
            ],
        ]);

        $items = CartItem::query()
            ->whereIn(
                'id',
                $validated['selected_items']
            )
            ->get();

        foreach ($items as $item) {
            $this->cartService->removeItem(
                $request->user(),
                $item
            );
        }

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Đã xóa các mục được chọn khỏi giỏ hàng.'
            );
    }
}
