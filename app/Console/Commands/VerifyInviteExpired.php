<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Invitation;
use Illuminate\Support\Facades\Log;

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

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

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

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
}
