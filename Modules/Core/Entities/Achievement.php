<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Core\Interfaces\AchievementCheck;

/**
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $icon
 * @property string $created_at
 * @property string $updated_at
 */
class Achievement extends Model implements AchievementCheck
{
    const ACHIEVEMENT_SPEED_OF_LIGHT = 1;
    const ACHIEVEMENT_METEORIC_SUPPORT = 2;
    const ACHIEVEMENT_COLONIZER = 3;
    const ACHIEVEMENT_SKY_SELLER = 4;
    const ACHIEVEMENT_STAR_DUST = 5;
    const ACHIEVEMENT_FALLING_STAR = 6;
    const ACHIEVEMENT_STAR_WARS = 7;
    const ACHIEVEMENT_ALIEN = 8;
    const ACHIEVEMENT_HITCHHIKER_OF_GALAXIES = 9;
    const ACHIEVEMENT_CAPITALIST_ORBIT = 10;
    const ACHIEVEMENT_MOONSTRUCK = 11;
    const ACHIEVEMENT_INFINITY_AND_BEYOND = 12;

    const ACHIEVEMENTS_CLASS = [
        Achievement::ACHIEVEMENT_SPEED_OF_LIGHT => Achievements\SpeedOfLight::class,
        Achievement::ACHIEVEMENT_METEORIC_SUPPORT => Achievements\MeteoricSupport::class,
        Achievement::ACHIEVEMENT_COLONIZER => Achievements\Colonizer::class,
        Achievement::ACHIEVEMENT_SKY_SELLER => Achievements\SkySeller::class,
        Achievement::ACHIEVEMENT_STAR_DUST => Achievements\StarDust::class,
        Achievement::ACHIEVEMENT_FALLING_STAR => Achievements\FallingStar::class,
        Achievement::ACHIEVEMENT_STAR_WARS => Achievements\StarWars::class,
        Achievement::ACHIEVEMENT_ALIEN => Achievements\Alien::class,
        Achievement::ACHIEVEMENT_HITCHHIKER_OF_GALAXIES => Achievements\HitchhikerOfGalaxies::class,
        Achievement::ACHIEVEMENT_CAPITALIST_ORBIT => Achievements\CapitalistOrbit::class,
        Achievement::ACHIEVEMENT_MOONSTRUCK => Achievements\Moonstruck::class,
        Achievement::ACHIEVEMENT_INFINITY_AND_BEYOND => Achievements\InfinityAndBeyond::class,
    ];

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = "integer";

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "achievements";

    /**
     * @var array
     */
    protected $fillable = ["name", "description", "icon", "created_at", "updated_at"];

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function userAchieved(User $user): bool
    {
        try {
            $achievementSubclass = Achievement::ACHIEVEMENTS_CLASS[$this->id];
            return (new $achievementSubclass())->userAchieved($user);
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
}
