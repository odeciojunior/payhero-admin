<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $categoria
 * @property int $user
 * @property string $nome
 * @property string $descricao
 * @property string $garantia
 * @property int $quantidade
 * @property boolean $disponivel
 * @property boolean $formato
 * @property string $created_at
 * @property string $updated_at
 * @property string $custo_produto
 * @property string $foto
 * @property string $deleted_at
 * @property string $altura
 * @property string $largura
 * @property string $peso
 * @property string $recebedor_custo
 * @property boolean $shopify
 * @property Categoria $categoria
 * @property User $user
 * @property ProdutosPlano[] $produtosPlanos
 * @property ProjetosProduto[] $projetosProdutos
 */
class Produto extends Model
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
    protected $fillable = ['categoria', 'user', 'nome', 'descricao', 'garantia', 'quantidade', 'disponivel', 'formato', 'created_at', 'updated_at', 'custo_produto', 'foto', 'deleted_at', 'altura', 'largura', 'peso', 'recebedor_custo', 'shopify'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categoria()
    {
        return $this->belongsTo('App\Categoria', 'categoria');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function produtosPlanos()
    {
        return $this->hasMany('App\ProdutosPlano', 'produto');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projetosProdutos()
    {
        return $this->hasMany('App\ProjetosProduto', 'produto');
    }
}
