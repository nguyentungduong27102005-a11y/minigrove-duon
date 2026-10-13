<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use PayOS\Models\V2\PaymentRequests\CreatePaymentLinkRequest;
use PayOS\Models\V2\PaymentRequests\PaymentLink;
use PayOS\Models\V2\PaymentRequests\PaymentLinkStatus;
use PayOS\Models\Webhooks\WebhookData;
use PayOS\PayOS;
use RuntimeException;

class PayOSService
{
    public function configured(): bool
    {
        return filled(config('services.payos.client_id'))
            && filled(config('services.payos.api_key'))
            && filled(config('services.payos.checksum_key'));
    }

    public function createCheckoutUrl(Order $order): string
    {
        $payment = $this->paymentFor($order);

        if (
            $payment->payment_provider === 'PAYOS'
            && filled($payment->provider_checkout_url)
        ) {
            return $payment->provider_checkout_url;
        }

        $this->assertConfigured();

        $amount = (int) round((float) $order->total_amount);

        if ($amount < 1) {
            throw new RuntimeException(
                'Số tiền thanh toán PayOS không hợp lệ.'
            );
        }

        /*
         * PayOS yêu cầu orderCode là số nguyên và duy nhất.
         * ID Order của MiniGrove đáp ứng điều kiện này.
         */
        $request = new CreatePaymentLinkRequest(
            orderCode: (int) $order->id,
            amount: $amount,
            description: 'MINIGROVE ' . $order->id,
            cancelUrl: route(
                'checkout.payos.cancel',
                $order
            ),
            returnUrl: route(
                'checkout.payos.return',
                $order
            ),
            buyerName: $order->customer_name,
            buyerEmail: $order->email,
            buyerPhone: $order->phone,
            buyerAddress: $order->shipping_address,
            expiredAt: now()
                ->addMinutes(30)
                ->timestamp
        );

        $link = $this->client()
            ->paymentRequests
            ->create($request);

        $payment->forceFill([
            'payment_provider' => 'PAYOS',
            'provider_payment_id' =>
                $link->paymentLinkId,
            'provider_checkout_url' =>
                $link->checkoutUrl,
            'bank_name' => 'PayOS',
            'account_name' => $link->accountName,
            'account_number' => $link->accountNumber,
            'transfer_content' => $link->description,
            'note' =>
                'Thanh toán QR qua PayOS, chờ webhook xác nhận tự động.',
        ])->save();

        return $link->checkoutUrl;
    }

    public function syncOrderStatus(Order $order): bool
    {
        $this->assertConfigured();

        $link = $this->client()
            ->paymentRequests
            ->get((int) $order->id);

        if ($link->status !== PaymentLinkStatus::PAID) {
            return false;
        }

        $transaction = $link->transactions[0] ?? null;

        $this->markPaid(
            orderCode: (int) $order->id,
            amount: (int) $link->amountPaid,
            reference: $transaction?->reference,
            paymentLinkId: $link->id
        );

        return true;
    }

    public function verifyWebhook(array $payload): WebhookData
    {
        $this->assertConfigured();

        return $this->client()
            ->webhooks
            ->verify($payload);
    }

    public function handleVerifiedWebhook(
        WebhookData $data
    ): bool {
        if ($data->code !== '00') {
            return false;
        }

        return $this->markPaid(
            orderCode: $data->orderCode,
            amount: $data->amount,
            reference: $data->reference,
            paymentLinkId: $data->paymentLinkId
        );
    }

    public function confirmWebhook(
        string $webhookUrl
    ): mixed {
        $this->assertConfigured();

        return $this->client()
            ->webhooks
            ->confirm($webhookUrl);
    }

