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
        $transactionModel = new Transaction;
        $revenue = $transactionModel
            ->whereIn('status_enum', [Transaction::STATUS_TRANSFERRED, Transaction::STATUS_PAID])
            ->where('user_id', $user->id)
            ->sum('value');

        return $revenue >= 100000;
    }
}
