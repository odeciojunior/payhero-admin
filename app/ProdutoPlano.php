<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $produto
 * @property integer $plano
 * @property string $created_at
 * @property string $updated_at
 * @property Plano $plano
 * @property Produto $produto
 */
class ProdutoPlano extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'produtos_planos';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['produto', 'plano', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plano()
    {
        return $this->belongsTo('App\Plano', 'plano');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function produto()
    {
        return $this->belongsTo('App\Produto', 'produto');
    }
}
