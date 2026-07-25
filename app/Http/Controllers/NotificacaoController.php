<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Carbon\Carbon;

class NotificacaoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Busca todas as notificações agrupadas por data
        $todasNotificacoes = $user->notifications()->latest()->get();

        // Agrupa por período
        $grupos = [];
        foreach ($todasNotificacoes as $notif) {
            $data = $notif->created_at;

            if ($data->isToday()) {
                $grupo = 'Hoje';
            } elseif ($data->isYesterday()) {
                $grupo = 'Ontem';
            } elseif ($data->isCurrentWeek()) {
                $grupo = 'Esta semana';
            } elseif ($data->isCurrentMonth()) {
                $grupo = 'Este mês';
            } else {
                $grupo = $data->format('F Y');
            }

            $grupos[$grupo][] = $notif;
        }

        $totalNaoLidas = $user->unreadNotifications->count();

        return view('notificacoes', compact('grupos', 'totalNaoLidas'));
    }

    public function marcarLida(string $id)
    {
        $notif = DatabaseNotification::find($id);

        if ($notif && $notif->notifiable_id === auth()->id()) {
            $notif->markAsRead();
        }

        return response()->json(['ok' => true]);
    }

    public function marcarTodas()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'Todas as notificações marcadas como lidas.');
    }
}