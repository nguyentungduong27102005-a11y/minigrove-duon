<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grove;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroveController extends Controller
{
    /**
     * Danh sách Grove của khách hàng.
     *
     * Privacy:
     * - Chỉ dùng Customer ID để nhận diện khách hàng.
     * - Không load quan hệ user().
     * - Không lấy name, email, phone, avatar, address.
     */
    public function index(Request $request): View
    {
        $keyword = trim((string) $request->query('keyword', ''));
        $customerId = trim((string) $request->query('customer_id', ''));
        $ecosystem = trim((string) $request->query('ecosystem', ''));
        $status = trim((string) $request->query('status', ''));

        $query = Grove::query()
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
            ]);

        if ($keyword !== '') {
            $query->where(function ($searchQuery) use ($keyword) {
                $searchQuery
                    ->where('grove_code', 'like', '%' . $keyword . '%')
                    ->orWhere('terrarium_name', 'like', '%' . $keyword . '%');
            });
        }

        if ($customerId !== '') {
            if (ctype_digit($customerId)) {
                $query->where('user_id', (int) $customerId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($ecosystem !== '') {
            $query->where('ecosystem_type', $ecosystem);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $groves = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $totalGroves = Grove::query()->count();

        $activeGroves = Grove::query()
            ->where('status', 'active')
            ->count();

        $pendingGroves = Grove::query()
            ->where('status', 'pending_activation')
            ->count();

        $attentionGroves = Grove::query()
            ->where('status', 'needs_attention')
            ->count();

        $archivedGroves = Grove::query()
            ->where('status', 'archived')
            ->count();

        $ecosystemOptions = Grove::query()
            ->whereNotNull('ecosystem_type')
            ->where('ecosystem_type', '!=', '')
            ->distinct()
            ->orderBy('ecosystem_type')
            ->pluck('ecosystem_type');

        $statusOptions = [
            'pending_activation',
            'active',
            'needs_attention',
            'archived',
        ];

        return view(
            'admin.groves.index',
            compact(
                'groves',
                'keyword',
                'customerId',
                'ecosystem',
                'status',
                'totalGroves',
                'activeGroves',
                'pendingGroves',
                'attentionGroves',
                'archivedGroves',
                'ecosystemOptions',
                'statusOptions'
            )
        );
    }

    /**
     * Chi tiết một Grove.
     *
     * Privacy:
     * - Không load user().
     * - Chỉ hiển thị Customer #ID thông qua user_id.
     */
    public function show(int $grove): View
    {
        $grove = Grove::query()
            ->whereKey($grove)
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
                'updated_at',
            ])
            ->firstOrFail();

        return view(
            'admin.groves.show',
            compact('grove')
        );
    }
}
