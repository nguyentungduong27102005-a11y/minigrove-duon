<?php

namespace App\Support;

use App\Models\Message;
use Illuminate\Support\Facades\Storage;

class ChatMessagePayload
{
    public static function make(Message $message): array
    {
        $message->loadMissing([
            'attachments',
            'productCards',
        ]);

        return [
            'id' => $message->id,
            'conversation_id' => $message->conversation_id,
            'sender_id' => $message->sender_id,
            'content' => $message->content,
            'message_type' => $message->message_type,
            'sent_at' => $message->sent_at?->toIso8601String(),
            'sent_at_label' => $message->sent_at?->format(
                'H:i d/m/Y'
            ),
            'attachments' => $message->attachments
                ->map(
                    fn ($attachment) => [
                        'id' => $attachment->id,
                        'name' => $attachment->original_name,
                        'mime_type' => $attachment->mime_type,
                        'file_size' => $attachment->file_size,
                        'type' => $attachment->attachment_type,
                        'url' => Storage::disk('public')->url(
                            $attachment->file_path
                        ),
                    ]
                )
                ->values()
                ->all(),
            'products' => $message->productCards
                ->map(function ($productCard) {
                    $listPrice =
                        (float) $productCard->list_price;
                    $salePrice =
                        $productCard->sale_price !== null
                            ? (float) $productCard->sale_price
                            : null;
                    $hasSale =
                        $salePrice !== null
                        && $salePrice < $listPrice;

                    return [
                        'id' => $productCard->product_id,
                        'name' => $productCard->product_name,
                        'sku' => $productCard->product_sku,
                        'list_price' => $listPrice,
                        'sale_price' => $salePrice,
                        'has_sale' => $hasSale,
                        'price_label' => number_format(
                            $hasSale
                                ? $salePrice
                                : $listPrice,
                            0,
                            ',',
                            '.'
                        ) . 'đ',
                        'list_price_label' => number_format(
                            $listPrice,
                            0,
                            ',',
                            '.'
                        ) . 'đ',
                        'image_url' => $productCard->image_path
                            ? Storage::disk('public')->url(
                                $productCard->image_path
                            )
                            : null,
                        'url' => $productCard->product_id
                            ? route(
                                'products.show',
                                $productCard->product_id
                            )
                            : null,
                    ];
                })
                ->values()
                ->all(),
        ];
    }
}