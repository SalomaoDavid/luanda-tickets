<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Conversation;
use App\Models\Pedido;
use App\Models\Bilhete;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Notifications\TicketPurchasedNotification;

class BookingController extends Controller
{
    /**
     * Confirma a reserva, gera o pedido e os bilhetes individuais.
     */
    public function confirmarReserva($id)
    {
        return DB::transaction(function () use ($id) {

            // ✅ Select específico nas relações — só colunas necessárias
            $reserva = Reserva::with([
                'tipoIngresso:id,evento_id,nome,preco',
                'tipoIngresso.evento:id,user_id,titulo',
                'tipoIngresso.evento.user:id,name',
                'user:id,name,email'
            ])->findOrFail($id);

            $evento = $reserva->tipoIngresso->evento;

            // Verificação de permissão — lógica intacta
            if (auth()->user()->role !== 'admin' && $evento->user_id !== auth()->id()) {
                abort(403, 'Ação não autorizada.');
            }

            // 1. Atualizar Status — usando updateQuietly (sem disparar eventos desnecessários)
            $reserva->updateQuietly(['status' => 'pago']);

            // 2. Criar o Pedido Financeiro — lógica intacta
            $pedido = Pedido::create([
                'user_id'           => $reserva->user_id,
                'total_pago'        => $reserva->total,
                'metodo_pagamento'  => 'Transferencia',
                'status'            => 'pago',
                'comprovativo_path' => $reserva->comprovativo_path
            ]);

            // 3. Gerar Bilhetes em batch (insert único em vez de N inserts)
            $bilhetes = [];
            for ($i = 0; $i < $reserva->quantidade; $i++) {
                $bilhetes[] = [
                    'pedido_id'         => $pedido->id,
                    'evento_id'         => $evento->id,
                    'tipo_ingressos_id' => $reserva->tipo_ingresso_id,
                    'codigo_unico'      => (string) Str::uuid(),
                    'validado_em'       => null,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }
            Bilhete::insert($bilhetes); // ✅ 1 query em vez de N queries

            // 4. Notificação — lógica intacta
            if ($reserva->user_id && $evento->user_id !== $reserva->user_id) {
                $evento->user->notify(new TicketPurchasedNotification($reserva));
            }

            // 5. Lógica de Chat — lógica intacta
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
                    'body'    => "Olá! O seu pagamento para o evento '{$evento->titulo}' foi confirmado com sucesso. Seus bilhetes já estão disponíveis no seu perfil!"
                ]);
            }

            return redirect()->back()->with('success', 'Pagamento confirmado e bilhetes gerados com sucesso!');
        });
    }

    /**
     * Lista as reservas pendentes para o Admin ou Criador do Evento.
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

        // ✅ Select específico — sem carregar colunas desnecessárias
        $reservas = $query->with([
            'tipoIngresso:id,evento_id,nome,preco',
            'tipoIngresso.evento:id,user_id,titulo',
            'user:id,name,email'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('admin-reservas', compact('reservas'));
    }

    /**
     * Lista as reservas já pagas (Histórico).
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

        // ✅ Select específico — sem carregar colunas desnecessárias
        $reservas = $query->with([
            'tipoIngresso:id,evento_id,nome,preco',
            'tipoIngresso.evento:id,user_id,titulo',
            'user:id,name,email'
        ])
        ->orderBy('updated_at', 'desc')
        ->get();

        return view('admin-pagos', compact('reservas'));
    }
}