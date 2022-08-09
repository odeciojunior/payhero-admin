<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nwidart\Modules\Collection;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $access_token
 * @property string $refresh_token
 * @property string $expiration
 * @property boolean $completed
 * @property string $created_at
 * @property string $updated_at
 * @property Collection $shippings
 */
class MelhorenvioIntegration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "id",
        "user_id",
        "name",
        "access_token",
        "refresh_token",
        "expiration",
        "completed",
        "created_at",
        "updated_at",
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
