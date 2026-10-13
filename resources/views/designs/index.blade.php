@extends('layouts.customer')

@section('title', 'Thiết kế của tôi - MiniGrove')

@section('content')
<section class="design-page">
    <div class="customer-container">
        <div class="design-heading">
            <div>
                <h1>Thiết kế của tôi</h1>
                <p>Các Terrarium bạn đã tạo bằng MiniGrove Studio. Giá hiện hành được tính lại từ Product mỗi khi mua.</p>
            </div>
            <a href="{{ route('studio.index') }}" class="design-create">+ Tạo thiết kế mới</a>
        </div>

        @if ($designs->isEmpty())
            <div class="design-empty">
                <div class="design-empty-icon">🌿</div>
                <h2>Bạn chưa lưu thiết kế nào</h2>
                <p>Hãy bắt đầu với MiniGrove Studio.</p>
                <a href="{{ route('studio.index') }}" class="design-primary">Tự thiết kế Terrarium</a>
            </div>
        @else
            <div class="design-grid">
                @foreach ($designs as $design)
                    @php
                        $snapshot = $design->configuration_snapshot ?? [];
                        $types = [
                            'container' => 'Bình',
                            'plant' => 'Cây',
                            'moss' => 'Rêu',
                            'substrate' => 'Nền',
                            'stone' => 'Đá',
                            'accessory' => 'Phụ kiện',
                        ];
                        $canEdit = $design->order_details_count === 0;
                    @endphp

                    <article class="design-card">
                        <div class="design-card-head">
                            <div>
                                <span class="design-code">{{ $design->design_code }}</span>
                                <h2>{{ $design->name }}</h2>
                            </div>
                            <span class="design-style">{{ ucfirst($design->style) }}</span>
                        </div>

                        <div class="design-products">
                            @foreach ($types as $key => $label)
                                @if (!empty($snapshot[$key]))
                                    <div>
                                        <span>{{ $label }}</span>
                                        <strong>{{ $snapshot[$key]['name'] ?? '—' }}</strong>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="design-price-box">
                            <div>
                                <span>Giá khi lưu</span>
                                <strong>{{ number_format((float) $design->estimated_price, 0, ',', '.') }}đ</strong>
                            </div>
                            <div>
                                <span>Giá hiện hành</span>
                                @if ($design->current_estimated_price !== null)
                                    <strong class="design-current-price">{{ number_format((float) $design->current_estimated_price, 0, ',', '.') }}đ</strong>
                                @else
                                    <strong>Chưa thể mua</strong>
                                @endif
                            </div>
                        </div>

                        @if ($design->availability_error)
                            <div class="design-warning">
                                Thiết kế cần được điều chỉnh vì một Product hiện không còn phù hợp, đã hết hàng hoặc không còn hoạt động.
                            </div>
                        @endif

                        @if (!$canEdit)
                            <div class="design-note">
                                Thiết kế đã phát sinh Order nên không thể sửa hoặc xóa. Snapshot của đơn hàng vẫn được giữ nguyên.
                            </div>
                        @endif

                        <div class="design-actions">
                            @if ($design->current_estimated_price !== null)
                                <form method="POST" action="{{ route('designs.cart', $design) }}">
                                    @csrf
                                    <button type="submit" class="design-primary">Thêm vào giỏ</button>
                                </form>
                            @endif

                            @if ($canEdit)
                                <a href="{{ route('designs.edit', $design) }}" class="design-secondary">Chỉnh sửa</a>

                                <form method="POST" action="{{ route('designs.destroy', $design) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="design-danger">Xóa</button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection

@push('styles')
<style>
.design-page{min-height:650px;padding:42px 0 70px;background:#f7f9f8}
.design-heading{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;margin-bottom:24px}
.design-heading h1{margin:0 0 7px;color:#173f30}
.design-heading p{margin:0;color:#748079;line-height:1.6}
.design-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}
.design-card,.design-empty{padding:24px;border:1px solid #e0e8e3;border-radius:14px;background:#fff}
.design-card-head{display:flex;justify-content:space-between;gap:16px;padding-bottom:16px;border-bottom:1px solid #edf1ef}
.design-card h2{margin:5px 0 0;color:#173f30;font-size:20px}
.design-code,.design-style{color:#678075;font-size:12px;font-weight:700}
.design-style{height:fit-content;padding:6px 10px;border-radius:999px;background:#edf6f1;color:#174d37}
.design-products{display:grid;gap:8px;margin:18px 0}
.design-products div,.design-price-box{display:flex;justify-content:space-between;gap:16px}
.design-products span,.design-price-box span{color:#7a8781;font-size:13px}
.design-price-box{padding:15px;border-radius:10px;background:#f5f8f6}
.design-price-box>div{display:grid;gap:4px}
.design-price-box>div:last-child{text-align:right}
.design-current-price{color:#174d37}
.design-warning,.design-note{margin-top:14px;padding:11px 12px;border-radius:8px;background:#fff7e8;color:#7b5b19;font-size:13px;line-height:1.5}
.design-note{background:#f1f4f2;color:#647069}
.design-actions{display:flex;flex-wrap:wrap;gap:9px;margin-top:18px}
.design-actions form{margin:0}
.design-primary,.design-secondary,.design-danger,.design-create{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:9px 14px;border:0;border-radius:8px;text-decoration:none;font:inherit;font-size:13px;font-weight:700;cursor:pointer}
.design-primary,.design-create{background:#174d37;color:#fff}
.design-secondary{border:1px solid #cfdad4;background:#fff;color:#174d37}
.design-danger{background:#fff0f0;color:#b43d3d}
.design-empty{text-align:center;padding:60px 24px}
.design-empty-icon{font-size:42px}
.design-empty h2{color:#173f30}
@media(max-width:760px){.design-heading{align-items:flex-start;flex-direction:column}.design-grid{grid-template-columns:1fr}}
</style>
@endpush
