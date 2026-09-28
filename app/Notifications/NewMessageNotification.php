<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int    $messageId,
        public int    $conversationId,
        public int    $senderId,
        public string $senderName,
        public ?string $senderPhoto,
        public string $preview,
    ) {}

    public static function fromMessage(\App\Models\Message $message): self
    {
        return new self(
            messageId:      $message->id,
            conversationId: $message->conversation_id,
            senderId:       $message->user_id,
            senderName:     $message->user->name,
            senderPhoto:    $message->user->avatar_url ?? null,
            preview:        str($message->body)->limit(60),
        );
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', \App\Channels\WebPushChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message_id'      => $this->messageId,
            'conversation_id' => $this->conversationId,
            'sender_id'       => $this->senderId,
            'sender_name'     => $this->senderName,
            'sender_photo'    => $this->senderPhoto,
            'preview'         => $this->preview,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => $this->senderName,
            'body'  => $this->preview,
            'url'   => \App\Support\NotificationLinks::mensagem($this->senderId),
            'icon'  => $this->senderPhoto ? asset('storage/'.$this->senderPhoto) : asset('logos.png'),
        ];
    }
}