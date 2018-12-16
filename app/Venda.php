<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $comprador
 * @property integer $entrega
 * @property string $forma_pagamento
 * @property float $valor_total_pago
 * @property float $valor_recebido_mercado_pago
 * @property float $valor_frete
 * @property float $valor_plano
 * @property float $valor_cupom
 * @property boolean $tipo_cupom
 * @property string $cod_cupom
 * @property string $meio_pagamento
 * @property string $data_inicio
 * @property string $data_finalizada
 * @property string $pagamento_id
 * @property string $pagamento_status
 * @property int $qtd_parcelas
 * @property float $valor_parcelas
 * @property string $bandeira
 * @property string $link_boleto
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Compradore $compradore
 * @property Entrega $entrega
 * @property Boleto[] $boletos
 * @property Comisso[] $comissoes
 * @property PlanosVenda[] $planosVendas
 * @property Ticket[] $tickets
 */
class Venda extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['proprietario','comprador', 'entrega', 'forma_pagamento', 'valor_total_pago', 'valor_recebido_mercado_pago', 'valor_frete', 'valor_plano', 'valor_cupom', 'tipo_cupom', 'cod_cupom', 'meio_pagamento', 'data_inicio', 'data_finalizada', 'pagamento_id', 'pagamento_status', 'qtd_parcelas', 'valor_parcelas', 'bandeira', 'link_boleto', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function compradore()
    {
        return $this->belongsTo('App\Compradore', 'comprador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entrega()
    {
        return $this->belongsTo('App\Entrega', 'entrega');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function boletos()
    {
        return $this->hasMany('App\Boleto', 'venda');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comissoes()
    {
        return $this->hasMany('App\Comisso', 'venda');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planosVendas()
    {
        return $this->hasMany('App\PlanosVenda', 'venda');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany('App\Ticket', 'venda');
    }
}
