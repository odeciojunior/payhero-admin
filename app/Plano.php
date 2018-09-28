<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $nome
 * @property string $descricao
 * @property int $quntidade
 * @property boolean $status_cupom
 * @property string $cod_identificador
 * @property float $preco
 * @property string $created_at
 * @property string $updated_at
 * @property string $id_pacote_kapsula
 * @property Foto[] $fotos
 * @property PlanosCupon[] $planosCupons
 * @property PlanosVenda[] $planosVendas
 * @property ProdutosPlano[] $produtosPlanos
 */
class Plano extends Model
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
    protected $fillable = ['nome', 'descricao', 'quntidade', 'status_cupom', 'cod_identificador', 'preco', 'created_at', 'updated_at', 'id_pacote_kapsula'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fotos()
    {
        return $this->hasMany('App\Foto', 'plano');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planosCupons()
    {
        return $this->hasMany('App\PlanosCupon', 'plano');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planosVendas()
    {
        return $this->hasMany('App\PlanosVenda', 'plano');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function produtosPlanos()
    {
        return $this->hasMany('App\ProdutosPlano', 'plano');
    }
}
