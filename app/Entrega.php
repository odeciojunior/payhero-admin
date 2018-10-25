<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $transportadora
 * @property string $cep
 * @property string $pais
 * @property string $estado
 * @property string $cidade
 * @property string $bairro
 * @property string $rua
 * @property string $numero
 * @property string $ponto_referencia
 * @property integer $id_kapsula_pedido
 * @property string $status_kapsula
 * @property string $resposta_kapsula
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $cod_rastreio
 * @property Transportadora $transportadora
 * @property Venda[] $vendas
 */
class Entrega extends Model
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
    protected $fillable = ['transportadora', 'cep', 'pais', 'estado', 'cidade', 'bairro', 'rua', 'numero', 'ponto_referencia', 'id_kapsula_pedido', 'status_kapsula', 'resposta_kapsula', 'created_at', 'updated_at', 'deleted_at', 'cod_rastreio'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transportadora()
    {
        return $this->belongsTo('App\Transportadora', 'transportadora');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendas()
    {
        return $this->hasMany('App\Venda', 'entrega');
    }
}
