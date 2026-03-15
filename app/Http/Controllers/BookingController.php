<?php
namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Notifications\TicketPurchasedNotification;

class BookingController extends Controller
{
    // ... outros métodos (adminReservas, adminPagos) ...

    public function confirmarReserva($id)
        {
            $reserva = Reserva::with(['tipoIngresso.evento', 'user'])->findOrFail($id);

            if (auth()->user()->role !== 'admin' && $reserva->tipoIngresso->evento->user_id !== auth()->id()) {
                abort(403);
            }

            $reserva->status = 'pago';
            $reserva->save();

            $evento = $reserva->tipoIngresso->evento;

            // Notifica o criador do evento que alguém comprou bilhete
            if ($reserva->user_id && $evento->user_id !== $reserva->user_id) {
                $evento->user->notify(new TicketPurchasedNotification($reserva));
            }

            // Chat automático
            $conversation = Conversation::firstOrCreate(
                ['evento_id' => $evento->id]
            );

            if ($reserva->user_id) {
                $conversation->users()->syncWithoutDetaching([$reserva->user_id]);
            }

            $conversation->users()->syncWithoutDetaching([$evento->user_id]);

            $conversation->messages()->create([
                'user_id' => $evento->user_id,
                'body'    => "Olá! O seu pagamento para '{$evento->titulo}' foi confirmado. Este chat é o seu canal direto com a organização!"
            ]);

            return redirect()->back()->with('success', 'Pagamento confirmado e Chat liberado!');
        }

public function adminReservas()
{
    $user = auth()->user();
    
    // Inicia a query buscando reservas pendentes
    $query = \App\Models\Reserva::where('status', 'pendente');

    // Se não for admin, filtra para mostrar apenas reservas dos eventos deste criador
    if ($user->role !== 'admin') {
        $query->whereHas('tipoIngresso.evento', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    $reservas = $query->with(['tipoIngresso.evento', 'user'])
                      ->orderBy('created_at', 'desc')
                      ->get();

    return view('admin-reservas', compact('reservas'));
}

}
