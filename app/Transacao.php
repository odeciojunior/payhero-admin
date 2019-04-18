<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $venda
 * @property int $empresa
 * @property string $valor
 * @property string $tipo
 * @property string $created_at
 * @property string $updated_at
 * @property Empresa $empresa
 * @property Venda $venda
 */
class Transacao extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'transacoes';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['venda', 'empresa', 'valor', 'tipo', 'created_at', 'updated_at'];

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
    public function venda()
    {
        return $this->belongsTo('App\Venda', 'venda');
    }
}
