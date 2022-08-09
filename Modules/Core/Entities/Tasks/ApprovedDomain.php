<?php

namespace Modules\Core\Entities\Tasks;

use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\TaskCheck;

class ApprovedDomain extends Task implements TaskCheck
{
    public function userCompletedTask(User $user): bool
    {
        $approvedDomain = $user
            ->projects()
            ->whereHas("domains", function ($query) {
                $query->where("domains.status", (new Domain())->present()->getStatus("approved"));
            })
            ->limit(1)
            ->get();

        return $approvedDomain->count() > 0;
    }
}
