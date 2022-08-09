<?php

namespace Modules\Core\Entities\Achievements;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\AchievementCheck;

class Moonstruck extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 11;

    public function userAchieved(User $user): bool
    {
        $sql = 'SELECT DISTINCT subject_id AS user_id, CAST(created_at AS DATE) AS login_date
                FROM activity_log
                WHERE subject_type = ?
                  AND log_name = ? AND subject_id = ?;';
        $queryReturn = DB::select($sql, [User::class, "login", $user->id]);

        if (count($queryReturn)) {
            $maxSequence = 0;
            $sequence = 1;
            $currentDate = null;
            $beforeDate = null;
            for ($i = 0; $i < count($queryReturn); $i++) {
                $currentDate = Carbon::make($queryReturn[$i]->login_date);

                if ($i > 0) {
                    $beforeDate = Carbon::make($queryReturn[$i - 1]->login_date);
                }

                if ($currentDate->subDays(1)->eq($beforeDate)) {
                    $sequence++;
                    $maxSequence = $sequence >= $maxSequence ? $sequence : $maxSequence;
                } else {
                    $sequence = 1;
                }

                if ($maxSequence >= 21) {
                    return true;
                }
            }
        }

        return false;
    }
}
