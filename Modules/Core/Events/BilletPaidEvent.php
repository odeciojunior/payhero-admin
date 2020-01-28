<?php

namespace Modules\Core\Events;

use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Customer;
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
     * @var Customer
     */
    public $customer;

    /**
     * Create a new event instance.
     * @param Plan $plan
     * @param Sale $sale
     * @param Customer $customer
     */
    public function __construct(Plan $plan, Sale $sale, Customer $customer)
    {
        $this->plan     = $plan;
        $this->sale     = $sale;
        $this->customer = $customer;
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
