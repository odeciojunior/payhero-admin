<?php

namespace Modules\Core\Events;

use Modules\Core\Entities\User;

class TrackingsImportedEvent
{
    public $user;

    /**
     * TrackingsImportedEvent constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
