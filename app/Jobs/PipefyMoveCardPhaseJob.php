<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\User;
use Modules\Core\Services\Pipefy\PipefyService;

class PipefyMoveCardPhaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $phase;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $phase)
    {
        $this->user = $user;
        $this->phase = $phase;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new PipefyService())->moveCardToPhase($this->user, $this->phase);
    }
}
