@extends('layouts.admin')


@section(
    'title',
    'Quản lý Grove khách hàng'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/admin-groves.css') }}"
>

@endpush



@section('content')


<div class="grove-page">


    {{-- HEADER --}}
    <div class="grove-header">

        <div>

            <h1>
                Grove của khách hàng
            </h1>

            <p>
                Quản lý hệ sinh thái Terrarium của Customer theo mã định danh.
            </p>

        </div>

    </div>




    {{-- STATS --}}
    <div class="grove-stats">


        <div class="grove-stat-card">

            <div class="grove-stat-title">
                Tổng Grove
            </div>

            <div class="grove-stat-number">
                {{ $totalGroves }}
            </div>

        </div>



        <div class="grove-stat-card">

            <div class="grove-stat-title">
                Active
            </div>

            <div class="grove-stat-number">
                {{ $activeGroves }}
            </div>

        </div>



        <div class="grove-stat-card">

            <div class="grove-stat-title">
                Chờ kích hoạt
            </div>

            <div class="grove-stat-number">
                {{ $pendingGroves }}
            </div>

        </div>



        <div class="grove-stat-card">

            <div class="grove-stat-title">
                Cần chăm sóc
            </div>

            <div class="grove-stat-number">
                {{ $attentionGroves }}
            </div>

        </div>


    </div>





    {{-- FILTER --}}
    <div class="grove-section">


        <form
            method="GET"
            action="{{ route('admin.groves.index') }}"
            class="grove-filter"
        >


            <input
                type="text"
                name="keyword"
                value="{{ $keyword }}"
                placeholder="Grove code / Terrarium..."
            >



            <input
                type="text"
                name="customer_id"
                value="{{ $customerId }}"
                placeholder="Customer ID..."
            >




            <select name="ecosystem">

                <option value="">
                    Tất cả hệ sinh thái
                </option>


                @foreach($ecosystemOptions as $item)

                    <option
                        value="{{ $item }}"
                        @selected($ecosystem === $item)
                    >
                        {{ $item }}
                    </option>

                @endforeach


            </select>




            <select name="status">

                <option value="">
                    Tất cả trạng thái
                </option>


                @foreach($statusOptions as $item)

                    <option
                        value="{{ $item }}"
                        @selected($status === $item)
                    >
                        {{ $item }}
                    </option>

                @endforeach


            </select>




            <button type="submit">

                Lọc

            </button>


        </form>


    </div>







    {{-- TABLE --}}

    <div class="grove-section">


        <h2>
            Danh sách Grove
        </h2>



        <div class="grove-table-wrapper">


            <table class="grove-table">


                <thead>

                    <tr>

                        <th>
                            Grove Code
                        </th>


                        <th>
                            Customer ID
                        </th>


                        <th>
                            Terrarium
                        </th>


                        <th>
                            Ecosystem
                        </th>


                        <th>
                            Care Level
                        </th>


                        <th>
                            Condition
                        </th>


                        <th>
                            Status
                        </th>


                        <th>
                            #
                        </th>


                    </tr>


                </thead>



                <tbody>


                @forelse($groves as $grove)


                    <tr>


                        <td>
                            {{ $grove->grove_code }}
                        </td>


                        <td>
                            Customer #{{ $grove->user_id }}
                        </td>


                        <td>
                            {{ $grove->terrarium_name }}
                        </td>


                        <td>
                            {{ $grove->ecosystem_type }}
                        </td>


                        <td>
                            {{ $grove->care_level }}
                        </td>


                        <td>
                            {{ $grove->current_condition }}
                        </td>


                        <td>

                            <span class="grove-status">

                                {{ $grove->status }}

                            </span>

                        </td>


                        <td>


                            <a
                                href="{{ route('admin.groves.show',$grove->id) }}"
                                class="grove-action"
                            >

                                Xem

                            </a>


                        </td>


                    </tr>


                @empty


                    <tr>

                        <td colspan="8">

                            Không có Grove nào.

                        </td>

                    </tr>


                @endforelse



                </tbody>


            </table>


        </div>


        <div class="grove-pagination">

            {{ $groves->links() }}

        </div>


    </div>


</div>


@endsection