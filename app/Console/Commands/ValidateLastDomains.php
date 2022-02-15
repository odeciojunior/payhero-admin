<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Domain;
use Modules\Core\Services\SendgridService;
use Illuminate\Support\Facades\Log;

class ValidateLastDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:validateLastDomains';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            $domainModel = new Domain();
            $sendgridService = new SendgridService();

            $domains = $domainModel->where('status', $domainModel->present()->getStatus('approved'))
                ->where('created_at', '>', Carbon::today()->subDays(3))
                ->get();

            $total = $domains->count();
            $count = 1;

            foreach ($domains as $domain) {

                $this->line($count . ' de ' . $total . '. Validando o domínio: ' . $domain->name);

                $responseValidateDomain = null;
                $responseValidateLink = null;

                $linkBrandResponse = $sendgridService->getLinkBrand($domain->name);
                $sendgridResponse = $sendgridService->getZone($domain->name);

                if (!empty($linkBrandResponse) && !empty($sendgridResponse)) {
                    $sendgridService->validateDomain($sendgridResponse->id);
                    $sendgridService->validateBrandLink($linkBrandResponse->id);
                }

                $count++;
            }

        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
}
