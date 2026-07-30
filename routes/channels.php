<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Canal privado de uma conversa de chat.
 * Só o remetente ou o destinatário da conversa podem subscrever.
 */
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (!$conversation) {
        return false;
    }

    return (int) $conversation->sender_id === (int) $user->id
        || (int) $conversation->receiver_id === (int) $user->id;
});