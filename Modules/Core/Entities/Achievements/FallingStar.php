<?php

namespace Modules\Core\Entities\Achievements;

use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Achievement;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\AchievementCheck;

class FallingStar extends Achievement implements AchievementCheck
{
    const ACHIEVEMENT_ID = 6;

    public function userAchieved(User $user): bool
    {
        //TODO: Está com "return false;" pois não existem afiliados ativos no momento
        //TODO: otimizar tabela affilliates com a coluna producer_id (users)
        // return false;

        //TODO: Refactor with query builder
        $sql =
            'SELECT u.id, count(*) AS total_affiliates
                FROM users u
                         INNER JOIN users_projects up ON u.id = up.user_id
                         INNER JOIN projects p ON p.id = up.project_id AND p.status_url_affiliates = TRUE
                         INNER JOIN affiliates a ON a.project_id = p.id AND a.id IN (
                    SELECT affiliate_id
                    FROM sales s
                    WHERE a.id = s.affiliate_id
                      AND owner_id = u.id
                      AND s.status IN (' .
            Sale::STATUS_APPROVED .
            ',
                                       ' .
            Sale::STATUS_CHARGEBACK .
            ',
                                       ' .
            Sale::STATUS_REFUNDED .
            ',
                                       ' .
            Sale::STATUS_IN_DISPUTE .
            '
                                       )
                )
                WHERE u.id = ?
                GROUP BY u.id;';

        $queryReturn = DB::select($sql, [$user->id]);

        return count($queryReturn) && $queryReturn[0]->total_affiliates >= 10;
    }
}
