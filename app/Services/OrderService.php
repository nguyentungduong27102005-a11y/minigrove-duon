<?php

namespace App\Services;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\CustomDesign;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Models\PaymentSetting;
use App\Services\GHNService;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    protected GHNService $ghnService;

    public function __construct(
        GHNService $ghnService,
        private CustomDesignService $customDesignService
    ) {
        $this->ghnService = $ghnService;
    }
    /**
     * Tạo Order thật.
     *
     * Toàn bộ nghiệp vụ được đặt
     * trong một Database Transaction.
     */
    public function placeOrder(
        User $user,
        int $addressId,
        string $paymentMethod,
        ?string $note = null
    ): Order {

        return DB::transaction(
            function () use (
                $user,
                $addressId,
                $paymentMethod,
                $note
            ) {

                /*
                |--------------------------------------------------------------------------
                | 1. KIỂM TRA ĐỊA CHỈ
                |--------------------------------------------------------------------------
                |
                | Chỉ được sử dụng Address
                | thuộc chính Customer hiện tại.
                |
                */

                $address = Address::query()
                    ->where(
                        'id',
                        $addressId
                    )
                    ->where(
                        'user_id',
                        $user->id
                    )
                    ->first();


                if (!$address) {

                    throw ValidationException::withMessages([
                        'address' =>
                            'Địa chỉ nhận hàng không hợp lệ.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | 2. KIỂM TRA PAYMENT METHOD
                |--------------------------------------------------------------------------
                */

                $paymentMethod =
                    strtoupper(
                        trim(
                            $paymentMethod
                        )
                    );


                if (
                    !in_array(
                        $paymentMethod,
                        [
                            'COD',
                            'QR',
                            'ONEPAY',
                        ],
                        true
                    )
                ) {

                    throw ValidationException::withMessages([
                        'payment_method' =>
                            'Phương thức thanh toán không hợp lệ.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | 3. LẤY CHECKOUT MODE
                |--------------------------------------------------------------------------
                */

                $mode =
                    session(
                        'checkout.mode'
                    );


                $orderItems =
                    collect();


                $subtotal = 0;


                /*
                |--------------------------------------------------------------------------
                | 4A. CHECKOUT TỪ CART
                |--------------------------------------------------------------------------
                */

                if ($mode === 'cart') {

                    $cartItemIds =
                        session(
                            'checkout.cart_item_ids',
                            []
                        );


                    if (
                        empty(
                            $cartItemIds
                        )
                    ) {

                        throw ValidationException::withMessages([
                            'checkout' =>
                                'Không có sản phẩm nào được chọn để đặt hàng.',
                        ]);
                    }


                    /*
                     * Chỉ lấy CartItem thuộc User.
                     */
                    $cartItems =
                        CartItem::query()
                            ->whereIn(
                                'id',
                                $cartItemIds
                            )
                            ->whereHas(
                                'cart',
                                function ($query) use ($user) {

                                    $query->where(
                                        'user_id',
                                        $user->id
                                    );
                                }
                            )
                            ->with([
                                'customDesign.items.product',
                            ])
                            ->get();


                    /*
                     * Không cho trường hợp
                     * có ID bị mất / không thuộc User.
                     */
                    $expectedCount =
                        count(
                            array_unique(
                                $cartItemIds
                            )
                        );


                    if (
                        $cartItems->count()
                        !== $expectedCount
                    ) {

                        throw ValidationException::withMessages([
                            'checkout' =>
                                'Giỏ hàng đã thay đổi. Vui lòng kiểm tra lại.',
                        ]);
                    }


                    foreach (
                        $cartItems as $cartItem
                    ) {
                        if ($cartItem->item_type === 'PRODUCT') {
                            $product =
                                Product::query()
                                    ->with('category')
                                    ->whereKey(
                                        $cartItem->product_id
                                    )
                                    ->lockForUpdate()
                                    ->first();

                            $this->validateProduct(
                                $product,
                                $cartItem->quantity
                            );

                            $unitPrice =
                                $this->getProductPrice($product);

                            $lineSubtotal =
                                $unitPrice
                                * $cartItem->quantity;

                            $subtotal += $lineSubtotal;

                            $orderItems->push([
                                'item_type' => 'PRODUCT',
                                'cart_item_id' => $cartItem->id,
                                'product' => $product,
                                'custom_design' => null,
                                'design_products' => collect(),
                                'quantity' => $cartItem->quantity,
                                'unit_price' => $unitPrice,
                                'subtotal' => $lineSubtotal,
                                'weight' =>
                                    (int) ($product->weight ?? 500)
                                    * (int) $cartItem->quantity,
                            ]);
                        } elseif (
                            $cartItem->item_type === 'CUSTOM_DESIGN'
                            && $cartItem->customDesign
                        ) {
                            /** @var CustomDesign $design */
                            $design = $cartItem->customDesign;

                            if ($design->user_id !== $user->id) {
                                throw ValidationException::withMessages([
                                    'checkout' =>
                                        'Thiết kế không thuộc tài khoản hiện tại.',
                                ]);
                            }

                            $recalculated =
                                $this->customDesignService
                                    ->recalculate($design);

                            $designProducts = collect();
                            $weight = 0;

                            foreach (
                                $recalculated['pricing']['items']
                                as $designItem
                            ) {
                                $lockedProduct =
                                    Product::query()
                                        ->whereKey(
                                            $designItem['product_id']
                                        )
                                        ->lockForUpdate()
                                        ->first();

                                $requiredQuantity =
                                    (int) $designItem['quantity'];

                                $this->validateProduct(
                                    $lockedProduct,
                                    $requiredQuantity
                                );

                                $designProducts->push([
                                    'product' => $lockedProduct,
                                    'quantity' => $requiredQuantity,
                                    'unit_price' =>
                                        (float) $designItem['unit_price'],
                                    'subtotal' =>
                                        (float) $designItem['subtotal'],
                                    'product_type' =>
                                        $designItem['product_type'] ?? null,
                                ]);

                                $weight +=
                                    (int) ($lockedProduct->weight ?? 0)
                                    * $requiredQuantity;
                            }

                            $unitPrice =
                                (float) $recalculated['pricing']['estimated_price'];

                            $lineSubtotal = $unitPrice;
                            $subtotal += $lineSubtotal;

                            $orderItems->push([
                                'item_type' => 'CUSTOM_DESIGN',
                                'cart_item_id' => $cartItem->id,
                                'product' => null,
                                'custom_design' => $design,
                                'design_products' => $designProducts,
                                'quantity' => 1,
                                'unit_price' => $unitPrice,
                                'subtotal' => $lineSubtotal,
                                'weight' => max(1, $weight),
                                'purchase_pricing' =>
                                    $recalculated['pricing'],
                                'purchase_compatibility' =>
                                    $recalculated['compatibility'],
                            ]);
                        } else {
                            throw ValidationException::withMessages([
                                'checkout' =>
                                    'Có mục trong giỏ hàng không hợp lệ.',
                            ]);
                        }
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | 4B. MUA NGAY
                |--------------------------------------------------------------------------
                */

                elseif (
                    $mode === 'buy_now'
                ) {

                    $productId =
                        session(
                            'checkout.product_id'
                        );


                    $quantity =
                        (int)
                        session(
                            'checkout.quantity',
                            1
                        );


                    if (!$productId) {

                        throw ValidationException::withMessages([
                            'checkout' =>
                                'Không tìm thấy sản phẩm Mua ngay.',
                        ]);
                    }


                    $product =
                        Product::query()
                            ->with('category')
                            ->whereKey(
                                $productId
                            )
                            ->lockForUpdate()
                            ->first();


                    $this->validateProduct(
                        $product,
                        $quantity
                    );


                    $unitPrice =
                        $this->getProductPrice(
                            $product
                        );


                    $lineSubtotal =
                        $unitPrice
                        * $quantity;


                    $subtotal =
                        $lineSubtotal;


                    $orderItems->push(
                        [
                            'item_type' =>
                                'PRODUCT',

                            'cart_item_id' =>
                                null,

                            'product' =>
                                $product,

                            'custom_design' =>
                                null,

                            'design_products' =>
                                collect(),

                            'quantity' =>
                                $quantity,

                            'unit_price' =>
                                $unitPrice,

                            'subtotal' =>
                                $lineSubtotal,

                            'weight' =>
                                (int) ($product->weight ?? 500)
                                * $quantity,
                        ]
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | MODE KHÔNG HỢP LỆ
                |--------------------------------------------------------------------------
                */

                else {

                    throw ValidationException::withMessages([
                        'checkout' =>
                            'Phiên thanh toán không hợp lệ.',
                    ]);
                }


                if (
                    $orderItems->isEmpty()
                ) {

                    throw ValidationException::withMessages([
                        'checkout' =>
                            'Không có sản phẩm để đặt hàng.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | 5. SHIPPING FEE - GHN
                |--------------------------------------------------------------------------
                |
                | Phải tính phí vận chuyển từ GHN trước khi tính Voucher Shipping.
                |
                */

                if (
                    !$address->ghn_district_id ||
                    !$address->ghn_ward_code
                ) {
                    throw ValidationException::withMessages([
                        'address' =>
                            'Địa chỉ chưa được cấu hình mã GHN. Vui lòng cập nhật lại địa chỉ.',
                    ]);
                }

                /*
                 * Tính tổng trọng lượng đơn hàng.
                 * GHN sử dụng đơn vị gram.
                 */
                $weight = 0;

                foreach ($orderItems as $item) {
                    $weight +=
                        (int) ($item['weight'] ?? 500);
                }

                $weight = max(1, $weight);

                /*
                 * Thông tin kho/shop MiniGrove trên GHN.
                 */
                $shippingData = [
                    'service_type_id' => 2,
                    'from_district_id' => 3440,
                    'from_ward_code' => '13005',
                    'to_district_id' =>
                        (int) $address->ghn_district_id,
                    'to_ward_code' =>
                        $address->ghn_ward_code,
                    'weight' => $weight,
                ];

                /*
                 * Gọi GHN để lấy phí vận chuyển.
                 */
                $shippingResult =
                    $this->ghnService
                        ->calculateFee($shippingData);

                $shippingFee =
                    (float) ($shippingResult['total'] ?? 0);


                /*
                |--------------------------------------------------------------------------
                | 6. VOUCHERS (ORDER + SHIPPING)
                |--------------------------------------------------------------------------
                */

                $orderVoucher = null;
                $discountAmount = 0.0;
                $orderVoucherCode =
                    session('checkout.order_voucher_code')
                    ?: session('checkout.voucher_code');

                if ($orderVoucherCode) {
                    $orderVoucher =
                        Voucher::query()
                            ->whereRaw(
                                'UPPER(code) = ?',
                                [
                                    strtoupper(
                                        trim($orderVoucherCode)
                                    ),
                                ]
                            )
                            ->lockForUpdate()
                            ->first();

                    if (!$orderVoucher) {
                        throw ValidationException::withMessages([
                            'voucher' =>
                                'Voucher giảm giá đơn hàng không còn tồn tại.',
                        ]);
                    }

                    $discountAmount =
                        $this->validateAndCalculateVoucher(
                            $user,
                            $orderVoucher,
                            $subtotal,
                            $shippingFee,
                            Voucher::SCOPE_ORDER
                        );
                }

                $shippingVoucher = null;
                $shippingDiscountAmount = 0.0;
                $shippingVoucherCode =
                    session('checkout.shipping_voucher_code');

                if ($shippingVoucherCode) {
                    $shippingVoucher =
                        Voucher::query()
                            ->whereRaw(
                                'UPPER(code) = ?',
                                [
                                    strtoupper(
                                        trim($shippingVoucherCode)
                                    ),
                                ]
                            )
                            ->lockForUpdate()
                            ->first();

                    if (!$shippingVoucher) {
                        throw ValidationException::withMessages([
                            'voucher' =>
                                'Voucher giảm phí vận chuyển không còn tồn tại.',
                        ]);
                    }

                    $shippingDiscountAmount =
                        $this->validateAndCalculateVoucher(
                            $user,
                            $shippingVoucher,
                            $subtotal,
                            $shippingFee,
                            Voucher::SCOPE_SHIPPING
                        );
                }


                /*
                |--------------------------------------------------------------------------
                | 7. TOTAL
                |--------------------------------------------------------------------------
                */

                $totalAmount =
                    max(
                        0,
                        $subtotal
                        - $discountAmount
                    )
                    + max(
                        0,
                        $shippingFee
                        - $shippingDiscountAmount
                    );


                /*
                |--------------------------------------------------------------------------
                | 8. SNAPSHOT ĐỊA CHỈ
                |--------------------------------------------------------------------------
                |
                | Sau này Customer thay Address,
                | Order cũ vẫn giữ thông tin cũ.
                |
                */

                $shippingAddress =
                    $address->address_detail
                    . ', '
                    . $address->ward
                    . ', '
                    . $address->district
                    . ', '
                    . $address->province;


                /*
                |--------------------------------------------------------------------------
                | 9. TẠO ORDER
                |--------------------------------------------------------------------------
                */

                $order =
                    Order::create(
                        [
                            'order_code' =>
                                $this
                                    ->generateOrderCode(),

                            'user_id' =>
                                $user->id,

                            'customer_name' =>
                                $address
                                    ->receiver_name,

                            'phone' =>
                                $address
                                    ->receiver_phone,

                            'email' =>
                                $user->email,

                            'shipping_address' =>
                                $shippingAddress,

                            'subtotal' =>
                                $subtotal,

                            'discount_amount' =>
                                $discountAmount,

                            'shipping_fee' =>
                                $shippingFee,

                            'shipping_discount_amount' =>
                                $shippingDiscountAmount,

                            'total_amount' =>
                                $totalAmount,

                            'payment_method' =>
                                $paymentMethod,

                            'payment_status' =>
                                'pending',

                            'order_status' =>
                                'pending',

                            'note' =>
                                $note
                                    ? trim($note)
                                    : null,
                        ]
                    );


                /*
                |--------------------------------------------------------------------------
                | 10. ORDER DETAIL + TRỪ STOCK
                |--------------------------------------------------------------------------
                */

                foreach (
                    $orderItems as $item
                ) {
                    if ($item['item_type'] === 'PRODUCT') {
                        /** @var Product $product */
                        $product = $item['product'];
                        $quantity = (int) $item['quantity'];

                        $quantityBefore =
                            (int) $product->stock_quantity;
                        $quantityAfter =
                            $quantityBefore - $quantity;

                        if ($quantityAfter < 0) {
                            throw ValidationException::withMessages([
                                'stock' =>
                                    'Sản phẩm '
                                    . $product->name
                                    . ' không còn đủ tồn kho.',
                            ]);
                        }

                        OrderDetail::create([
                            'order_id' => $order->id,
                            'item_type' => 'PRODUCT',
                            'product_id' => $product->id,
                            'custom_design_id' => null,
                            'item_name' => $product->name,
                            'item_code' =>
                                $product->sku
                                ?? ('PRODUCT-' . $product->id),
                            'unit_price' => $item['unit_price'],
                            'quantity' => $quantity,
                            'subtotal' => $item['subtotal'],
                            'item_snapshot' => [
                                'product_id' => $product->id,
                                'sku' => $product->sku,
                                'name' => $product->name,
                                'category' =>
                                    $product->category?->name,
                                'list_price' =>
                                    (float) $product->list_price,
                                'sale_price' =>
                                    $product->sale_price !== null
                                        ? (float) $product->sale_price
                                        : null,
                                'unit_price' => $item['unit_price'],
                            ],
                        ]);

                        $product->stock_quantity = $quantityAfter;
                        $product->save();

                        StockHistory::create([
                            'stock_type' => 'PRODUCT',
                            'product_id' => $product->id,
                            'transaction_type' => 'ORDER',
                            'quantity_before' => $quantityBefore,
                            'quantity_change' => -$quantity,
                            'quantity_after' => $quantityAfter,
                            'order_id' => $order->id,
                            'updated_by' => null,
                            'note' =>
                                'Trừ tồn kho khi tạo đơn '
                                . $order->order_code,
                        ]);
                    } else {
                        /** @var CustomDesign $design */
                        $design = $item['custom_design'];

                        OrderDetail::create([
                            'order_id' => $order->id,
                            'item_type' => 'CUSTOM_DESIGN',
                            'product_id' => null,
                            'custom_design_id' => $design->id,
                            'item_name' => $design->name,
                            'item_code' => $design->design_code,
                            'unit_price' => $item['unit_price'],
                            'quantity' => 1,
                            'subtotal' => $item['subtotal'],
                            'item_snapshot' => [
                                'design_id' => $design->id,
                                'design_code' => $design->design_code,
                                'name' => $design->name,
                                'style' => $design->style,
                                'saved_snapshot' =>
                                    $design->configuration_snapshot,
                                'saved_estimated_price' =>
                                    (float) $design->estimated_price,
                                'purchase_price' =>
                                    (float) $item['unit_price'],
                                'purchase_products' =>
                                    $item['purchase_pricing']['items'],
                                'purchase_compatibility' =>
                                    $item['purchase_compatibility'],
                                'purchased_at' =>
                                    now()->toIso8601String(),
                            ],
                        ]);

                        foreach (
                            $item['design_products']
                            as $designProduct
                        ) {
                            /** @var Product $componentProduct */
                            $componentProduct =
                                $designProduct['product'];

                            $requiredQuantity =
                                (int) $designProduct['quantity'];

                            $quantityBefore =
                                (int) $componentProduct->stock_quantity;
                            $quantityAfter =
                                $quantityBefore - $requiredQuantity;

                            if ($quantityAfter < 0) {
                                throw ValidationException::withMessages([
                                    'stock' =>
                                        'Sản phẩm '
                                        . $componentProduct->name
                                        . ' không còn đủ tồn kho.',
                                ]);
                            }

                            $componentProduct->stock_quantity =
                                $quantityAfter;
                            $componentProduct->save();

                            StockHistory::create([
                                'stock_type' => 'PRODUCT',
                                'product_id' =>
                                    $componentProduct->id,
                                'transaction_type' => 'ORDER',
                                'quantity_before' =>
                                    $quantityBefore,
                                'quantity_change' =>
                                    -$requiredQuantity,
                                'quantity_after' =>
                                    $quantityAfter,
                                'order_id' => $order->id,
                                'updated_by' => null,
                                'note' =>
                                    'Trừ tồn kho Product của thiết kế '
                                    . $design->design_code
                                    . ' khi tạo đơn '
                                    . $order->order_code,
                            ]);
                        }
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | 11. ORDER STATUS HISTORY
                |--------------------------------------------------------------------------
                */

                OrderStatusHistory::create(
                    [
                        'order_id' =>
                            $order->id,

                        'status' =>
                            'pending',

                        'description' =>
                            'Đơn hàng được tạo thành công.',

                        /*
                         * Trạng thái khởi tạo
                         * do hệ thống tạo.
                         */
                        'updated_by' =>
                            null,
                    ]
                );




                /*
|--------------------------------------------------------------------------
| 12. PAYMENT
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| 12. PAYMENT
|--------------------------------------------------------------------------
|
| Thông tin phương thức thanh toán được lấy
| từ payment_settings tại thời điểm tạo Order.
|
| Sau khi Payment được tạo, thông tin ngân hàng
| được lưu Snapshot vào payments.
|
| Vì vậy nếu Admin đổi tài khoản sau này,
| Payment cũ vẫn giữ đúng dữ liệu lúc đặt hàng.
|
*/


/*
 * Lấy cấu hình phương thức thanh toán.
 */
$paymentSetting =
    PaymentSetting::query()
        ->where(
            'payment_method',
            $paymentMethod
        )
        ->first();


/*
 * Bảo vệ nghiệp vụ ở Service.
 *
 * Dù OrderService được gọi từ nơi khác,
 * phương thức Inactive vẫn không được dùng.
 */
if (
    !$paymentSetting
    ||
    !$paymentSetting->is_enabled
) {

    throw ValidationException::withMessages([
        'payment_method' =>
            'Phương thức thanh toán này hiện không khả dụng. Vui lòng chọn phương thức khác.',
    ]);
}


/*
|--------------------------------------------------------------------------
| SNAPSHOT THÔNG TIN QR
|--------------------------------------------------------------------------
*/

$bankName = null;
$accountName = null;
$accountNumber = null;
$transferContent = null;


if ($paymentMethod === 'QR') {
    /*
     * QR mới được xử lý bởi PayOS.
     * Thông tin ngân hàng / QR động sẽ được
     * PayOS trả về sau khi Order đã được tạo.
     */
    $bankName = null;
    $accountName = null;
    $accountNumber = null;
    $transferContent = null;
}


/*
|--------------------------------------------------------------------------
| TẠO PAYMENT
|--------------------------------------------------------------------------
*/

Payment::create(
    [
        'order_id' =>
            $order->id,

        'payment_method' =>
            $paymentMethod,

        'payment_provider' =>
            $paymentMethod === 'QR'
                ? 'PAYOS'
                : null,

        'amount' =>
            $totalAmount,

        /*
         * COD và QR đều bắt đầu Pending.
         */
        'status' =>
            'pending',

        'transaction_code' =>
            null,


        /*
         * COD:
         * các trường ngân hàng = NULL.
         *
         * QR:
         * lưu Snapshot từ payment_settings.
         */
        'bank_name' =>
            $bankName,

        'account_name' =>
            $accountName,

        'account_number' =>
            $accountNumber,

        'transfer_content' =>
            $transferContent,


        /*
         * QR đang mô phỏng,
         * chưa cần lưu file QR vật lý.
         */
        'qr_code_path' =>
            null,

        'confirmed_by' =>
            null,

        'paid_at' =>
            null,

        'refunded_at' =>
            null,

        'note' =>
            $paymentMethod === 'QR'
                ? 'Thanh toán QR qua PayOS, chờ xác nhận tự động.'
                : null,
    ]
);

                /*
                |--------------------------------------------------------------------------
                | 13. VOUCHER USAGE (ORDER & SHIPPING)
                |--------------------------------------------------------------------------
                */

                if ($orderVoucher) {

                    VoucherUsage::create(
                        [
                            'voucher_id' =>
                                $orderVoucher->id,

                            'user_id' =>
                                $user->id,

                            'order_id' =>
                                $order->id,

                            'voucher_scope' =>
                                Voucher::SCOPE_ORDER,

                            'discount_amount' =>
                                $discountAmount,

                            'status' =>
                                'used',

                            'used_at' =>
                                now(),

                            'released_at' =>
                                null,
                        ]
                    );

                    /*
                     * Tăng số lượt đã dùng của Order Voucher.
                     */
                    $orderVoucher->usage_count =
                        (int) $orderVoucher->usage_count + 1;

                    $orderVoucher->save();
                }

                if ($shippingVoucher) {

                    VoucherUsage::create(
                        [
                            'voucher_id' =>
                                $shippingVoucher->id,

                            'user_id' =>
                                $user->id,

                            'order_id' =>
                                $order->id,

                            'voucher_scope' =>
                                Voucher::SCOPE_SHIPPING,

                            'discount_amount' =>
                                $shippingDiscountAmount,

                            'status' =>
                                'used',

                            'used_at' =>
                                now(),

                            'released_at' =>
                                null,
                        ]
                    );

                    /*
                     * Tăng số lượt đã dùng của Shipping Voucher.
                     */
                    $shippingVoucher->usage_count =
                        (int) $shippingVoucher->usage_count + 1;

                    $shippingVoucher->save();
                }


                /*
                |--------------------------------------------------------------------------
                | 14. XÓA CART ITEM ĐÃ MUA
                |--------------------------------------------------------------------------
                |
                | Chỉ Checkout mode = cart.
                |
                | Những món KHÔNG tick
                | vẫn giữ nguyên trong Cart.
                |
                */

                if (
                    $mode === 'cart'
                ) {

                    $idsToDelete =
                        $orderItems
                            ->pluck(
                                'cart_item_id'
                            )
                            ->filter()
                            ->values()
                            ->all();


                    CartItem::query()
                        ->whereIn(
                            'id',
                            $idsToDelete
                        )
                        ->whereHas(
                            'cart',
                            function ($query) use ($user) {

                                $query->where(
                                    'user_id',
                                    $user->id
                                );
                            }
                        )
                        ->delete();
                }


                /*
                |--------------------------------------------------------------------------
                | 15. RETURN ORDER
                |--------------------------------------------------------------------------
                */

                return $order
                    ->load([
                        'details',
                        'payments',
                        'statusHistories',
                        'voucherUsage',
                    ]);
            }
        );
    }


    /**
     * Kiểm tra Product trước khi đặt hàng.
     */
    private function validateProduct(
        ?Product $product,
        int $quantity
    ): void {

        if (!$product) {

            throw ValidationException::withMessages([
                'product' =>
                    'Có sản phẩm không còn tồn tại.',
            ]);
        }


        if (!$product->is_active) {

            throw ValidationException::withMessages([
                'product' =>
                    'Sản phẩm '
                    . $product->name
                    . ' hiện không còn được bán.',
            ]);
        }


        if ($quantity < 1) {

            throw ValidationException::withMessages([
                'quantity' =>
                    'Số lượng sản phẩm không hợp lệ.',
            ]);
        }


        if (
            $quantity >
            $product->stock_quantity
        ) {

            throw ValidationException::withMessages([
                'stock' =>
                    'Sản phẩm '
                    . $product->name
                    . ' chỉ còn '
                    . $product->stock_quantity
                    . ' sản phẩm.',
            ]);
        }
    }


    /**
     * Giá hiện tại của Product.
     */
    private function getProductPrice(
        Product $product
    ): float {

        return $product->sale_price !== null
            ? (float)
                $product->sale_price
            : (float)
                $product->list_price;
    }


    /**
     * Validate Voucher lần cuối
     * bên trong Transaction.
     */
    private function validateAndCalculateVoucher(
        User $user,
        Voucher $voucher,
        float $subtotal,
        float $shippingFee = 0.0,
        string $expectedScope = Voucher::SCOPE_ORDER
    ): float {

        if (!$voucher->is_active) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher không còn hoạt động.',
            ]);
        }


        $now = now();


        if (
            $voucher->start_at->gt(
                $now
            )
        ) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher chưa đến thời gian sử dụng.',
            ]);
        }


        if (
            $voucher->end_at->lt(
                $now
            )
        ) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher đã hết hạn.',
            ]);
        }


        /*
         * Kiểm tra tổng lượt.
         */
        if (
            $voucher->usage_limit
                !== null
            &&
            $voucher->usage_count
                >=
            $voucher->usage_limit
        ) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher đã hết lượt sử dụng.',
            ]);
        }


        /*
         * Kiểm tra phạm vi voucher.
         */
        $scope =
            $voucher->voucher_scope
            ?? Voucher::SCOPE_ORDER;

        if ($scope !== $expectedScope) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Phạm vi áp dụng của Voucher không hợp lệ.',
            ]);
        }


        /*
         * Giá trị đơn tối thiểu (luôn kiểm tra theo subtotal).
         */
        if (
            $subtotal <
            (float)
            $voucher
                ->min_order_amount
        ) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Đơn hàng không còn đủ điều kiện sử dụng Voucher.',
            ]);
        }


        /*
         * Giới hạn theo Customer.
         */
        if (
            $voucher
                ->per_user_limit
            !== null
        ) {

            $usedCount =
                VoucherUsage::query()
                    ->where(
                        'voucher_id',
                        $voucher->id
                    )
                    ->where(
                        'user_id',
                        $user->id
                    )
                    ->where(
                        'status',
                        'used'
                    )
                    ->count();


            if (
                $usedCount >=
                $voucher
                    ->per_user_limit
            ) {

                throw ValidationException::withMessages([
                    'voucher' =>
                        'Bạn đã sử dụng hết lượt của Voucher này.',
                ]);
            }
        }


        /*
         * Căn cứ giảm:
         * - shipping: phí vận chuyển
         * - order: tiền hàng
         */
        $baseAmount =
            $scope === Voucher::SCOPE_SHIPPING
                ? $shippingFee
                : $subtotal;


        /*
         * Tính Discount.
         */
        if (
            $voucher
                ->discount_type
            === 'percentage'
        ) {

            $discount =
                $baseAmount
                * (
                    (float)
                    $voucher
                        ->discount_value
                    / 100
                );
        }

        elseif (
            $voucher
                ->discount_type
            === 'fixed'
        ) {

            $discount =
                (float)
                $voucher
                    ->discount_value;
        }

        else {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher có cấu hình không hợp lệ.',
            ]);
        }


        /*
         * Giảm tối đa.
         */
        if (
            $voucher
                ->max_discount_amount
            !== null
        ) {

            $discount =
                min(
                    $discount,
                    (float)
                    $voucher
                        ->max_discount_amount
                );
        }


        /*
         * Không để vượt quá căn cứ giảm.
         */
        $discount =
            min(
                $discount,
                $baseAmount
            );


        return max(
            0.0,
            $discount
        );
    }


    /**
     * Sinh mã Order.
     *
     * Ví dụ:
     * MG-20260831-A8K29X
     */
    private function generateOrderCode(): string
    {
        do {

            $code =
                'MG-'
                . now()->format(
                    'Ymd'
                )
                . '-'
                . Str::upper(
                    Str::random(6)
                );

        } while (
            Order::query()
                ->where(
                    'order_code',
                    $code
                )
                ->exists()
        );


        return $code;
    }
}