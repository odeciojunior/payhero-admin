<?php

namespace Modules\Core\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\User;

class NotifyUserLevelEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public User $user;
    public int $level;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param int $level
     */
    public function __construct(User $user, int $level)
    {
        $this->user = $user;
        $this->level = $level;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("channel-name");
    }
}
