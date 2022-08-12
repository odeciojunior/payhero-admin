<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $benefit_id
 * @property boolean $enabled
 * @property User $user
 * @property User $benefit
 * @property string $created_at
 * @property string $updated_at
 */
class UserBenefit extends Model
{
    /**
     * @var array
     */
    protected $fillable = ["user_id", "benefit_id", "enabled", "created_at", "updated_at"];

    protected $hidden = ["laravel_through_key"];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function benefit()
    {
        return $this->belongsTo(Benefit::class);
    }
}
