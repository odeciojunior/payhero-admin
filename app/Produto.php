<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $categoria
 * @property string $nome
 * @property string $descricao
 * @property string $email
 * @property int $garantia
 * @property int $quntidade
 * @property boolean $disponivel
 * @property boolean $formato
 * @property string $telefone_suporte
 * @property string $created_at
 * @property string $updated_at
 * @property Categoria $categoria
 * @property ProdutosPlano[] $produtosPlanos
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
    protected $fillable = ['categoria', 'nome', 'descricao', 'email', 'garantia', 'quntidade', 'disponivel', 'formato', 'telefone_suporte', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categoria()
    {
        return $this->belongsTo('App\Categoria', 'categoria');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function produtosPlanos()
    {
        return $this->hasMany('App\ProdutosPlano', 'produto');
    }
}
