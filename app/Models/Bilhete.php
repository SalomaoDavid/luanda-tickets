<?php

namespace App\Models;

use App\Services\BilheteService;
use Illuminate\Database\Eloquent\Model;

class Bilhete extends Model
{
    protected $fillable = [
        'pedido_id',
        'evento_id',
        'tipo_ingressos_id',
        'codigo_unico',
        'hmac_assinatura',
        'lote_id',
        'tentativas_invalidas',
        'bloqueado',
        'validado_em',
    ];

    protected $casts = [
        'validado_em'          => 'datetime',
        'bloqueado'            => 'boolean',
        'tentativas_invalidas' => 'integer',
    ];

    // ── RELAÇÕES ──────────────────────────────────────────────

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function tipoIngresso()
    {
        return $this->belongsTo(TipoIngresso::class, 'tipo_ingressos_id');
    }

    // ── SEGURANÇA ─────────────────────────────────────────────

    /**
     * Verifica se a assinatura HMAC do bilhete é válida.
     */
    public function hmacValido(): bool
    {
        return BilheteService::verificarHmac($this);
    }

    /**
     * Verifica se o bilhete está apto para validação na entrada.
     */
    public function aptoParaEntrada(): array
    {
        if ($this->bloqueado) {
            return ['apto' => false, 'motivo' => 'Bilhete bloqueado por segurança.'];
        }

        if ($this->validado_em) {
            return ['apto' => false, 'motivo' => 'Bilhete já utilizado em ' . $this->validado_em->format('d/m H:i') . '.'];
        }

        if (!$this->hmacValido()) {
            return ['apto' => false, 'motivo' => 'Assinatura inválida — bilhete adulterado.'];
        }

        $pedido = $this->pedido()->select('id', 'status')->first();
        if (!$pedido || $pedido->status !== 'pago') {
            return ['apto' => false, 'motivo' => 'Pagamento não confirmado.'];
        }

        return ['apto' => true, 'motivo' => null];
    }

    /**
     * Regista tentativa inválida e bloqueia após 5 tentativas.
     */
    public function registarTentativaInvalida(): void
    {
        $novasTentativas = $this->tentativas_invalidas + 1;
        $bloquear        = $novasTentativas >= 5;

        // updateQuietly não dispara eventos — mas o trigger MySQL protege
        $this->updateQuietly([
            'tentativas_invalidas' => $novasTentativas,
            'bloqueado'            => $bloquear,
        ]);

        BilheteService::registarAuditoria(
            $this->id,
            $bloquear ? 'bloqueado' : 'tentativa_invalida',
            $this->codigo_unico,
            ['tentativas' => $novasTentativas, 'bloqueado' => $bloquear]
        );
    }
}