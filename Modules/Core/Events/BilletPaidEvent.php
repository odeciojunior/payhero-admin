<?php

namespace Modules\Core\Events;

use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Client;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;

class BilletPaidEvent
{
    /**
     * @var Plan
     */
    public $plan;
    /**
     * @var Sale
     */
    public $sale;
    /**
     * @var Client
     */
    public $client;

    /**
     * Create a new event instance.
     * @param Plan $plan
     * @param Sale $sale
     * @param Client $client
     */
    public function __construct(Plan $plan, Sale $sale, Client $client)
    {
        $this->plan   = $plan;
        $this->sale   = $sale;
        $this->client = $client;
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
