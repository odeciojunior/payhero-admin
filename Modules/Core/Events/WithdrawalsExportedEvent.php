<?php

namespace Modules\Core\Events;

use Modules\Core\Entities\User;

class WithdrawalsExportedEvent
{
    public $user;

    public $filename;

    public $email;

    /**
     * SalesExportedEvent constructor.
     * @param User $user
     * @param string $filename
     */
    public function __construct(User $user, string $filename, string $email = "")
    {
        $this->user = $user;
        $this->filename = $filename;
        $this->email = $email;
    }
}
