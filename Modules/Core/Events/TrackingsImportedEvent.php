<?php

namespace Modules\Core\Events;

use Modules\Core\Entities\User;

class TrackingsImportedEvent
{
    public $user;

    /**
     * TrackingCodeUpdatedEvent constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
