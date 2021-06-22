<?php


namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Nwidart\Modules\Collection;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $client_id
 * @property string $client_secret
 * @property string $access_token
 * @property string $refresh_token
 * @property string $expiration
 * @property string $zipcode
 * @property boolean $completed
 * @property string $created_at
 * @property string $updated_at
 * @property Collection $shippings
 */
class MelhorenvioIntegration extends Model
{

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'client_id',
        'client_secret',
        'access_token',
        'refresh_token',
        'expiration',
        'completed',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippings()
    {
        return $this->hasMany(Shipping::class);
    }
}
