<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Services\AccountApprovedService;
use Modules\Core\Services\FoxUtils;

use function PHPUnit\Framework\isEmpty;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {

    }
}