    private function markPaid(
        int $orderCode,
        int $amount,
        ?string $reference,
        ?string $paymentLinkId
    ): bool {
        return DB::transaction(
            function () use (
                $orderCode,
                $amount,
                $reference,
                $paymentLinkId
            ) {
                $order = Order::query()
                    ->whereKey($orderCode)
                    ->lockForUpdate()
                    ->first();

                /*
                 * PayOS có thể gửi payload mẫu khi xác minh
                 * webhook URL. Nếu không phải Order thật của
                 * MiniGrove thì xác nhận request nhưng bỏ qua.
                 */
                if (
                    !$order
                    || $order->payment_method !== 'QR'
                ) {
                    return false;
                }

                $expectedAmount =
                    (int) round(
                        (float) $order->total_amount
                    );

                if ($amount !== $expectedAmount) {
                    return false;
                }

                $payment = Payment::query()
                    ->where('order_id', $order->id)
                    ->where('payment_method', 'QR')
                    ->latest('id')
                    ->lockForUpdate()
                    ->first();

                if (!$payment) {
                    return false;
                }

                if (
                    filled($payment->provider_payment_id)
                    && filled($paymentLinkId)
                    && !hash_equals(
                        (string) $payment->provider_payment_id,
                        (string) $paymentLinkId
                    )
                ) {
                    return false;
                }

                if (
                    $payment->status === 'paid'
                    && $order->payment_status === 'paid'
                ) {
                    /*
                     * Trường hợp webhook/return được gọi lại:
                     * nếu tiền đã Paid nhưng Order vẫn còn Pending
                     * (ví dụ dữ liệu được tạo trước khi bổ sung
                     * auto-confirm), đồng bộ nốt sang Confirmed.
                     *
                     * Không đụng tới các trạng thái cao hơn như
                     * preparing / packed / shipping / completed.
                     */
                    if ($order->order_status === 'pending') {
                        $order->forceFill([
                            'order_status' => 'confirmed',
                        ])->save();

                        OrderStatusHistory::create([
                            'order_id' => $order->id,
                            'status' => 'confirmed',
                            'description' =>
                                'PayOS xác nhận thanh toán thành công. Đơn hàng được hệ thống tự động xác nhận.',
                            'updated_by' => null,
                        ]);
                    }

                    return true;
                }

                $payment->forceFill([
                    'payment_provider' => 'PAYOS',
                    'provider_payment_id' =>
                        $paymentLinkId
                        ?: $payment->provider_payment_id,
                    'transaction_code' =>
                        $reference
                        ?: $payment->transaction_code,
                    'status' => 'paid',
                    'paid_at' =>
                        $payment->paid_at ?? now(),
                    'confirmed_by' => null,
                    'note' =>
                        'PayOS xác nhận thanh toán tự động.',
                ])->save();

                $wasPending =
                    $order->order_status === 'pending';

                $order->forceFill([
                    'payment_status' => 'paid',
                    'order_status' =>
                        $wasPending
                            ? 'confirmed'
                            : $order->order_status,
                ])->save();

                if ($wasPending) {
                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'status' => 'confirmed',
                        'description' =>
                            'PayOS xác nhận thanh toán thành công. Đơn hàng được hệ thống tự động xác nhận.',
                        'updated_by' => null,
                    ]);
                }

                return true;
            }
        );
    }

    private function paymentFor(Order $order): Payment
    {
        return $order->payments()
            ->where('payment_method', 'QR')
            ->latest('id')
            ->firstOrFail();
    }

    private function client(): PayOS
    {
        $this->assertConfigured();

        return new PayOS(
            clientId:
                (string) config(
                    'services.payos.client_id'
                ),
            apiKey:
                (string) config(
                    'services.payos.api_key'
                ),
            checksumKey:
                (string) config(
                    'services.payos.checksum_key'
                )
        );
    }

    private function assertConfigured(): void
    {
        if (!$this->configured()) {
            throw new RuntimeException(
                'PayOS chưa được cấu hình. Vui lòng thêm PAYOS_CLIENT_ID, PAYOS_API_KEY và PAYOS_CHECKSUM_KEY vào .env.'
            );
        }
    }
}
