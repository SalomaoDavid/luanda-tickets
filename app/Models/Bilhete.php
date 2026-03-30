<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bilhete extends Model
{
    // Definimos a tabela caso não siga o padrão plural (opcional)
    // protected $table = 'bilhetes';

    protected $fillable = [
        'pedido_id', 
        'evento_id', 
        'tipo_ingressos_id', 
        'codigo_unico', 
        'validado_em'
    ];

    /**
     * Relação com o Evento
     */
    public function evento() 
    {
        return $this->belongsTo(Evento::class);
    }

    /**
     * Relação com o Pedido (Crucial para a segurança/403)
     */
    public function pedido() 
    {
        // Corrigido: Removido o duplo return
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * Relação com o Tipo de Ingresso
     */
    public function tipoIngresso() 
    {
        return $this->belongsTo(TipoIngresso::class, 'tipo_ingressos_id');
    }
}