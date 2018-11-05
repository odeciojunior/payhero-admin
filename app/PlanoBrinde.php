<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $brinde
 * @property integer $plano
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Brinde $brinde
 * @property Plano $plano
 */
class PlanoBrinde extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'planos_brindes';

    /**
     * @var array
     */
    protected $fillable = ['brinde', 'plano', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brinde()
    {
        return $this->belongsTo('App\Brinde', 'brinde');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plano()
    {
        return $this->belongsTo('App\Plano', 'plano');
    }
}
