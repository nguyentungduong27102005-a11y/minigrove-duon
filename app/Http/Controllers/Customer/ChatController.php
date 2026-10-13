<?php

namespace App\Http\Controllers\Customer;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\ChatAttachmentService;
use App\Support\ChatMessagePayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Trang Chat của Customer.
     */
    public function index(Request $request): View
    {
        $hasChatHistory = Conversation::query()
            ->where(
                'customer_id',
                $request->user()->id
            )
            ->exists();

        /*
        |--------------------------------------------------------------------------
        | CONVERSATION ĐANG HOẠT ĐỘNG
        |--------------------------------------------------------------------------
        |
        | Mỗi Customer chỉ dùng Conversation mới nhất
        | chưa đóng để tiếp tục trao đổi.
        |
        */

        $conversation = Conversation::query()
            ->where(
                'customer_id',
                $request->user()->id
            )
            ->whereIn(
                'status',
                [
                    'open',
                    'waiting',
                ]
            )
            ->select([
                'id',
                'customer_id',
                'assigned_admin_id',
                'topic',
                'subject',
                'status',
                'last_message_at',
                'created_at',
            ])
            ->latest('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | TIN NHẮN
        |--------------------------------------------------------------------------
        */

        $messages = collect();

        if ($conversation) {
            $messages = Message::query()
                ->with([
                    'attachments:id,message_id,original_name,file_path,mime_type,file_size,attachment_type',
                    'productCards:id,message_id,product_id,product_name,product_sku,list_price,sale_price,image_path,sort_order',
                ])
                ->where(
                    'conversation_id',
                    $conversation->id
                )
                ->select([
                    'id',
                    'conversation_id',
                    'sender_id',
                    'content',
                    'message_type',
                    'sent_at',
                    'read_at',
                ])
                ->orderBy('sent_at')
                ->orderBy('id')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | ĐÁNH DẤU TIN NHẮN ADMIN ĐÃ ĐỌC
            |--------------------------------------------------------------------------
            |
            | Chỉ đánh dấu những tin không phải Customer gửi.
            |
            */

            Message::query()
                ->where(
                    'conversation_id',
                    $conversation->id
                )
                ->where(
                    'sender_id',
                    '!=',
                    $request->user()->id
                )
                ->whereNull(
                    'read_at'
                )
                ->update([
                    'read_at' => now(),
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | LỊCH SỬ HỘI THOẠI ĐÃ ĐÓNG
        |--------------------------------------------------------------------------
        */

        $closedConversations = Conversation::query()
            ->where(
                'customer_id',
                $request->user()->id
            )
            ->where(
                'status',
                'closed'
            )
            ->select([
                'id',
                'subject',
                'topic',
                'closed_at',
                'created_at',
            ])
            ->latest('id')
            ->limit(10)
            ->get();

        return view(
            'chat.index',
            compact(
                'conversation',
                'messages',
                'closedConversations',
                'hasChatHistory'
            )
        );
    }

    /**
     * Customer gửi tin nhắn.
     */
    public function store(
        Request $request,
        ChatAttachmentService $attachmentService
    ): RedirectResponse|JsonResponse {
        $validated = $request->validate(
            [
                'content' => [
                    'nullable',
                    'string',
                    'max:5000',
                ],
                'images' => [
                    'nullable',
                    'array',
                    'max:5',
                ],
                'images.*' => [
                    'image',
                    'mimes:jpg,jpeg,png,webp,gif',
                    'max:5120',
                ],
            ],
            [
                'content.max' =>
                    'Tin nhắn không được vượt quá 5000 ký tự.',
                'images.max' =>
                    'Mỗi tin nhắn chỉ được gửi tối đa 5 ảnh.',
                'images.*.image' =>
                    'Tệp đính kèm phải là hình ảnh.',
                'images.*.mimes' =>
                    'Ảnh chỉ hỗ trợ JPG, JPEG, PNG, WEBP hoặc GIF.',
                'images.*.max' =>
                    'Mỗi ảnh không được vượt quá 5MB.',
            ]
        );

        $content = trim(
            (string) ($validated['content'] ?? '')
        );
        $images = $request->file('images', []);

        if ($content === '' && $images === []) {
            throw ValidationException::withMessages([
                'content' =>
                    'Vui lòng nhập tin nhắn hoặc chọn ít nhất một ảnh.',
            ]);
        }

        [$conversation, $message] = DB::transaction(
            function () use (
                $request,
                $content,
                $images,
                $attachmentService
            ) {
                /*
                |--------------------------------------------------------------------------
                | TÌM CONVERSATION ĐANG HOẠT ĐỘNG
                |--------------------------------------------------------------------------
                */

                $conversation = Conversation::query()
                    ->where(
                        'customer_id',
                        $request->user()->id
                    )
                    ->whereIn(
                        'status',
                        [
                            'open',
                            'waiting',
                        ]
                    )
                    ->latest('id')
                    ->lockForUpdate()
                    ->first();

                /*
                |--------------------------------------------------------------------------
                | NẾU CHƯA CÓ → TẠO MỚI
                |--------------------------------------------------------------------------
                */

                if (!$conversation) {
                    $conversation = Conversation::create([
                        'customer_id' =>
                            $request->user()->id,

                        'assigned_admin_id' =>
                            null,

                        'topic' =>
                            'GENERAL',

                        'subject' =>
                            'Yêu cầu hỗ trợ',

                        'status' =>
                            'open',

                        'last_message_at' =>
                            now(),

                        'closed_by' =>
                            null,

                        'closed_at' =>
                            null,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | TẠO MESSAGE
                |--------------------------------------------------------------------------
                */

                $message = Message::create([
                    'conversation_id' =>
                        $conversation->id,

                    'sender_id' =>
                        $request->user()->id,

                    'content' =>
                        $content !== ''
                            ? $content
                            : null,

                    'message_type' =>
                        $images !== []
                            ? (
                                $content !== ''
                                    ? 'MIXED'
                                    : 'IMAGE'
                            )
                            : 'TEXT',

                    'sent_at' =>
                        now(),

                    'read_at' =>
                        null,
                ]);

                if ($images !== []) {
                    $attachmentService->attachImages(
                        $message,
                        $images
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | CẬP NHẬT CONVERSATION
                |--------------------------------------------------------------------------
                */

                $conversation->update([
                    'status' =>
                        'open',

                    'last_message_at' =>
                        now(),
                ]);

                return [
                    $conversation->fresh(),
                    $message->fresh()->load('attachments'),
                ];
            }
        );

        ChatMessageSent::dispatch(
            $message,
            $conversation
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'message' => ChatMessagePayload::make(
                    $message
                ),
                'conversation' => [
                    'id' => $conversation->id,
                    'status' => $conversation->status,
                    'subject' => $conversation->subject,
                ],
            ]);
        }

        return redirect()
            ->route('chat.index')
            ->with(
                'success',
                'Tin nhắn đã được gửi đến MiniGrove.'
            );
    }
}