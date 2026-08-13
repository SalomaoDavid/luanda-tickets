<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bilhete;
use App\Models\Evento;
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

        // ── 3. Criador só pode validar bilhetes dos seus eventos ──
        if (auth()->user()->role === 'creator') {
            $evento = Evento::select('id', 'user_id')->find($bilhete->evento_id);

            if (!$evento || $evento->user_id !== auth()->id()) {
                // Regista tentativa não autorizada na auditoria
                BilheteService::registarAuditoria(
                    $bilhete->id,
                    'acesso_negado',
                    $codigo,
                    ['motivo' => 'Criador tentou validar bilhete de evento alheio', 'criador_id' => auth()->id()]
                );

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Não tens permissão para validar bilhetes deste evento.',
                ], 403);
            }
        }

        // ── 4. Verifica HMAC (integridade) ──
        if (!BilheteService::verificarHmac($bilhete)) {
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

        // ── 5. Bilhete já bloqueado ──
        // Se já estava bloqueado antes desta tentativa → inválido sem mais detalhes
        if ($bilhete->bloqueado) {
            BilheteService::registarAuditoria(
                $bilhete->id,
                'tentativa_apos_bloqueio',
                $codigo,
                ['motivo' => 'Tentativa de uso após bloqueio']
            );

            return response()->json([
                'status'  => 'error',
                'message' => 'Bilhete inválido.',
            ], 200);
        }

        // ── 6. Bilhete já utilizado — bloquear e registar ──
        if ($bilhete->validado_em) {
            // Bloqueia imediatamente ao tentar reutilizar
            $bilhete->updateQuietly([
                'bloqueado' => true,
            ]);

            $motivo = 'Bilhete já utilizado em '
                . \Carbon\Carbon::parse($bilhete->validado_em)->format('d/m/Y H:i')
                . '. Bloqueado por tentativa de reutilização.';

            BilheteService::registarAuditoria(
                $bilhete->id,
                'bloqueado_reuso',
                $codigo,
                [
                    'motivo'       => $motivo,
                    'validado_em'  => $bilhete->validado_em,
                    'bloqueado_em' => now(),
                ]
            );

            return response()->json([
                'status'  => 'warning',
                'message' => $motivo,
            ], 200);
        }

        // ── 7. Verifica outras condições (HMAC já verificado, pagamento, etc.) ──
        $apto = $bilhete->aptoParaEntrada();

        if (!$apto['apto']) {
            return response()->json([
                'status'  => 'error',
                'message' => $apto['motivo'],
            ], 200);
        }

        // ── 8. Valida o bilhete ──
        $bilhete->updateQuietly(['validado_em' => now()]);

        // ── 9. Regista na auditoria ──
        $bilhete->loadMissing([
            'pedido.user:id,name',
            'evento:id,titulo,localizacao,data_evento,hora_inicio,hora_fim,user_id',
            'tipoIngresso:id,nome,preco',
        ]);

        BilheteService::registarAuditoria(
            $bilhete->id,
            'validado',
            $codigo,
            [
                'cliente' => $bilhete->pedido->user->name ?? 'Convidado',
                'evento'  => $bilhete->evento->titulo ?? '',
                'tipo'    => $bilhete->tipoIngresso->nome ?? '',
                'lote_id' => $bilhete->lote_id,
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