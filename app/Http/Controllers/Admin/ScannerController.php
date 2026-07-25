<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bilhete;
use App\Services\BilheteService;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function index()
    {
        return view('admin.scanner-visual');
    }

    public function validar(Request $request)
    {
        $codigo = trim($request->codigo ?? '');

        if (empty($codigo)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Código não fornecido.',
            ], 400);
        }

        // ── 1. Busca o bilhete ──
        $bilhete = Bilhete::select(
                'id','codigo_unico','validado_em','pedido_id',
                'evento_id','tipo_ingressos_id','hmac_assinatura',
                'lote_id','tentativas_invalidas','bloqueado'
            )
            ->where('codigo_unico', $codigo)
            ->first();

        // ── 2. Bilhete não existe ──
        if (!$bilhete) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bilhete inválido ou inexistente.',
            ], 404);
        }

        // ── 3. Verifica HMAC (integridade) ──
        if (!BilheteService::verificarHmac($bilhete)) {
            // Regista tentativa inválida
            $bilhete->registarTentativaInvalida();

            BilheteService::registarAuditoria(
                $bilhete->id,
                'hmac_invalido',
                $codigo,
                ['motivo' => 'Assinatura HMAC não coincide — possível adulteração']
            );

            return response()->json([
                'status'  => 'error',
                'message' => 'Bilhete adulterado ou inválido. Incidente registado.',
            ], 400);
        }

        // ── 4. Verifica se está apto para entrada ──
        $apto = $bilhete->aptoParaEntrada();

        if (!$apto['apto']) {
            // Regista tentativa se bilhete já foi usado
            if ($bilhete->validado_em) {
                BilheteService::registarAuditoria(
                    $bilhete->id,
                    'tentativa_reuso',
                    $codigo,
                    ['validado_em' => $bilhete->validado_em]
                );
            }

            return response()->json([
                'status'  => $bilhete->validado_em ? 'warning' : 'error',
                'message' => $apto['motivo'],
            ], 200);
        }

        // ── 5. Valida o bilhete (updateQuietly — trigger MySQL protege campos críticos) ──
        $bilhete->updateQuietly(['validado_em' => now()]);

        // ── 6. Regista na auditoria ──
        $bilhete->loadMissing([
            'pedido.user:id,name',
            'evento:id,titulo,localizacao,data_evento,hora_inicio,hora_fim',
            'tipoIngresso:id,nome,preco',
        ]);

        BilheteService::registarAuditoria(
            $bilhete->id,
            'validado',
            $codigo,
            [
                'cliente'  => $bilhete->pedido->user->name ?? 'Convidado',
                'evento'   => $bilhete->evento->titulo ?? '',
                'tipo'     => $bilhete->tipoIngresso->nome ?? '',
                'lote_id'  => $bilhete->lote_id,
            ],
            auth()->id()
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Entrada Liberada! Bem-vindo ao evento.',
            'cliente' => $bilhete->pedido->user->name ?? 'Convidado',
            'evento'  => $bilhete->evento->titulo ?? '',
            'local'   => $bilhete->evento->localizacao ?? '',
            'data'    => $bilhete->evento->data_evento
                            ? \Carbon\Carbon::parse($bilhete->evento->data_evento)->translatedFormat('d \d\e F \d\e Y')
                            : '',
            'hora'    => $bilhete->evento->hora_inicio
                            ? \Illuminate\Support\Str::substr($bilhete->evento->hora_inicio, 0, 5)
                              . ($bilhete->evento->hora_fim ? ' – ' . \Illuminate\Support\Str::substr($bilhete->evento->hora_fim, 0, 5) : '')
                            : '',
            'tipo'    => $bilhete->tipoIngresso->nome ?? '',
            'preco'   => $bilhete->tipoIngresso
                            ? number_format($bilhete->tipoIngresso->preco, 0, ',', '.') . ' Kz'
                            : '',
            'lote'    => $bilhete->lote_id,
            'codigo'  => substr($codigo, 0, 13) . '…',
        ]);
    }
}