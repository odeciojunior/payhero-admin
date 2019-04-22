<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property integer $produto
 * @property int $projeto
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Produto $produto
 * @property Projeto $projeto
 */
class ProjetoProduto extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'projetos_produtos';

    /**
     * @var array
     */
    protected $fillable = ['produto', 'projeto', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function produto()
    {
        return $this->belongsTo('App\Produto', 'produto');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projeto()
    {
        return $this->belongsTo('App\Projeto', 'projeto');
    }
}
