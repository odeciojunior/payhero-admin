<?php

namespace Modules\Core\Events;

use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Client;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Delivery;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;

class SaleApprovedEvent
{
    use SerializesModels;
    /**
     * @var Plan
     */
    public $plan;
    /**
     * @var Sale
     */
    public $sale;
    /**
     * @var Project
     */
    public $project;
    /**
     * @var Delivery
     */
    public $delivery;
    /**
     * @var Client
     */
    public $client;

    /**
     * Create a new event instance.
     * @param Plan $plan
     * @param Sale $sale
     * @param Project $project
     * @param Delivery $delivery
     * @param Client $client
     */
    public function __construct(Plan $plan, Sale $sale, Project $project, Delivery $delivery, Client $client)
    {
        $this->plan     = $plan;
        $this->sale     = $sale;
        $this->project  = $project;
        $this->delivery = $delivery;
        $this->client   = $client;
    }

    /**
     * Get the channels the event should broadcast on.
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
