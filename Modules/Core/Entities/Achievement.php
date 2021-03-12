<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $icon
 * @property string $created_at
 * @property string $updated_at
 */
class Achievement extends Model
{
    const ACHIEVEMENT_SPEED_OF_LIGHT         = 1;
    const ACHIEVEMENT_METEORIC_SUPPORT       = 2;
    const ACHIEVEMENT_COLONIZER              = 3;
    const ACHIEVEMENT_SKY_SELLER             = 4;
    const ACHIEVEMENT_STAR_DUST              = 5;
    const ACHIEVEMENT_FALLING_STAR           = 6;
    const ACHIEVEMENT_STAR_WARS              = 7;
    const ACHIEVEMENT_ALIEN                  = 8;
    const ACHIEVEMENT_HITCHHIKER_OF_GALAXIES = 9;
    const ACHIEVEMENT_CAPITALIST_ORBIT       = 10;
    const ACHIEVEMENT_MOONSTRUCK             = 11;
    const ACHIEVEMENT_INFINITY_AND_BEYOND    = 12;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'achievements';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'icon',
        'created_at',
        'updated_at'
    ];

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}
