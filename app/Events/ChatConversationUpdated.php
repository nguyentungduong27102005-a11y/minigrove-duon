<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatConversationUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Conversation $conversation
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                'chat.conversation.'
                . $this->conversation->id
            ),
            new PrivateChannel('chat.admin'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.conversation.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation' => [
                'id' => $this->conversation->id,
                'customer_id' =>
                    $this->conversation->customer_id,
                'assigned_admin_id' =>
                    $this->conversation->assigned_admin_id,
                'topic' => $this->conversation->topic,
                'subject' => $this->conversation->subject,
                'status' => $this->conversation->status,
                'last_message_at' =>
                    $this->conversation
                        ->last_message_at
                        ?->toIso8601String(),
                'last_message_at_label' =>
                    $this->conversation
                        ->last_message_at
                        ?->format('d/m/Y H:i'),
                'closed_at' =>
                    $this->conversation
                        ->closed_at
                        ?->toIso8601String(),
            ],
        ];
    }
}
