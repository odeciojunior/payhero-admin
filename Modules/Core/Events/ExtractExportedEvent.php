<?php

namespace Modules\Core\Events;

use Modules\Core\Entities\User;

class ExtractExportedEvent
{
    public $user;

    public $filename;

    public $email;

    /**
     * ExtractExportedEvent constructor.
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
