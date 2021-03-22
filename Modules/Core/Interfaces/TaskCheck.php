<?php

namespace Modules\Core\Interfaces;

use Modules\Core\Entities\User;

interface TaskCheck
{
    public function userCompletedTask(User $user): bool;
}
