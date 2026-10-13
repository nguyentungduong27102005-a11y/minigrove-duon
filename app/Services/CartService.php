<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CustomDesign;
use App\Models\Product;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function __construct(
        private CustomDesignService $customDesignService
    ) {
    }

    /**
     * Lấy Cart hiện tại của Customer.
     * Nếu chưa có thì tự tạo.
     */
    public function getOrCreateCart(User $user): Cart
    {
        return Cart::firstOrCreate([
            'user_id' => $user->id,
        ]);
    }


    /**
     * Thêm Product vào giỏ hàng.
     */
    public function addProduct(
        User $user,
        Product $product,
        int $quantity = 1
    ): CartItem {

        /*
         * Quantity phải >= 1.
         */
        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' =>
                    'Số lượng sản phẩm phải từ 1 trở lên.',
            ]);
        }


        /*
         * Product phải Active.
         */
        if (!$product->is_active) {
            throw ValidationException::withMessages([
                'product' =>
                    'Sản phẩm hiện không còn được bán.',
            ]);
        }


        /*
         * Product phải còn hàng.
         */
        if ($product->stock_quantity <= 0) {
            throw ValidationException::withMessages([
                'product' =>
                    'Sản phẩm hiện đã hết hàng.',
            ]);
        }


        $cart = $this->getOrCreateCart($user);


        /*
         * Kiểm tra Product đã có trong Cart chưa.
         */
        $cartItem = $cart->items()
            ->where('item_type', 'PRODUCT')
            ->where('product_id', $product->id)
            ->first();


        /*
         * Nếu đã có:
         * tăng quantity thay vì tạo dòng mới.
         */
        if ($cartItem) {

            $newQuantity =
                $cartItem->quantity + $quantity;


            /*
             * Không được vượt Stock.
             */
            if (
                $newQuantity >
                $product->stock_quantity
            ) {
                throw ValidationException::withMessages([
                    'quantity' =>
                        'Số lượng trong giỏ vượt quá tồn kho hiện tại.',
                ]);
            }


            $cartItem->update([
                'quantity' => $newQuantity,
            ]);


            return $cartItem->fresh();
        }


        /*
         * Product chưa có trong Cart.
         */
        if (
            $quantity >
            $product->stock_quantity
        ) {
            throw ValidationException::withMessages([
                'quantity' =>
                    'Số lượng yêu cầu vượt quá tồn kho hiện tại.',
            ]);
        }


        return $cart->items()->create([
            'item_type' => 'PRODUCT',

            'product_id' =>
                $product->id,

            'custom_design_id' =>
                null,

            'quantity' =>
                $quantity,
        ]);
    }

    /**
     * Thêm một Custom Design vào Cart.
     *
     * Giá không lưu trong CartItem. Mỗi lần Cart/Checkout đọc thiết kế,
     * hệ thống sẽ tính lại theo giá Product hiện hành.
     */
    public function addCustomDesign(
        User $user,
        CustomDesign $design
    ): CartItem {
        if ($design->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'design' => 'Bạn không có quyền sử dụng thiết kế này.',
            ]);
        }

        if ($design->status !== 'saved') {
            throw ValidationException::withMessages([
                'design' => 'Thiết kế hiện không thể thêm vào giỏ hàng.',
            ]);
        }

        $this->customDesignService->recalculate($design);

        $cart = $this->getOrCreateCart($user);

        $cartItem = $cart->items()
            ->where('item_type', 'CUSTOM_DESIGN')
            ->where('custom_design_id', $design->id)
            ->first();

        if ($cartItem) {
            return $cartItem;
        }

        return $cart->items()->create([
            'item_type' => 'CUSTOM_DESIGN',
            'product_id' => null,
            'custom_design_id' => $design->id,
            'quantity' => 1,
        ]);
    }


    /**
     * Cập nhật quantity của Product trong Cart.
     */
    public function updateProductQuantity(
        User $user,
        CartItem $cartItem,
        int $quantity
    ): CartItem {

        $this->ensureOwnership(
            $user,
            $cartItem
        );


        if (
            $cartItem->item_type
            !== 'PRODUCT'
        ) {
            throw ValidationException::withMessages([
                'cart' =>
                    'Dòng giỏ hàng không phải sản phẩm.',
            ]);
        }


        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' =>
                    'Số lượng sản phẩm phải từ 1 trở lên.',
            ]);
        }


        $product = Product::find(
            $cartItem->product_id
        );


        if (
            !$product ||
            !$product->is_active
        ) {
            throw ValidationException::withMessages([
                'product' =>
                    'Sản phẩm hiện không còn được bán.',
            ]);
        }


        if (
            $quantity >
            $product->stock_quantity
        ) {
            throw ValidationException::withMessages([
                'quantity' =>
                    'Số lượng yêu cầu vượt quá tồn kho hiện tại.',
            ]);
        }


        $cartItem->update([
            'quantity' => $quantity,
        ]);


        return $cartItem->fresh();
    }


    /**
     * Xóa một dòng khỏi Cart.
     */
    public function removeItem(
        User $user,
        CartItem $cartItem
    ): void {

        $this->ensureOwnership(
            $user,
            $cartItem
        );


        $cartItem->delete();
    }


    /**
     * Kiểm tra CartItem có thật sự
     * thuộc Cart của User hiện tại không.
     */
    private function ensureOwnership(
        User $user,
        CartItem $cartItem
    ): void {

        $belongsToUser = Cart::where(
            'id',
            $cartItem->cart_id
        )
            ->where(
                'user_id',
                $user->id
            )
            ->exists();


        if (!$belongsToUser) {
            throw ValidationException::withMessages([
                'cart' =>
                    'Bạn không có quyền thao tác giỏ hàng này.',
            ]);
        }
    }
}