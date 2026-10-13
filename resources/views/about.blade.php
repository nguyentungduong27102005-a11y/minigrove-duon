@extends('layouts.customer')

@section('title', 'Về chúng tôi - MiniGrove')

@push('styles')
    <link
        rel="stylesheet"
        href="{{
            asset('css/about.css')
            . '?v='
            . filemtime(
                public_path(
                    'css/about.css'
                )
            )
        }}"
    >
@endpush

@section('content')

<section class="about-page">

    <div class="customer-container">

        <section class="about-hero">

            <div class="about-eyebrow">
                VỀ CHÚNG TÔI
            </div>

            <h1>
                Câu chuyện của MiniGrove
            </h1>

            <p>
                MiniGrove được hình thành từ mong muốn mang một
                khoảng xanh nhỏ đến gần hơn với cuộc sống hằng ngày,
                đồng thời tạo ra một trải nghiệm mua sắm và tự thiết kế
                Terrarium trực quan, dễ sử dụng và mang dấu ấn riêng
                của mỗi người.
            </p>

        </section>


        <section class="about-story-grid">

            <article class="about-story-card">

                <div class="about-card-icon">
                    🌱
                </div>

                <h2>
                    Khởi nguồn ý tưởng
                </h2>

                <p>
                    MiniGrove bắt đầu từ ý tưởng xây dựng một không gian
                    dành cho những người yêu cây xanh và Terrarium.
                    Thay vì chỉ xem và mua sản phẩm có sẵn, người dùng
                    còn có thể tự lựa chọn bình, cây, rêu, nền, đá,
                    phụ kiện và phong cách để tạo nên một hệ sinh thái
                    mini theo sở thích của mình.
                </p>

            </article>


            <article class="about-story-card">

                <div class="about-card-icon">
                    🪴
                </div>

                <h2>
                    Hình thành MiniGrove
                </h2>

                <p>
                    Từ ý tưởng ban đầu, MiniGrove được phát triển thành
                    một hệ thống thương mại điện tử kết hợp trải nghiệm
                    thiết kế Terrarium. Các chức năng mua sắm, giỏ hàng,
                    yêu thích, voucher, thanh toán và quản lý đơn hàng
                    được kết nối với khu vực “Thiết kế của tôi” để tạo
                    thành một hành trình thống nhất cho khách hàng.
                </p>

            </article>


            <article class="about-story-card">

                <div class="about-card-icon">
                    🌿
                </div>

                <h2>
                    Phát triển trải nghiệm
                </h2>

                <p>
                    MiniGrove tiếp tục được hoàn thiện theo hướng thuận
                    tiện và gần gũi hơn: người dùng có thể xem chi tiết
                    sản phẩm, lưu voucher, tự thiết kế Terrarium,
                    thanh toán trực tuyến và trao đổi với bộ phận hỗ trợ
                    qua Chat. Mỗi phần đều được xây dựng để giữ giao diện
                    nhất quán, dễ hiểu và phù hợp với chủ đề thiên nhiên.
                </p>

            </article>

        </section>


        <section class="about-vision">

            <div>

                <div class="about-eyebrow">
                    MINI ECOSYSTEM
                </div>

                <h2>
                    Nhỏ một khu rừng,
                    lớn một thế giới.
                </h2>

                <p>
                    MiniGrove hướng đến việc biến quá trình chọn,
                    thiết kế và sở hữu một Terrarium thành một trải
                    nghiệm cá nhân hóa. Mỗi sản phẩm không chỉ là một
                    món đồ trang trí mà còn là một hệ sinh thái nhỏ
                    mang câu chuyện và phong cách riêng của người sở hữu.
                </p>

            </div>

            <div class="about-vision-mark">
                🌿
            </div>

        </section>


        <section class="about-team">

            <div class="about-section-heading">

                <div class="about-eyebrow">
                    NHÓM THỰC HIỆN
                </div>

                <h2>
                    Nhóm 13
                </h2>

                <p>
                    MiniGrove được xây dựng và hoàn thiện bởi:
                </p>

            </div>

            <div class="about-team-grid">

                <div class="about-member">
                    <span>01</span>
                    <strong>Nguyễn Thị Mến</strong>
                </div>

                <div class="about-member">
                    <span>02</span>
                    <strong>Nguyễn Minh Hà</strong>
                </div>

                <div class="about-member">
                    <span>03</span>
                    <strong>Nguyễn Tùng Dương</strong>
                </div>

            </div>

        </section>


        <section class="about-contact">

            <div class="about-section-heading">

                <div class="about-eyebrow">
                    LIÊN HỆ
                </div>

                <h2>
                    Kết nối với MiniGrove
                </h2>

            </div>

            <div class="about-contact-grid">

                <div class="about-contact-card">
                    <span>⌖</span>
                    <div>
                        <strong>Địa chỉ</strong>
                        <p>
                            Số 41A phường Phú Diễn,
                            quận Bắc Từ Liêm, Hà Nội
                        </p>
                    </div>
                </div>

                <a
                    class="about-contact-card"
                    href="tel:035791347"
                >
                    <span>☎</span>
                    <div>
                        <strong>Hotline</strong>
                        <p>035791347</p>
                    </div>
                </a>

                <a
                    class="about-contact-card"
                    href="mailto:gioinhatlop@gmail.com"
                >
                    <span>✉</span>
                    <div>
                        <strong>Email</strong>
                        <p>gioinhatlop@gmail.com</p>
                    </div>
                </a>

            </div>

        </section>

    </div>

</section>

@endsection