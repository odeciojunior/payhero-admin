<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
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
    protected $fillable = ['cep', 'pais', 'estado', 'cidade', 'bairro', 'rua', 'numero', 'ponto_referencia', 'id_kapsula_pedido', 'status_kapsula', 'resposta_kapsula', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendas()
    {
        return $this->hasMany('App\Venda', 'entrega');
    }
}
