<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.admin', function ($user) {
    return $user->role?->name === 'admin';
});

Broadcast::channel(
    'chat.conversation.{conversationId}',
    function ($user, $conversationId) {
        $conversation = Conversation::query()
            ->find($conversationId);

        if (!$conversation) {
            return false;
        }

        if ($user->role?->name === 'customer') {
            return (int) $conversation->customer_id
                === (int) $user->id;
        }

        if ($user->role?->name === 'admin') {
            return $conversation->assigned_admin_id === null
                || (int) $conversation->assigned_admin_id
                    === (int) $user->id;
        }

        return false;
    }
);
