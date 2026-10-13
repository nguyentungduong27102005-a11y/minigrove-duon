<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\Message;
use App\Support\ChatMessagePayload;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Message $message,
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
        return 'chat.message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => ChatMessagePayload::make(
                $this->message
            ),
            'conversation' =>
                $this->conversationPayload(),
        ];
    }

    private function conversationPayload(): array
    {
        return [
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
        ];
    }
}
