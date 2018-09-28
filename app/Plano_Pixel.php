<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property integer $plano
 * @property int $pixel
 * @property boolean $checkout
 * @property boolean $cartao
 * @property boolean $boleto
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Pixel $pixel
 * @property Plano $plano
 */
class Plano_Pixel extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'planos_pixels';

    /**
     * @var array
     */
    protected $fillable = ['plano', 'pixel', 'checkout', 'cartao', 'boleto', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pixel()
    {
        return $this->belongsTo('App\Pixel', 'pixel');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plano()
    {
        return $this->belongsTo('App\Plano', 'plano');
    }
}
