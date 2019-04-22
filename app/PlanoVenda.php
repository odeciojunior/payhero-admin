<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $plano
 * @property integer $venda
 * @property Plano $plano
 * @property Venda $venda
 */
class PlanoVenda extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'planos_vendas';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['plano', 'venda','valor_plano','quantidade'];

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
    public function venda()
    {
        return $this->belongsTo('App\Venda', 'venda');
    }
}
