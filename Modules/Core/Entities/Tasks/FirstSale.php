<?php

namespace Modules\Core\Entities\Tasks;

use Modules\Core\Entities\Task;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\TaskCheck;

class FirstSale extends Task implements TaskCheck
{
    public function userCompletedTask(User $user): bool
    {
        $transactions = Transaction::where("user_id", $user->id)
            ->whereIn("status_enum", [Transaction::STATUS_TRANSFERRED, Transaction::STATUS_PAID])
            ->limit(1)
            ->get();

        return $transactions->count() > 0;
    }
}
