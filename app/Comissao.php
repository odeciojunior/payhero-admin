<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $venda
 * @property int $referencia_comissionado
 * @property boolean $tipo_comissao
 * @property float $valor
 * @property string $created_at
 * @property string $updated_at
 * @property Venda $venda
 */
class Comissao extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'comissoes';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['venda', 'referencia_comissionado', 'tipo_comissao', 'valor', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function venda()
    {
        return $this->belongsTo('App\Venda', 'venda');
    }
}
