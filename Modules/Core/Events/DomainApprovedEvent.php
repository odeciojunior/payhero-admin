<?php

namespace Modules\Core\Events;

use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Collection;

class DomainApprovedEvent
{
    /**
     * @var Project
     */
    public $project;

    /**
     * @var Domain
     */
    public $domain;

    /**
     * @var array
     */
    public $users;

    /**
     * Create a new event instance.
     * @param Domain $domain
     * @param Project $project
     * @param Collection $users
     */
    public function __construct(Domain $domain, Project $project, Collection $users)
    {
        $this->domain  = $domain;
        $this->project = $project;
        $this->users   = $users;
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
