<?php

namespace App\Services;

use App\Models\Bilhete;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BilheteService
{
    /**
     * Gera a assinatura HMAC para um bilhete.
     * Usa APP_KEY como segredo — nunca exposto ao cliente.
     */
    public static function gerarHmac(array $dados): string
    {
        $payload = implode('|', [
            $dados['codigo_unico'],
            $dados['pedido_id'],
            $dados['evento_id'],
            $dados['tipo_ingressos_id'],
            $dados['lote_id'],
        ]);

        return hash_hmac('sha256', $payload, config('app.key'));
    }

    /**
     * Verifica se a assinatura HMAC de um bilhete é válida.
     */
    public static function verificarHmac(Bilhete $bilhete): bool
    {
        if (!$bilhete->hmac_assinatura) {
            return false;
        }

        $hmacEsperado = self::gerarHmac([
            'codigo_unico'      => $bilhete->codigo_unico,
            'pedido_id'         => $bilhete->pedido_id,
            'evento_id'         => $bilhete->evento_id,
            'tipo_ingressos_id' => $bilhete->tipo_ingressos_id,
            'lote_id'           => $bilhete->lote_id,
        ]);

        // hash_equals previne timing attacks
        return hash_equals($hmacEsperado, $bilhete->hmac_assinatura);
    }

    /**
     * Gera um ID de lote único: LT-{data}-{random}
     */
    public static function gerarLoteId(): string
    {
        return 'LT-' . now()->format('ymd') . '-' . strtoupper(Str::random(6));
    }

    /**
     * Emite bilhetes em lote com HMAC e registo de auditoria.
     * Substitui o Bilhete::insert() simples do BookingController.
     */
    public static function emitirLote(
        int $pedidoId,
        int $eventoId,
        int $tipoIngressoId,
        int $userId,
        int $quantidade,
        float $total,
        string $ip = null
    ): array {
        $loteId = self::gerarLoteId();

        // Regista o lote
        DB::table('bilhetes_lotes')->insert([
            'lote_id'       => $loteId,
            'reserva_id'    => $pedidoId,
            'evento_id'     => $eventoId,
            'user_id'       => $userId,
            'quantidade'    => $quantidade,
            'total'         => $total,
            'emitido_em'    => now(),
            'emitido_por_ip'=> $ip ?? request()->ip(),
        ]);

        $bilhetes  = [];
        $auditorias = [];
        $now       = now();

        for ($i = 0; $i < $quantidade; $i++) {
            $codigoUnico = (string) Str::uuid();

            $hmac = self::gerarHmac([
                'codigo_unico'      => $codigoUnico,
                'pedido_id'         => $pedidoId,
                'evento_id'         => $eventoId,
                'tipo_ingressos_id' => $tipoIngressoId,
                'lote_id'           => $loteId,
            ]);

            $bilhetes[] = [
                'pedido_id'         => $pedidoId,
                'evento_id'         => $eventoId,
                'tipo_ingressos_id' => $tipoIngressoId,
                'codigo_unico'      => $codigoUnico,
                'hmac_assinatura'   => $hmac,
                'lote_id'           => $loteId,
                'tentativas_invalidas' => 0,
                'bloqueado'         => false,
                'validado_em'       => null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];

            $auditorias[] = [
                'bilhete_id'     => 0, // preenchido depois
                'acao'           => 'emitido',
                'codigo_unico'   => $codigoUnico,
                'scanner_ip'     => $ip ?? request()->ip(),
                'validado_por'   => $userId,
                'snapshot'       => json_encode([
                    'lote_id'   => $loteId,
                    'evento_id' => $eventoId,
                    'total'     => $total,
                ]),
                'created_at'     => $now,
            ];
        }

        // Insert em batch — 1 query
        Bilhete::insert($bilhetes);

        // Busca os IDs gerados para a auditoria
        $ids = DB::table('bilhetes')
            ->where('lote_id', $loteId)
            ->pluck('id')
            ->toArray();

        foreach ($auditorias as $i => $aud) {
            $auditorias[$i]['bilhete_id'] = $ids[$i] ?? 0;
        }

        DB::table('bilhetes_auditoria')->insert($auditorias);

        return $ids;
    }

    /**
     * Regista uma entrada de auditoria.
     */
    public static function registarAuditoria(
        int    $bilheteId,
        string $acao,
        string $codigoUnico,
        array  $snapshot = [],
        int    $validadoPor = null
    ): void {
        DB::table('bilhetes_auditoria')->insert([
            'bilhete_id'        => $bilheteId,
            'acao'              => $acao,
            'codigo_unico'      => $codigoUnico,
            'scanner_ip'        => request()->ip(),
            'scanner_user_agent'=> request()->userAgent(),
            'validado_por'      => $validadoPor ?? auth()->id(),
            'snapshot'          => json_encode($snapshot),
            'created_at'        => now(),
        ]);
    }
}