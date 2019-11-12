<?php

namespace Modules\Core\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\User;

class TrackingsImportedEvent
{
    use SerializesModels;

    public $user;

    /**
     * TrackingCodeUpdatedEvent constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return [];
    }
}
