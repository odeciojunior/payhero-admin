<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $comprador
 * @property boolean $status
 * @property string $forma_pagamento
 * @property float $valor_total_pago
 * @property float $valor_recebido_mercado_pago
 * @property float $valor_plano
 * @property float $valor_cupom
 * @property integer $tipo_cupom
 * @property string $meio_pagamento
 * @property string $data_inicio
 * @property string $data_finalizada
 * @property string $cod_cupom
 * @property string $mercado_pago_id
 * @property string $mercado_pago_status
 * @property string $created_at
 * @property string $updated_at
 * @property float $valor_frete
 * @property integer $qtd_parcela
 * @property float $valor_parcela
 * @property string $bandeira
 * @property Compradore $compradore
 * @property Boleto[] $boletos
 * @property Comisso[] $comissoes
 * @property PlanosVenda[] $planosVendas
 * @property Entrega[] $entregas
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
    protected $fillable = ['comprador', 'status', 'forma_pagamento', 'valor_total_pago', 'valor_recebido_mercado_pago', 'valor_plano', 'valor_cupom', 'tipo_cupom', 'meio_pagamento', 'data_inicio', 'data_finalizada', 'cod_cupom', 'created_at', 'updated_at', 'mercado_pago_id', 'mercado_pago_status', 'valor_frete', 'qtd_parcela', 'valor_parcela', 'bandeira','entrega', 'link_boleto'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function compradore()
    {
        return $this->belongsTo('App\Compradore', 'comprador');
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
    public function entregas()
    {
        return $this->hasMany('App\Entrega', 'entrega');
    }
}
