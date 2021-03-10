<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Achievements;

class AchievementService
{
    const ACHIEVEMENTS_CLASS = [
        Achievement::ACHIEVEMENT_SPEED_OF_LIGHT         => Achievements\SpeedOfLight::class,
        Achievement::ACHIEVEMENT_METEORIC_SUPPORT       => Achievements\MeteoricSupport::class,
        Achievement::ACHIEVEMENT_COLONIZER              => Achievements\Colonizer::class,
        Achievement::ACHIEVEMENT_SKY_SELLER             => Achievements\SkySeller::class,
        Achievement::ACHIEVEMENT_STAR_DUST              => Achievements\StarDust::class,
        Achievement::ACHIEVEMENT_FALLING_STAR           => Achievements\FallingStar::class,
        Achievement::ACHIEVEMENT_STAR_WARS              => Achievements\StarWars::class,
        Achievement::ACHIEVEMENT_ALIEN                  => Achievements\Alien::class,
        Achievement::ACHIEVEMENT_HITCHHIKER_OF_GALAXIES => Achievements\HitchhikerOfGalaxies::class,
        Achievement::ACHIEVEMENT_CAPITALIST_ORBIT       => Achievements\CapitalistOrbit::class,
        Achievement::ACHIEVEMENT_MOONSTRUCK             => Achievements\Moonstruck::class,
        Achievement::ACHIEVEMENT_INFINITY_AND_BEYOND    => Achievements\InfinityAndBeyond::class,
    ];

    private $achivements;

    public function __construct()
    {
        $this->achivements = Achievement::all();
    }

    public function checkUserAchievements(User $user)
    {
        foreach (self::ACHIEVEMENTS_CLASS as $achievementClass) {
            $achievement = $achievementClass::find($achievementClass::ACHIEVEMENT_ID);
            $this->checkAchievement($user, $achievement);
        }
    }

    public function checkAchievement(User $user, Achievement $achievement): bool
    {
        $userAchievement = $user->achievements->where('id', $achievement::ACHIEVEMENT_ID)->first();
        if ($userAchievement) {
            return true;
        }

        if ($achievement->didUserAchieve($user)) {
            return $this->setUserAchievement($user, $achievement);
        }

        return false;
    }

    public static function setUserAchievement(User $user, Achievement $achievement): bool
    {
        try {
            $user->achievements()->attach($achievement);
            $user->update();
            //TODO: notification here
            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
}
