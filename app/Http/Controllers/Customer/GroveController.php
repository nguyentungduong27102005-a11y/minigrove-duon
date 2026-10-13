<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Grove;

use Illuminate\Http\Request;
use Illuminate\View\View;

class GroveController extends Controller
{
    /**
     * Danh sách Grove của Customer đang đăng nhập.
     */
    public function index(
        Request $request
    ): View {

        $groves =
            Grove::query()
                ->where(
                    'user_id',
                    $request
                        ->user()
                        ->id
                )
                ->select([
                    'id',
                    'grove_code',
                    'user_id',
                    'order_detail_id',
                    'terrarium_name',
                    'cover_image',
                    'ecosystem_type',
                    'care_level',
                    'light_requirement',
                    'care_cycle_days',
                    'current_condition',
                    'status',
                    'received_at',
                    'activated_at',
                    'created_at',
                ])
                ->latest('id')
                ->paginate(12);


        return view(
            'groves.index',
            compact(
                'groves'
            )
        );
    }


    /**
     * Chi tiết một Grove.
     *
     * Customer chỉ được xem Grove thuộc chính mình.
     */
    public function show(
        Request $request,
        int $grove
    ): View {

        $grove =
            Grove::query()
                ->whereKey(
                    $grove
                )
                ->where(
                    'user_id',
                    $request
                        ->user()
                        ->id
                )
                ->select([
                    'id',
                    'grove_code',
                    'user_id',
                    'order_detail_id',
                    'terrarium_name',
                    'cover_image',
                    'ecosystem_type',
                    'plant_snapshot',
                    'moss_snapshot',
                    'substrate_snapshot',
                    'stone_snapshot',
                    'accessory_snapshot',
                    'care_level',
                    'light_requirement',
                    'care_cycle_days',
                    'current_condition',
                    'status',
                    'received_at',
                    'activated_at',
                    'created_at',
                ])
                ->firstOrFail();


        return view(
            'groves.show',
            compact(
                'grove'
            )
        );
    }
}