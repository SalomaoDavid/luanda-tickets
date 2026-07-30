<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Conversation;
use App\Models\Pedido;
use App\Models\Bilhete;
use App\Models\TipoIngresso;
use App\Services\BilheteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\TicketPurchasedNotification;

class BookingController extends Controller
{
    /**
     * Confirma a reserva, gera o pedido, subtrai stock e emite bilhetes seguros.
     */
    public function confirmarReserva($id)
    {
        // Variáveis para usar fora da transacção
        $notificacaoReserva = null;

        DB::transaction(function () use ($id, &$notificacaoReserva) {

            $reserva = Reserva::with([
                'tipoIngresso:id,evento_id,nome,preco,quantidade_disponivel',
                'tipoIngresso.evento:id,user_id,titulo',
                'tipoIngresso.evento.user:id,name',
                'user:id,name,email',
            ])->findOrFail($id);

            $evento       = $reserva->tipoIngresso->evento;
            $tipoIngresso = $reserva->tipoIngresso;

            // ── Verificação de permissão ──
            if (auth()->user()->role !== 'admin' && $evento->user_id !== auth()->id()) {
                abort(403, 'Ação não autorizada.');
            }

            // ── Verificação de stock ──
            if ($tipoIngresso->quantidade_disponivel < $reserva->quantidade) {
                throw new \Exception("Não há bilhetes suficientes. Disponíveis: {$tipoIngresso->quantidade_disponivel}, pedido: {$reserva->quantidade}.");
            }

            // 1. Atualiza status da reserva
            $reserva->status = 'pago';
            $reserva->save();

            // 2. Subtrai stock
            TipoIngresso::where('id', $tipoIngresso->id)
                ->decrement('quantidade_disponivel', $reserva->quantidade);

            // 3. Cria o pedido financeiro
            $pedido = new Pedido();
            $pedido->user_id          = $reserva->user_id;
            $pedido->total_pago       = $reserva->total;
            $pedido->metodo_pagamento = 'Transferencia';
            $pedido->status           = 'pago';
            $pedido->comprovativo_path = $reserva->comprovativo_path; // atribuição directa
            $pedido->save();

            // 4. Emite bilhetes
            BilheteService::emitirLote(
                pedidoId:       $pedido->id,
                eventoId:       $evento->id,
                tipoIngressoId: $reserva->tipo_ingresso_id,
                userId:         $reserva->user_id ?? auth()->id(),
                quantidade:     $reserva->quantidade,
                total:          $reserva->total,
                ip:             request()->ip()
            );

            // 5. Registar divisão de saldos
            \App\Http\Controllers\SaldoController::registarDivisao($reserva);

            // 6. Chat
            $conversation = Conversation::where('evento_id', $evento->id)
                ->whereHas('users', function ($q) use ($reserva) {
                    if ($reserva->user_id) {
                        $q->where('users.id', $reserva->user_id);
                    }
                })->first();

            if ($conversation) {
                if ($reserva->user_id) {
                    $conversation->users()->syncWithoutDetaching([$reserva->user_id]);
                }
                $conversation->users()->syncWithoutDetaching([$evento->user_id]);
                $conversation->messages()->create([
                    'user_id' => $evento->user_id,
                    'body'    => "Olá! O seu pagamento para o evento '{$evento->titulo}' foi confirmado. Os seus bilhetes já estão disponíveis!",
                ]);
            }

            // Guardar referência para notificação fora da transacção
            $notificacaoReserva = $reserva;
        });

        // Notificação FORA da transacção
        if ($notificacaoReserva && $notificacaoReserva->user_id) {
            $notificacaoReserva->load('tipoIngresso.evento.user');
            if ($notificacaoReserva->tipoIngresso->evento->user_id !== $notificacaoReserva->user_id) {
                $notificacaoReserva->tipoIngresso->evento->user->notify(
                    TicketPurchasedNotification::fromReserva($notificacaoReserva)
                );
            }
        }

        return redirect()->back()->with('success', 'Pagamento confirmado e bilhetes gerados com sucesso!');
    }

    /**
     * Lista reservas pendentes para Admin ou Criador.
     */
    public function adminReservas()
    {
        $user = auth()->user();

        $query = Reserva::where('status', 'pendente');

        if ($user->role !== 'admin') {
            $query->whereHas('tipoIngresso.evento', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $reservas = $query->with([
            'tipoIngresso:id,evento_id,nome,preco',
            'tipoIngresso.evento:id,user_id,titulo',
            'user:id,name,email',
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        return view('admin-reservas', compact('reservas'));
    }

    /**
     * Lista reservas pagas (histórico).
     */
    public function adminPagos()
    {
        $user = auth()->user();

        $query = Reserva::where('status', 'pago');

        if ($user->role !== 'admin') {
            $query->whereHas('tipoIngresso.evento', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $pagamentos = $query->with([
            'tipoIngresso:id,evento_id,nome,preco',
            'tipoIngresso.evento:id,user_id,titulo',
            'user:id,name,email',
        ])
        ->orderBy('updated_at', 'desc')
        ->paginate(20);

        return view('admin-pagos', compact('pagamentos'));
    }

    /**
     * Elimina uma reserva pendente.
     */
    public function eliminarReserva($id)
    {
        $reserva = Reserva::findOrFail($id);

        $evento = $reserva->tipoIngresso?->evento;
        if (auth()->user()->role !== 'admin' && $evento?->user_id !== auth()->id()) {
            abort(403, 'Ação não autorizada.');
        }

        $reserva->delete();

        return redirect()->back()->with('success', 'Reserva eliminada.');
    }
}