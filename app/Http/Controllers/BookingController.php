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
        return DB::transaction(function () use ($id) {

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

            // ── Verificação de stock (última linha de defesa antes de confirmar) ──
            if ($tipoIngresso->quantidade_disponivel < $reserva->quantidade) {
                return redirect()->back()->with('error',
                    "Não há bilhetes suficientes disponíveis. Disponíveis: {$tipoIngresso->quantidade_disponivel}, pedido: {$reserva->quantidade}."
                );
            }

            // 1. Atualiza status da reserva
            $reserva->updateQuietly(['status' => 'pago']);

            // 2. Subtrai a quantidade do stock
            //    decrement é atómico — sem race conditions
            //    O trigger MySQL garante que não fica negativo
            TipoIngresso::where('id', $tipoIngresso->id)
                ->decrement('quantidade_disponivel', $reserva->quantidade);

            // 3. Cria o pedido financeiro
            $pedido = Pedido::create([
                'user_id'           => $reserva->user_id,
                'total_pago'        => $reserva->total,
                'metodo_pagamento'  => 'Transferencia',
                'status'            => 'pago',
                'comprovativo_path' => $reserva->comprovativo_path,
            ]);

            // 4. Emite bilhetes com HMAC + lote + auditoria
            BilheteService::emitirLote(
                pedidoId:       $pedido->id,
                eventoId:       $evento->id,
                tipoIngressoId: $reserva->tipo_ingresso_id,
                userId:         $reserva->user_id ?? auth()->id(),
                quantidade:     $reserva->quantidade,
                total:          $reserva->total,
                ip:             request()->ip()
            );

            // 5. Notificação
            if ($reserva->user_id && $evento->user_id !== $reserva->user_id) {
                $evento->user->notify(new TicketPurchasedNotification($reserva));
            }

            // 6. Lógica de Chat — intacta
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
                    'body'    => "Olá! O seu pagamento para o evento '{$evento->titulo}' foi confirmado com sucesso. Os seus bilhetes já estão disponíveis no seu perfil!",
                ]);
            }

            return redirect()->back()->with('success', 'Pagamento confirmado e bilhetes gerados com sucesso!');
        });
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
        ->get();

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
        ->get();

        return view('admin-pagos', compact('pagamentos'));
    }

    /**
     * Elimina uma reserva pendente.
     */
    public function eliminarReserva($id)
    {
        $reserva = Reserva::findOrFail($id);

        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $reserva->delete();

        return redirect()->back()->with('success', 'Reserva eliminada.');
    }
}