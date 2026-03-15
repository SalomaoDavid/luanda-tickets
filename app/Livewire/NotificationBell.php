<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Notifications\DatabaseNotification;

class NotificationBell extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $open = false;

    public function getListeners()
    {
        $userId = auth()->id();
        return [
            "echo-private:App.Models.User.{$userId},Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'onNewNotification',
        ];
    }

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = auth()->user();
        $this->unreadCount = $user->unreadNotifications()->count();

        // Para mensagens, mostra apenas a mais recente por conversa
        $all = $user->notifications()->latest()->get();

        $seen = [];
        $filtered = [];

        foreach ($all as $notification) {
            if ($notification->type === 'App\Notifications\NewMessageNotification') {
                $convId = $notification->data['conversation_id'] ?? null;
                if ($convId && in_array($convId, $seen)) continue;
                if ($convId) $seen[] = $convId;
            }
            $filtered[] = $notification;
        }

        $this->notifications = collect($filtered)->take(10)->toArray();
    }

    public function onNewNotification($event)
    {
        $this->loadNotifications();
    }

    public function toggleOpen()
    {
        $this->open = !$this->open;
        if ($this->open) {
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }

    public function markAsRead($id)
    {
        $notification = DatabaseNotification::find($id);
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}