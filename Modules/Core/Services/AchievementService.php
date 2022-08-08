<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\User;
use Modules\Core\Events\NotifyUserAchievementEvent;

class AchievementService
{
    public function checkUserAchievements(User $user)
    {
        foreach (Achievement::ACHIEVEMENTS_CLASS as $id => $achievementClass) {
            $achievement = $achievementClass::find($id);
            $this->checkAchievement($user, $achievement);
        }
    }

    public function checkAchievement(User $user, Achievement $achievement): bool
    {
        $hasAchievement = $user->achievements->contains("id", $achievement->id);
        if ($hasAchievement) {
            return true;
        }

        if ($achievement->userAchieved($user)) {
            return $this->setUserAchievement($user, $achievement);
        }

        return false;
    }

    public static function setUserAchievement(User $user, Achievement $achievement): bool
    {
        try {
            $user->achievements()->attach($achievement);
            $user->update();
            event(new NotifyUserAchievementEvent($user, $achievement));
            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    public function getCurrentUserAchievements(User $user): array
    {
        return Achievement::select("id", "name", "description", "icon", "storytelling")
            ->selectRaw(
                "EXISTS(SELECT user_id FROM achievement_user WHERE user_id = " .
                    $user->id .
                    " AND achievement_id = achievements.id) AS active"
            )
            ->get([$user->id])
            ->toArray();
    }
}
