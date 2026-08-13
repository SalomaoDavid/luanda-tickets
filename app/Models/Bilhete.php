<?php

namespace App\Models;

use App\Services\BilheteService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bilhete extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pedido_id',
        'evento_id',
        'tipo_ingressos_id',
        'codigo_unico',
        // hmac_assinatura REMOVIDO — só definido pelo BilheteService
        'lote_id',
        'tentativas_invalidas',
        'bloqueado',
        'validado_em',
    ];

    protected $hidden = [
        'hmac_assinatura', // Nunca exposta em respostas JSON
    ];

    protected $casts = [
        'validado_em'          => 'datetime',
        'deleted_at'           => 'datetime',
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

    public function hmacValido(): bool
    {
        return BilheteService::verificarHmac($this);
    }

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

    public function registarTentativaInvalida(): void
    {
        $novasTentativas = $this->tentativas_invalidas + 1;
        $bloquear        = $novasTentativas >= 5;

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