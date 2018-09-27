<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $cupom
 * @property integer $plano
 * @property string $created_at
 * @property string $updated_at
 * @property Cupon $cupon
 * @property Plano $plano
 */
class PlanoCupom extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'planos_cupons';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['cupom', 'plano', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cupon()
    {
        return $this->belongsTo('App\Cupon', 'cupom');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plano()
    {
        return $this->belongsTo('App\Plano', 'plano');
    }
}
