<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\TipoIngresso;
use App\Models\User;
use App\Notifications\NovoPedidoNotification;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tipo_ingresso_id' => 'required|exists:tipo_ingressos,id',
            'nome_cliente'     => 'required|string|max:255',
            'whatsapp'         => 'required|string',
            'quantidade'       => 'required|integer|min:1',
            'comprovativo'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        try {
            // ✅ Select específico — só colunas necessárias para calcular o total
            $tipo = TipoIngresso::select('id', 'preco', 'quantidade_disponivel')
                ->findOrFail($request->tipo_ingresso_id);

            // ✅ Verificação de disponibilidade antes de aceitar a reserva
            if ($tipo->quantidade_disponivel < $request->quantidade) {
                return redirect()->back()->withErrors([
                    'msg' => 'Não há bilhetes suficientes disponíveis.'
                ]);
            }

            // Salvar o ficheiro — lógica intacta
            $path = $request->file('comprovativo')->store('comprovativos', 'public');

            // Criar a reserva — lógica intacta
            $reserva = Reserva::create([
                'user_id'           => auth()->id(),
                'tipo_ingresso_id'  => $request->tipo_ingresso_id,
                'nome_cliente'      => $request->nome_cliente,
                'whatsapp'          => $request->whatsapp,
                'quantidade'        => $request->quantidade,
                'total'             => $tipo->preco * $request->quantidade,
                'status'            => 'pendente',
                'comprovativo_path' => $path,
            ]);

            // Notifica todos os admins de que há um novo pedido a aguardar aprovação
            $reserva->load(['tipoIngresso.evento', 'user:id,name']);
            $notificacaoAdmin = NovoPedidoNotification::fromReserva($reserva);
            User::where('role', 'admin')->get()->each(
                fn ($admin) => $admin->notify($notificacaoAdmin)
            );

            return redirect()->back()->with(
                'success',
                'O seu pedido foi enviado para o admin, receberá uma notificação quando for aprovado pelo admin e verás no seu perfil o seu bilhete.'
            );

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Erro ao salvar: ' . $e->getMessage()]);
        }
    }
}