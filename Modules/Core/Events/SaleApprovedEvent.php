<?php

namespace Modules\Core\Events;

use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Customer;
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
     * @var Customer
     */
    public $customer;

    /**
     * Create a new event instance.
     * @param Plan $plan
     * @param Sale $sale
     * @param Project $project
     * @param Delivery $delivery
     * @param Customer $customer
     */
    public function __construct(Plan $plan, Sale $sale, Project $project, Delivery $delivery, Customer $customer)
    {
        $this->plan     = $plan;
        $this->sale     = $sale;
        $this->project  = $project;
        $this->delivery = $delivery;
        $this->customer = $customer;
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
