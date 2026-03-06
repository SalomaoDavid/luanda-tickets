<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\TipoIngresso;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tipo_ingresso_id' => 'required|exists:tipo_ingressos,id',
            'nome_cliente' => 'required|string|max:255',
            'whatsapp' => 'required|string',
            'quantidade' => 'required|integer|min:1',
        ]);

        $tipo = TipoIngresso::findOrFail($request->tipo_ingresso_id);
        $total = $tipo->preco * $request->quantidade;

        Reserva::create([
            'user_id' => auth()->id(), // Vincula a conta logada à reserva
            'tipo_ingresso_id' => $request->tipo_ingresso_id,
            'nome_cliente' => $request->nome_cliente,
            'whatsapp' => $request->whatsapp,
            'quantidade' => $request->quantidade,
            'total' => $total,
            'status' => 'pendente'
        ]);

        return back()->with('sucesso', 'Reserva efetuada com sucesso! Entraremos em contacto via WhatsApp.');
    }
}
