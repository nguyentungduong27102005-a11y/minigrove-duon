<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\PayOSService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class PayOSWebhookController extends Controller
{
    public function handle(
        Request $request,
        PayOSService $payOSService
    ): JsonResponse {
        try {
            $verified = $payOSService
                ->verifyWebhook($request->all());

            $payOSService
                ->handleVerifiedWebhook($verified);

            return response()->json([
                'success' => true,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(
                [
                    'success' => false,
                    'message' =>
                        'Webhook PayOS không hợp lệ.',
                ],
                400
            );
        }
    }
}
