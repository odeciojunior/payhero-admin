<?php

namespace Modules\Core\Events;

use Illuminate\Queue\SerializesModels;

class UpdateCheckoutTable
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

}
