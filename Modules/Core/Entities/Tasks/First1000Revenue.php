<?php

namespace Modules\Core\Entities\Tasks;

use Modules\Core\Entities\Task;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\TaskCheck;

class First1000Revenue extends Task implements TaskCheck
{
    public function userCompletedTask(User $user): bool
    {
        $revenue = Transaction::where("user_id", $user->id)
            ->whereIn("status_enum", [Transaction::STATUS_TRANSFERRED, Transaction::STATUS_PAID])
            ->selectRaw("sum(value) as total_value")
            ->havingRaw("sum(value) > 100000")
            ->first();

        return $revenue && $revenue->total_value >= 100000;
    }
}
