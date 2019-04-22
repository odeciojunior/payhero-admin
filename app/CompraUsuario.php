<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $comprador
 * @property boolean $status
 * @property string $forma_pagamento
 * @property float $valor_total_pago
 * @property string $data_inicio
 * @property string $data_finalizada
 * @property string $plataforma_id
 * @property int $qtd_parcela
 * @property float $valor_parcela
 * @property string $bandeira
 * @property string $link_boleto
 * @property string $item
 * @property string $quantidade
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 */
class CompraUsuario extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'compras_usuarios';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['comprador', 'status', 'forma_pagamento', 'valor_total_pago', 'data_inicio', 'data_finalizada', 'plataforma_id', 'qtd_parcela', 'valor_parcela', 'bandeira', 'link_boleto', 'item', 'quantidade', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'comprador');
    }
}
