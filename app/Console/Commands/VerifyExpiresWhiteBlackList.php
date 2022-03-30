<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\WhiteBlackList;

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

        try {

            WhiteBlackList::where('expires_at', '<', Carbon::now()->toDateString())->delete();

        } catch (Exception $e) {
            report($e);
        }

    }
}
