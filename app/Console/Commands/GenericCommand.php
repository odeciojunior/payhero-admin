<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Services\AccountApprovedService;
use Modules\Core\Services\CompanyBalanceService;
use Modules\Core\Services\FoxUtils;

use function PHPUnit\Framework\isEmpty;


use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\CieloService;
use Modules\Core\Services\Gateways\GerencianetService;
use Modules\Core\Services\Gateways\GetnetService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {

    }
}
