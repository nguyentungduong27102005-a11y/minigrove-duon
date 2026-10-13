@extends('layouts.customer')

@section(
    'title',
    'Thanh toán PayOS - MiniGrove'
)

@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('css/checkout.css') }}"
>

<style>
    .payos-fallback {
        max-width: 760px;
        margin: 0 auto;
    }

    .payos-card {
        padding: 34px;
        border: 1px solid #dce6e0;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 14px 36px rgba(20, 72, 50, .08);
    }

    .payos-brand {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
        color: #155b3b;
        font-size: 24px;
        font-weight: 800;
    }

    .payos-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 11px;
        border-radius: 999px;
        background: #fff4d8;
        color: #8a6517;
        font-size: 12px;
        font-weight: 700;
    }

    .payos-info {
        margin: 22px 0;
        border-top: 1px solid #e2e9e5;
    }

    .payos-info-row {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        padding: 14px 0;
        border-bottom: 1px solid #e2e9e5;
    }

    .payos-info-row span {
        color: #738078;
    }

    .payos-info-row strong {
        color: #163f2e;
        text-align: right;
    }

    .payos-amount {
        font-size: 22px;
        color: #b64d32 !important;
    }

    .payos-note {
        margin: 20px 0;
        padding: 14px 16px;
        border-radius: 10px;
        background: #eaf4ed;
        color: #245a3e;
        line-height: 1.6;
    }

    .payos-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .payos-button {
        min-height: 46px;
        padding: 0 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #185b3d;
        border-radius: 9px;
        background: #185b3d;
        color: #fff;
        text-decoration: none;
        font-weight: 700;
    }

    .payos-button-secondary {
        background: #fff;
        color: #185b3d;
    }

    .payos-error {
        margin-bottom: 18px;
        padding: 12px 14px;
        border: 1px solid #efc9bf;
        border-radius: 9px;
        background: #fff5f2;
        color: #9a3f2c;
    }

    @media (max-width: 640px) {
        .payos-card {
            padding: 22px;
        }

        .payos-info-row {
            flex-direction: column;
            gap: 5px;
        }

        .payos-info-row strong {
            text-align: left;
        }

        .payos-button {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<section class="checkout-page">
    <div class="customer-container">

        <a
            href="{{ route('home') }}"
            class="checkout-back-button"
        >
            ← Về trang chủ
        </a>

        <div class="payos-fallback">
            <div class="payos-card">

                <div class="payos-brand">
                    <span aria-hidden="true">▣</span>
                    Thanh toán QR qua PayOS
                </div>

                <div class="payos-badge">
                    <span aria-hidden="true">●</span>
                    Đang chờ PayOS xác nhận
                </div>

                <div class="payos-info">
                    <div class="payos-info-row">
                        <span>Mã đơn hàng</span>
                        <strong>
                            {{ $order->order_code }}
                        </strong>
                    </div>

                    <div class="payos-info-row">
                        <span>Số tiền</span>
                        <strong class="payos-amount">
                            {{
                                number_format(
                                    (float) $order->total_amount,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ
                        </strong>
                    </div>

                    <div class="payos-info-row">
                        <span>Cổng thanh toán</span>
                        <strong>PayOS</strong>
                    </div>

                    @if ($payment->provider_payment_id)
                        <div class="payos-info-row">
                            <span>Payment Link ID</span>
                            <strong>
                                {{ $payment->provider_payment_id }}
                            </strong>
                        </div>
                    @endif
                </div>

                <div
                    class="payos-note"
                    id="payosPaymentNotice"
                >
                    PayOS sẽ xác nhận tự động sau khi thanh toán thành công.
                    Bạn không cần gửi ảnh chuyển khoản hoặc chờ Admin xác nhận thủ công.
                </div>

                <div class="payos-actions">
                    @if ($payment->provider_checkout_url)
                        <a
                            href="{{ $payment->provider_checkout_url }}"
                            class="payos-button"
                        >
                            Tiếp tục thanh toán qua PayOS
                        </a>
                    @else
                        <a
                            href="{{ route('checkout.qr', $order) }}"
                            class="payos-button"
                        >
                            Thử tạo lại PayOS
                        </a>
                    @endif

                    <a
                        href="{{ route('home') }}"
                        class="
                            payos-button
                            payos-button-secondary
                        "
                    >
                        Về trang chủ
                    </a>
                </div>

            </div>
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusUrl =
        @json(route('checkout.qr.status', $order));

    const successUrl =
        @json(route('checkout.success', $order));

    const notice =
        document.getElementById(
            'payosPaymentNotice'
        );

    let redirecting = false;

    async function checkPaymentStatus() {
        if (redirecting) {
            return;
        }

        try {
            const response = await fetch(
                statusUrl,
                {
                    headers: {
                        Accept: 'application/json',
                    },
                    credentials: 'same-origin',
                    cache: 'no-store',
                }
            );

            if (!response.ok) {
                return;
            }

            const data = await response.json();

            if (data.paid) {
                redirecting = true;

                if (notice) {
                    notice.textContent =
                        'PayOS đã xác nhận thanh toán. Đang chuyển sang trang thành công...';
                }

                window.location.replace(
                    data.success_url || successUrl
                );
            }
        } catch (error) {
            // Giữ trang chờ nếu mạng tạm thời lỗi.
        }
    }

    checkPaymentStatus();

    const timer = window.setInterval(
        checkPaymentStatus,
        2000
    );

    window.addEventListener(
        'focus',
        checkPaymentStatus
    );

    document.addEventListener(
        'visibilitychange',
        function () {
            if (!document.hidden) {
                checkPaymentStatus();
            }
        }
    );

    window.addEventListener(
        'beforeunload',
        function () {
            window.clearInterval(timer);
        }
    );
});
</script>
@endpush
