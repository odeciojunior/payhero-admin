<?php

namespace Modules\Core\Entities\Tasks;

use Modules\Core\Entities\Task;
use Modules\Core\Entities\User;
use Modules\Core\Interfaces\TaskCheck;

class CreateFirstStore extends Task implements TaskCheck
{
    public function userCompletedTask(User $user): bool
    {
        return $user->projects()->count() > 0;
    }
}
