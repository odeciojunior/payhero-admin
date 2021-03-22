<?php

namespace App\Console\Commands;

use App\Exceptions\CommandMonitorTimeException;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Exception;
use Modules\Core\Entities\Invitation;

class VerifyInviteExpired extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'verify:inviteexpired';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {

            $invitationModel = new Invitation();

            $invites = $invitationModel->where('expiration_date', '<=', Carbon::now()->toDateString())->get();
            foreach ($invites as $invite) {
                $invite->update(['status' => $invitationModel->present()->getStatus('expired')]);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
