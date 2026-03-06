<?php
namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    // ... outros métodos (adminReservas, adminPagos) ...

    public function confirmarReserva($id)
    {
        // Carrega a reserva com o evento e o usuário que comprou
        $reserva = Reserva::with(['tipoIngresso.evento', 'user'])->findOrFail($id);
        
        if (auth()->user()->role !== 'admin' && $reserva->tipoIngresso->evento->user_id !== auth()->id()) {
            abort(403);
        }

        $reserva->status = 'pago'; 
        $reserva->save();

        // --- ATIVAÇÃO DO CHAT AUTOMÁTICO ---
        $evento = $reserva->tipoIngresso->evento;

        // 1. Cria ou busca a conversa do evento
        $conversation = Conversation::firstOrCreate(
            ['evento_id' => $evento->id]
        );

        // 2. Adiciona o COMPRADOR à conversa (se ele estiver logado)
        if ($reserva->user_id) {
            $conversation->users()->syncWithoutDetaching([$reserva->user_id]);
        }

        // 3. Adiciona o ORGANIZADOR à conversa
        $conversation->users()->syncWithoutDetaching([$evento->user_id]);

        // 4. Mensagem automática de boas-vindas
        $conversation->messages()->create([
            'user_id' => $evento->user_id,
            'body' => "Olá! O seu pagamento para '{$evento->titulo}' foi confirmado. Este chat é o seu canal direto com a organização!"
        ]);

        return redirect()->back()->with('success', 'Pagamento confirmado e Chat liberado!');
    }

    public function adminPagos()
{
    $user = auth()->user();
    
    // Se for admin, vê tudo. Se for criador, vê apenas os pagos dos seus eventos.
    $query = \App\Models\Reserva::where('status', 'pago');

    if ($user->role !== 'admin') {
        $query->whereHas('tipoIngresso.evento', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    $pagamentos = $query->with(['tipoIngresso.evento', 'user'])
                        ->orderBy('updated_at', 'desc')
                        ->get();

    return view('admin-pagos', compact('pagamentos'));
}
}
