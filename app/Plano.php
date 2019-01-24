<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $empresa
 * @property int $layout
 * @property int $hotzapp_dados
 * @property int $transportadora
 * @property int $layoutss
 * @property int $projeto
 * @property string $nome
 * @property string $descricao
 * @property int $quantidade
 * @property boolean $status_cupom
 * @property string $cod_identificador
 * @property float $preco
 * @property boolean $frete_fixo
 * @property float $valor_frete
 * @property boolean $frete
 * @property boolean $cartao
 * @property boolean $boleto
 * @property string $desconto
 * @property int $valor_desconto
 * @property string $mensagen_desconto
 * @property boolean $status
 * @property string $id_plano_trasnportadora
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Empresa $empresa
 * @property DadosHotzapp $dadosHotzapp
 * @property Layout $layout
 * @property Projeto $projeto
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
    protected $fillable = ['empresa', 'layout', 'hotzapp_dados', 'transportadora', 'layoutss', 'projeto', 'nome', 'descricao', 'quantidade', 'status_cupom', 'cod_identificador', 'preco', 'frete_fixo', 'valor_frete', 'frete', 'cartao', 'boleto', 'desconto', 'valor_desconto', 'mensagen_desconto', 'status', 'id_plano_trasnportadora','foto', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empresa()
    {
        return $this->belongsTo('App\Empresa', 'empresa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dadosHotzapp()
    {
        return $this->belongsTo('App\DadosHotzapp', 'hotzapp_dados');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function layout()
    {
        return $this->belongsTo('App\Layout', 'layout');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projeto()
    {
        return $this->belongsTo('App\Projeto', 'projeto');
    }

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
        return $this->belongsTo('App\ViewCheckout', 'layoutss');
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
