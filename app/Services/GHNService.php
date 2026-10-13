<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GHNService
{
    protected string $baseUrl;
    protected string $token;
    protected string $shopId;

    public function __construct()
    {
        $this->baseUrl = config('services.ghn.base_url');
        $this->token = config('services.ghn.token');
        $this->shopId = config('services.ghn.shop_id');
    }

    /**
     * Tính phí vận chuyển GHN.
     *
     * Chỉ gọi API tính phí, không tạo đơn giao hàng.
     */
    public function calculateFee(array $data): array
    {
        $response = Http::withHeaders([
            'Token' => $this->token,
            'ShopId' => $this->shopId,
            'Content-Type' => 'application/json',
        ])->post(
            $this->baseUrl . '/v2/shipping-order/fee',
            $data
        );

        if (!$response->successful()) {
            throw new RuntimeException(
                'GHN API error: ' . $response->body()
            );
        }

        $result = $response->json();

        if (($result['code'] ?? null) != 200) {
            throw new RuntimeException(
                $result['message'] ?? 'Không thể tính phí vận chuyển GHN.'
            );
        }

        return $result['data'];
    }
}