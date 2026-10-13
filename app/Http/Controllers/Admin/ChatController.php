<?php

namespace App\Http\Controllers\Admin;

use App\Events\ChatConversationUpdated;
use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use App\Services\AdminChatUnreadService;
use App\Services\ChatAttachmentService;
use App\Support\ChatMessagePayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Số tin nhắn Customer chưa đọc của Admin.
     */
    public function unreadCount(
        Request $request,
        AdminChatUnreadService $service
    ): JsonResponse {
        return response()->json([
            'unread_count' =>
                $service->countForAdmin(
                    (int) $request->user()->id
                ),
        ]);
    }

    /**
     * Danh sách hội thoại Customer cần Admin xử lý.
     *
     * Privacy:
     * - Không load thông tin PII của Customer.
     * - Admin chỉ sử dụng Customer #ID.
     */
    public function index(Request $request): View
    {
        $adminId = $request->user()->id;

        $conversations = Conversation::query()
            ->whereIn(
                'status',
                [
                    'open',
                    'waiting',
                ]
            )
            ->where(function ($query) use ($adminId) {
                $query
                    ->whereNull('assigned_admin_id')
                    ->orWhere(
                        'assigned_admin_id',
                        $adminId
                    );
            })
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
            ->orderByRaw(
                'CASE WHEN assigned_admin_id = ? THEN 0 ELSE 1 END',
                [$adminId]
            )
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view(
            'admin.chat.index',
            compact('conversations')
        );
    }

    /**
     * Chi tiết một hội thoại.
     *
     * Admin chỉ được xem:
     * - Hội thoại chưa được nhận;
     * - Hoặc hội thoại do chính Admin đó nhận.
     */
    public function show(
        Request $request,
        int $conversation
    ): View {
        $adminId = $request->user()->id;

        $conversation = Conversation::query()
            ->whereKey($conversation)
            ->whereIn(
                'status',
                [
                    'open',
                    'waiting',
                ]
            )
            ->where(function ($query) use ($adminId) {
                $query
                    ->whereNull('assigned_admin_id')
                    ->orWhere(
                        'assigned_admin_id',
                        $adminId
                    );
            })
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
            ->firstOrFail();

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

        $products = Product::query()
            ->where('is_active', true)
            ->with([
                'images' => function ($query) {
                    $query
                        ->select([
                            'id',
                            'product_id',
                            'image_path',
                            'is_primary',
                            'sort_order',
                        ])
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])
            ->select([
                'id',
                'name',
                'sku',
                'list_price',
                'sale_price',
                'stock_quantity',
            ])
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Đánh dấu tin nhắn Customer đã đọc
        |--------------------------------------------------------------------------
        */

        $markedRead = Message::query()
            ->where(
                'conversation_id',
                $conversation->id
            )
            ->where(
                'sender_id',
                $conversation->customer_id
            )
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        if ($markedRead > 0) {
            ChatConversationUpdated::dispatch(
                $conversation->fresh()
            );
        }

        return view(
            'admin.chat.show',
            compact(
                'conversation',
                'messages',
                'products'
            )
        );
    }

    /**
     * Đánh dấu tin nhắn Customer đã đọc khi Admin
     * đang mở hội thoại và nhận tin mới qua Reverb.
     */
    public function markRead(
        Request $request,
        int $conversation,
        AdminChatUnreadService $service
    ): JsonResponse {
        $adminId = (int) $request->user()->id;

        $conversation = Conversation::query()
            ->whereKey($conversation)
            ->whereIn(
                'status',
                ['open', 'waiting']
            )
            ->where(
                function ($query) use ($adminId) {
                    $query
                        ->whereNull(
                            'assigned_admin_id'
                        )
                        ->orWhere(
                            'assigned_admin_id',
                            $adminId
                        );
                }
            )
            ->firstOrFail();

        $markedRead = Message::query()
            ->where(
                'conversation_id',
                $conversation->id
            )
            ->where(
                'sender_id',
                $conversation->customer_id
            )
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        if ($markedRead > 0) {
            ChatConversationUpdated::dispatch(
                $conversation->fresh()
            );
        }

        return response()->json([
            'success' => true,
            'unread_count' =>
                $service->countForAdmin(
                    $adminId
                ),
        ]);
    }

    /**
     * Admin nhận xử lý hội thoại.
     */
    public function accept(
        Request $request,
        int $conversation
    ): RedirectResponse|JsonResponse {
        $adminId = $request->user()->id;

        $updatedConversation = DB::transaction(
            function () use (
                $conversation,
                $adminId
            ) {
                $lockedConversation = Conversation::query()
                    ->whereKey($conversation)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_if(
                    $lockedConversation->status === 'closed',
                    404
                );

                abort_if(
                    $lockedConversation->assigned_admin_id !== null
                    && (int) $lockedConversation->assigned_admin_id
                    !== (int) $adminId,
                    409,
                    'Hội thoại đã được Admin khác nhận.'
                );

                if (
                    $lockedConversation->assigned_admin_id
                    === null
                ) {
                    $lockedConversation->update([
                        'assigned_admin_id' => $adminId,
                    ]);
                }

                return $lockedConversation->fresh();
            }
        );

        ChatConversationUpdated::dispatch(
            $updatedConversation
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'conversation' => [
                    'id' => $updatedConversation->id,
                    'status' => $updatedConversation->status,
                    'assigned_admin_id' =>
                        $updatedConversation
                            ->assigned_admin_id,
                ],
            ]);
        }

        return redirect()
            ->route(
                'admin.chat.show',
                $conversation
            )
            ->with(
                'success',
                'Bạn đã nhận xử lý hội thoại.'
            );
    }

    /**
     * Admin trả lời Customer.
     *
     * Admin phải nhận hội thoại trước khi trả lời.
     */
    public function reply(
        Request $request,
        int $conversation,
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
                'product_ids' => [
                    'nullable',
                    'array',
                    'max:3',
                ],
                'product_ids.*' => [
                    'integer',
                    'distinct',
                    Rule::exists('products', 'id')
                        ->where('is_active', true),
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
                'product_ids.max' =>
                    'Mỗi lần chỉ được gửi tối đa 3 sản phẩm.',
                'product_ids.*.distinct' =>
                    'Không thể chọn trùng sản phẩm.',
                'product_ids.*.exists' =>
                    'Sản phẩm đã chọn không còn khả dụng.',
            ]
        );

        $adminId = $request->user()->id;
        $images = $request->file('images', []);
        $productIds = array_values(
            $validated['product_ids'] ?? []
        );
        $content = trim(
            (string) ($validated['content'] ?? '')
        );

        $selectedProducts = collect();

        if ($productIds !== []) {
            $productsById = Product::query()
                ->whereIn('id', $productIds)
                ->where('is_active', true)
                ->with([
                    'images' => function ($query) {
                        $query
                            ->select([
                                'id',
                                'product_id',
                                'image_path',
                                'is_primary',
                                'sort_order',
                            ])
                            ->orderByDesc('is_primary')
                            ->orderBy('sort_order')
                            ->orderBy('id');
                    },
                ])
                ->get([
                    'id',
                    'name',
                    'sku',
                    'list_price',
                    'sale_price',
                ])
                ->keyBy('id');

            $selectedProducts = collect($productIds)
                ->map(
                    fn ($productId) =>
                        $productsById->get((int) $productId)
                )
                ->filter()
                ->values();
        }

        if (
            $content === ''
            && $images === []
            && $selectedProducts->isEmpty()
        ) {
            throw ValidationException::withMessages([
                'content' =>
                    'Vui lòng nhập phản hồi, chọn ảnh hoặc chọn sản phẩm.',
            ]);
        }

        $hasProducts = $selectedProducts->isNotEmpty();
        $hasImages = $images !== [];
        $hasText = $content !== '';

        $messageType = match (true) {
            ($hasText && ($hasImages || $hasProducts)),
            ($hasImages && $hasProducts) => 'MIXED',
            $hasImages => 'IMAGE',
            $hasProducts => 'PRODUCT',
            default => 'TEXT',
        };

        [$updatedConversation, $message] = DB::transaction(
            function () use (
                $conversation,
                $adminId,
                $content,
                $images,
                $selectedProducts,
                $messageType,
                $attachmentService
            ) {
                $lockedConversation = Conversation::query()
                    ->whereKey($conversation)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_if(
                    $lockedConversation->status === 'closed',
                    404
                );

                abort_unless(
                    (int) $lockedConversation->assigned_admin_id
                    === (int) $adminId,
                    403,
                    'Bạn cần nhận hội thoại trước khi phản hồi.'
                );

                $sentAt = now();

                $message = Message::create([
                    'conversation_id' =>
                        $lockedConversation->id,

                    'sender_id' =>
                        $adminId,

                    'content' =>
                        $content !== ''
                            ? $content
                            : null,

                    'message_type' =>
                        $messageType,

                    'sent_at' =>
                        $sentAt,

                    'read_at' =>
                        null,
                ]);

                if ($images !== []) {
                    $attachmentService->attachImages(
                        $message,
                        $images
                    );
                }

                foreach (
                    $selectedProducts as $index => $product
                ) {
                    $productImage =
                        $product->images->first();

                    $message->productCards()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'list_price' => $product->list_price,
                        'sale_price' => $product->sale_price,
                        'image_path' =>
                            $productImage?->image_path,
                        'sort_order' => $index,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | waiting = Admin đã phản hồi, đang chờ Customer
                |--------------------------------------------------------------------------
                */

                $lockedConversation->update([
                    'status' =>
                        'waiting',

                    'last_message_at' =>
                        $sentAt,
                ]);

                return [
                    $lockedConversation->fresh(),
                    $message->fresh()->load([
                        'attachments',
                        'productCards',
                    ]),
                ];
            }
        );

        ChatMessageSent::dispatch(
            $message,
            $updatedConversation
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => ChatMessagePayload::make(
                    $message
                ),
                'conversation' => [
                    'id' => $updatedConversation->id,
                    'status' => $updatedConversation->status,
                    'assigned_admin_id' =>
                        $updatedConversation
                            ->assigned_admin_id,
                ],
            ]);
        }

        return redirect()
            ->route(
                'admin.chat.show',
                $conversation
            )
            ->with(
                'success',
                'Đã gửi phản hồi cho Customer.'
            );
    }

    /**
     * Admin đóng hội thoại.
     */
    public function close(
        Request $request,
        int $conversation
    ): RedirectResponse|JsonResponse {
        $adminId = $request->user()->id;

        $updatedConversation = DB::transaction(
            function () use (
                $conversation,
                $adminId
            ) {
                $lockedConversation = Conversation::query()
                    ->whereKey($conversation)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_if(
                    $lockedConversation->status === 'closed',
                    404
                );

                abort_unless(
                    (int) $lockedConversation->assigned_admin_id
                    === (int) $adminId,
                    403,
                    'Bạn chỉ có thể đóng hội thoại do mình phụ trách.'
                );

                $lockedConversation->update([
                    'status' =>
                        'closed',

                    'closed_by' =>
                        $adminId,

                    'closed_at' =>
                        now(),
                ]);

                return $lockedConversation->fresh();
            }
        );

        ChatConversationUpdated::dispatch(
            $updatedConversation
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'conversation' => [
                    'id' => $updatedConversation->id,
                    'status' => $updatedConversation->status,
                    'assigned_admin_id' =>
                        $updatedConversation
                            ->assigned_admin_id,
                ],
            ]);
        }

        return redirect()
            ->route('admin.chat.index')
            ->with(
                'success',
                'Hội thoại đã được đóng.'
            );
    }
}