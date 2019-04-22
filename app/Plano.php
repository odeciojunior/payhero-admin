<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property int $empresa
 * @property int $projeto
 * @property int $layout
 * @property int $hotzapp_dados
 * @property int $transportadora
 * @property string $nome
 * @property string $descricao
 * @property int $quantidade
 * @property string $cod_identificador
 * @property float $preco
 * @property boolean $frete_fixo
 * @property float $valor_frete
 * @property boolean $frete
 * @property boolean $pagamento_cartao
 * @property boolean $pagamento_boleto
 * @property string $desconto
 * @property int $valor_desconto
 * @property string $mensagen_desconto
 * @property boolean $status
 * @property string $id_plano_trasnportadora
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $qtd_parcelas
 * @property string $parcelas_sem_juros
 * @property string $foto
 * @property string $responsavel_frete
 * @property string $shopify_id
 * @property string $shopify_variant_id
 * @property Empresa $empresa
 * @property DadosHotzapp $dadosHotzapp
 * @property Layout $layout
 * @property Projeto $projeto
 * @property Transportadora $transportadora
 * @property Foto[] $fotos
 * @property LinksAfiliado[] $linksAfiliados
 * @property MensagensSm[] $mensagensSms
 * @property PlanosBrinde[] $planosBrindes
 * @property PlanosCupon[] $planosCupons
 * @property PlanosPixel[] $planosPixels
 * @property PlanosVenda[] $planosVendas
 * @property ProdutosPlano[] $produtosPlanos
 * @property ZenviaSm[] $zenviaSms
 */
class Plano extends Model
{
    use SoftDeletes;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['empresa', 'projeto', 'layout', 'hotzapp_dados', 'transportadora', 'nome', 'descricao', 'quantidade', 'cod_identificador', 'preco', 'frete_fixo', 'valor_frete', 'frete', 'pagamento_cartao', 'pagamento_boleto', 'desconto', 'valor_desconto', 'mensagen_desconto', 'status', 'id_plano_trasnportadora', 'created_at', 'updated_at', 'deleted_at', 'qtd_parcelas', 'parcelas_sem_juros', 'foto', 'responsavel_frete', 'shopify_id', 'shopify_variant_id'];

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fotos()
    {
        return $this->hasMany('App\Foto', 'plano');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function linksAfiliados()
    {
        return $this->hasMany('App\LinksAfiliado', 'plano');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mensagensSms()
    {
        return $this->hasMany('App\MensagensSm', 'plano');
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zenviaSms()
    {
        return $this->hasMany('App\ZenviaSm', 'plano');
    }
}
