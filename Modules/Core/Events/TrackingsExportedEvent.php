<?php

namespace Modules\Core\Events;

use Modules\Core\Entities\User;

class TrackingsExportedEvent
{
    public $user;

    public $filename;

    /**
     * TrackingCodeUpdatedEvent constructor.
     * @param User $user
     * @param string $filename
     */
    public function __construct(User $user, string $filename)
    {
        $this->user = $user;
        $this->filename = $filename;
    }
}
