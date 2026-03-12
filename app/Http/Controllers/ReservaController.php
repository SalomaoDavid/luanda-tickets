<?php
namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\TipoIngresso;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validação (incluindo o arquivo)
        $request->validate([
            'tipo_ingresso_id' => 'required|exists:tipo_ingressos,id',
            'nome_cliente' => 'required|string|max:255',
            'whatsapp' => 'required|string',
            'quantidade' => 'required|integer|min:1',
            'comprovativo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $tipo = TipoIngresso::findOrFail($request->tipo_ingresso_id);
        $total = $tipo->preco * $request->quantidade;

        // 2. Upload do Comprovativo
        $path = $request->file('comprovativo')->store('comprovativos', 'public');

        // 3. Criar a Reserva
        Reserva::create([
            'user_id' => auth()->id(),
            'tipo_ingresso_id' => $request->tipo_ingresso_id,
            'nome_cliente' => $request->nome_cliente,
            'whatsapp' => $request->whatsapp,
            'quantidade' => $request->quantidade,
            'total' => $total,
            'comprovativo' => $path, // Coluna deve existir na tabela
            'status' => 'pendente'
        ]);

        // 4. Redirecionar para a página de reservas (conforme solicitado)
        // Verifique se o nome da rota da listagem é 'admin.reservas'
        return redirect()->route('admin.reservas')->with('success', 'Reserva enviada! Aguarde a confirmação.');
    }
}