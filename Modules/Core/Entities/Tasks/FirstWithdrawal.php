<?php

namespace Modules\Core\Entities\Tasks;

use Modules\Core\Entities\Task;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Interfaces\TaskCheck;

class FirstWithdrawal extends Task implements TaskCheck
{
    public function userCompletedTask(User $user): bool
    {
        return Withdrawal::where("status", Withdrawal::STATUS_TRANSFERRED)
            ->whereIn("company_id", $user->companies()->pluck("id"))
            ->count() > 0;
    }
}
