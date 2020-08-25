<?php


namespace Modules\Core\Events;


use Illuminate\Queue\SerializesModels;

class UserRegisteredEvent
{
    use SerializesModels;

    public $request;

    /**
     * Create a new event instance.
     * @param $request
     */
    public function __construct($request)
    {
            $this->request = $request;
    }
}
