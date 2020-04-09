<?php

namespace Modules\Core\Events;

use Modules\Core\Entities\User;

class SalesExportedEvent
{
    public $user;

    public $filename;

    /**
     * SalesExportedEvent constructor.
     * @param User $user
     * @param string $filename
     */
    public function __construct(User $user, string $filename)
    {
        $this->user = $user;
        $this->filename = $filename;
    }
}
