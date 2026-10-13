<?php

namespace App\Services;

use App\Models\Message;

class AdminChatUnreadService
{
    /**
     * Đếm tin nhắn Customer chưa được Admin đọc
     * trong các hội thoại mà Admin hiện có thể xử lý.
     */
    public function countForAdmin(int $adminId): int
    {
        return Message::query()
            ->join(
                'conversations',
                'conversations.id',
                '=',
                'messages.conversation_id'
            )
            ->whereNull('messages.read_at')
            ->whereColumn(
                'messages.sender_id',
                'conversations.customer_id'
            )
            ->whereIn(
                'conversations.status',
                ['open', 'waiting']
            )
            ->where(
                function ($query) use ($adminId) {
                    $query
                        ->whereNull(
                            'conversations.assigned_admin_id'
                        )
                        ->orWhere(
                            'conversations.assigned_admin_id',
                            $adminId
                        );
                }
            )
            ->count('messages.id');
    }
}
