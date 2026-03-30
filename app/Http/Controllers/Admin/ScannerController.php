<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bilhete; // ← model correto
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function index()
    {
        return view('admin.scanner-visual');
    }

    public function validar(Request $request)
    {
        $codigo = $request->codigo;

        $bilhete = Bilhete::select('id', 'codigo_unico', 'validado_em', 'pedido_id', 'evento_id', 'tipo_ingressos_id')
            ->where('codigo_unico', $codigo)
            ->first();

        if (!$bilhete) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bilhete Inválido ou Inexistente!'
            ], 404);
        }

        if ($bilhete->validado_em) {
            return response()->json([
                'status'  => 'warning',
                'message' => 'Este bilhete já foi usado em: ' . \Carbon\Carbon::parse($bilhete->validado_em)->format('d/m H:i')
            ], 200);
        }

        $pedido = $bilhete->pedido()->select('id', 'status', 'user_id')->first();

        if (!$pedido || $pedido->status !== 'pago') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pagamento não confirmado para este bilhete.'
            ], 400);
        }

        $bilhete->updateQuietly(['validado_em' => now()]);

        // ✅ Carrega mais relações para a resposta
        $bilhete->loadMissing([
            'pedido.user:id,name',
            'evento:id,titulo,localizacao,data_evento,hora_inicio,hora_fim',
            'tipoIngresso:id,nome,preco',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Entrada Liberada! Bem-vindo ao evento.',
            'cliente' => $bilhete->pedido->user->name ?? 'Convidado',
            'evento'  => $bilhete->evento->titulo     ?? '',
            'local'   => $bilhete->evento->localizacao ?? '',
            'data'    => $bilhete->evento->data_evento
                            ? \Carbon\Carbon::parse($bilhete->evento->data_evento)->translatedFormat('d \d\e F \d\e Y')
                            : '',
            'hora'    => $bilhete->evento->hora_inicio
                            ? \Illuminate\Support\Str::substr($bilhete->evento->hora_inicio, 0, 5)
                            . ($bilhete->evento->hora_fim ? ' – ' . \Illuminate\Support\Str::substr($bilhete->evento->hora_fim, 0, 5) : '')
                            : '',
            'tipo'    => $bilhete->tipoIngresso->nome  ?? '',
            'preco'   => $bilhete->tipoIngresso
                            ? number_format($bilhete->tipoIngresso->preco, 0, ',', '.') . ' Kz'
                            : '',
            'codigo'  => substr($codigo, 0, 13) . '…',
        ]);
    }
}