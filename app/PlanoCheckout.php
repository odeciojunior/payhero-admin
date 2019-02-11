<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $checkout
 * @property integer $plano
 * @property string $quantidade
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Checkout $checkout
 * @property Plano $plano
 */
class PlanoCheckout extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'planos_checkout';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['checkout', 'plano', 'quantidade', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function checkout()
    {
        return $this->belongsTo('App\Checkout', 'checkout');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plano()
    {
        return $this->belongsTo('App\Plano', 'plano');
    }
}
