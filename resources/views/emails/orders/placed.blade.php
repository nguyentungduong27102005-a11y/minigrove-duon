<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">

    <title>Xác nhận đơn hàng</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f5f7f5; padding:30px;">

<div style="
    max-width:650px;
    margin:auto;
    background:white;
    padding:30px;
    border-radius:12px;
">

    <h1 style="color:#174c38;">
        MiniGrove
    </h1>

    <h2>
        Đặt hàng thành công
    </h2>

    <p>
        Xin chào <strong>{{ $order->customer_name }}</strong>,
    </p>

    <p>
        MiniGrove đã tiếp nhận đơn hàng của bạn.
    </p>

    <hr>

    <p>
        <strong>Mã đơn hàng:</strong>
        {{ $order->order_code }}
    </p>

    <p>
        <strong>Địa chỉ nhận hàng:</strong><br>
        {{ $order->shipping_address }}
    </p>

    <p>
        <strong>Phương thức thanh toán:</strong>
        {{ $order->payment_method }}
    </p>

    <hr>

    <h3>Chi tiết thanh toán</h3>

    <p>
        Tạm tính:
        <strong>
            {{ number_format($order->subtotal, 0, ',', '.') }}đ
        </strong>
    </p>

    <p>
        Giảm giá:
        <strong>
            {{ number_format($order->discount_amount, 0, ',', '.') }}đ
        </strong>
    </p>

    <p>
        Phí vận chuyển:
        <strong>
            {{ number_format($order->shipping_fee, 0, ',', '.') }}đ
        </strong>
    </p>

    <h2 style="color:#174c38;">
        Tổng thanh toán:
        {{ number_format($order->total_amount, 0, ',', '.') }}đ
    </h2>

    <hr>

    <p>
        Cảm ơn bạn đã mua sắm tại MiniGrove 🌱
    </p>

</div>

</body>
</html>