<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Invitation;

class VerifyInviteExpired extends Command
{
    protected $signature = 'verify:inviteexpired';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        try {
            $invites = Invitation::where('expiration_date', '<=', Carbon::now())
                ->where("status", "!=", Invitation::INVITATION_EXPIRED)
                ->get();
            foreach ($invites as $invite) {
                $invite->update(['status' => Invitation::INVITATION_EXPIRED]);
            }
        } catch (Exception $e) {
            report($e);
        }

    }
}
