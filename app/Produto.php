<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $empresa
 * @property integer $categoria
 * @property string $nome
 * @property string $descricao
 * @property string $garantia
 * @property int $quantidade
 * @property boolean $disponivel
 * @property boolean $formato
 * @property string $created_at
 * @property string $updated_at
 * @property float $custo_produto
 * @property string $foto
 * @property string $deleted_at
 * @property string $altura
 * @property string $largura
 * @property string $peso
 * @property Categoria $categoria
 * @property Empresa $empresa
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
    protected $fillable = ['user', 'categoria', 'nome', 'descricao', 'garantia', 'quantidade', 'disponivel', 'formato', 'created_at', 'updated_at', 'custo_produto','recebedor_custo', 'foto', 'deleted_at', 'altura', 'largura', 'peso'];

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
    public function empresa()
    {
        return $this->belongsTo('App\Empresa', 'empresa');
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
