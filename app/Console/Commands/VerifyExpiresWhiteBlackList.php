<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\WhiteBlackList;
use Illuminate\Support\Facades\Log;

class VerifyExpiresWhiteBlackList extends Command
{
    protected $signature = 'whiteblacklist:verifyexpires';

    protected $description = 'Remove regras do white/black list que expiraram';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            WhiteBlackList::where('expires_at', '<', Carbon::now()->toDateString())->delete();

        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
}
