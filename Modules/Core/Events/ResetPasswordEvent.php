<?php

namespace Modules\Core\Events;

use App\Entities\Sale;
use App\Entities\User;
use Illuminate\Queue\SerializesModels;

class ResetPasswordEvent
{
    use SerializesModels;
    public $user;
    public $token;

    /**
     * ResetPasswordEvent constructor.
     * @param $token
     * @param User $user
     */
    public function __construct($token, User $user)
    {
        $this->user  = $user;
        $this->token = $token;
    }

    /**
     * Get the channels the event should be broadcast on.
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
