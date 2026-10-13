@extends('layouts.admin')


@section(
    'title',
    'Chi tiết Grove'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/admin-groves.css') }}"
>

@endpush



@section('content')


<div class="grove-page">


    <div class="grove-back-top">

        <a
            href="{{ route('admin.groves.index') }}"
            class="grove-action"
        >
            ← Danh sách Grove
        </a>

    </div>



    <div class="grove-header">

        <div>

            <h1>
                Chi tiết Grove
            </h1>

            <p>
                Thông tin hệ sinh thái của khách hàng.
            </p>

        </div>

    </div>





    {{-- THÔNG TIN CHÍNH --}}

    <div class="grove-detail-card">


        <h2>
            Grove #{{ $grove->id }}
        </h2>



        <div class="grove-detail-grid">



            <div>
                <strong>
                    Grove Code
                </strong>

                <p>
                    {{ $grove->grove_code ?? '—' }}
                </p>
            </div>



            <div>
                <strong>
                    Customer ID
                </strong>

                <p>
                    Customer #{{ $grove->user_id }}
                </p>
            </div>



            <div>
                <strong>
                    Terrarium
                </strong>

                <p>
                    {{ $grove->terrarium_name ?? '—' }}
                </p>
            </div>



            <div>
                <strong>
                    Hệ sinh thái
                </strong>

                <p>
                    {{ $grove->ecosystem_type ?? '—' }}
                </p>
            </div>



            <div>
                <strong>
                    Cấp chăm sóc
                </strong>

                <p>
                    {{ $grove->care_level ?? '—' }}
                </p>
            </div>



            <div>
                <strong>
                    Ánh sáng
                </strong>

                <p>
                    {{ $grove->light_requirement ?? '—' }}
                </p>
            </div>



            <div>
                <strong>
                    Chu kỳ chăm sóc
                </strong>

                <p>
                    {{ $grove->care_cycle_days ?? 0 }}
                    ngày
                </p>
            </div>



            <div>
                <strong>
                    Trạng thái
                </strong>

                <p>
                    {{ $grove->status ?? '—' }}
                </p>
            </div>



            <div>
                <strong>
                    Ngày nhận Grove
                </strong>

                <p>
                    {{
                        $grove->received_at
                        ? $grove->received_at->format('d/m/Y')
                        : '—'
                    }}
                </p>
            </div>



            <div>
                <strong>
                    Ngày kích hoạt
                </strong>

                <p>
                    {{
                        $grove->activated_at
                        ? $grove->activated_at->format('d/m/Y')
                        : '—'
                    }}
                </p>
            </div>



        </div>


    </div>







    {{-- THÀNH PHẦN HỆ SINH THÁI --}}


    <div class="grove-detail-card">


        <h2>
            Thành phần hệ sinh thái
        </h2>



        <div class="grove-detail-grid">



            <div>

                <strong>
                    Cây
                </strong>

                <p>
                    {{
                        is_array($grove->plant_snapshot)
                        ? implode(', ', $grove->plant_snapshot)
                        : ($grove->plant_snapshot ?? '—')
                    }}
                </p>

            </div>




            <div>

                <strong>
                    Rêu
                </strong>

                <p>
                    {{
                        is_array($grove->moss_snapshot)
                        ? implode(', ', $grove->moss_snapshot)
                        : ($grove->moss_snapshot ?? '—')
                    }}
                </p>

            </div>




            <div>

                <strong>
                    Giá thể
                </strong>

                <p>
                    {{
                        is_array($grove->substrate_snapshot)
                        ? implode(', ', $grove->substrate_snapshot)
                        : ($grove->substrate_snapshot ?? '—')
                    }}
                </p>

            </div>




            <div>

                <strong>
                    Đá
                </strong>

                <p>
                    {{
                        is_array($grove->stone_snapshot)
                        ? implode(', ', $grove->stone_snapshot)
                        : ($grove->stone_snapshot ?? '—')
                    }}
                </p>

            </div>




            <div>

                <strong>
                    Phụ kiện
                </strong>

                <p>
                    {{
                        is_array($grove->accessory_snapshot)
                        ? implode(', ', $grove->accessory_snapshot)
                        : ($grove->accessory_snapshot ?? '—')
                    }}
                </p>

            </div>



        </div>


    </div>






    




</div>


@endsection