<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $transportadora
 * @property int $view_checkout
 * @property string $nome
 * @property string $descricao
 * @property int $quntidade
 * @property boolean $status_cupom
 * @property string $cod_identificador
 * @property float $preco
 * @property boolean $frete_fixo
 * @property float $valor_frete
 * @property boolean $frete
 * @property boolean $status
 * @property string $id_plano_trasnportadora
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Transportadora $transportadora
 * @property ViewCheckout $viewCheckout
 * @property Foto[] $fotos
 * @property PlanosBrinde[] $planosBrindes
 * @property PlanosCupon[] $planosCupons
 * @property PlanosPixel[] $planosPixels
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
    protected $fillable = ['transportadora', 'layout', 'nome', 'descricao', 'quntidade', 'status_cupom', 'cod_identificador', 'preco', 'frete_fixo', 'valor_frete', 'frete', 'status', 'id_plano_trasnportadora', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transportadora()
    {
        return $this->belongsTo('App\Transportadora', 'transportadora');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function viewCheckout()
    {
        return $this->belongsTo('App\ViewCheckout', 'view_checkout');
    }

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
    public function planosBrindes()
    {
        return $this->hasMany('App\PlanosBrinde', 'plano');
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
    public function planosPixels()
    {
        return $this->hasMany('App\PlanosPixel', 'plano');
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
