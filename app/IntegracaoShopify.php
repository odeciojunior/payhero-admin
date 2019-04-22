<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user
 * @property int $projeto
 * @property string $token
 * @property string $url_loja
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Projeto $projeto
 * @property User $user
 */
class IntegracaoShopify extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'integracoes_shopify';

    /**
     * @var array
     */
    protected $fillable = ['user', 'projeto', 'token', 'url_loja', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projeto()
    {
        return $this->belongsTo('App\Projeto', 'projeto');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user');
    }
}
