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
            Reserva::create([
                'user_id'           => auth()->id(),
                'tipo_ingresso_id'  => $request->tipo_ingresso_id,
                'nome_cliente'      => $request->nome_cliente,
                'whatsapp'          => $request->whatsapp,
                'quantidade'        => $request->quantidade,
                'total'             => $tipo->preco * $request->quantidade,
                'status'            => 'pendente',
                'comprovativo_path' => $path,
            ]);

            return redirect()->back()->with('success', 'Reserva enviada com sucesso! Aguarde a validação.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Erro ao salvar: ' . $e->getMessage()]);
        }
    }
}