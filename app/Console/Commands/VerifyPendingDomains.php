<?php

namespace App\Console\Commands;

use App\Entities\Domain;
use Illuminate\Console\Command;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\SendgridService;

class VerifyPendingDomains extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'verify:pendingdomains';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Verifica se os domínios pendentes ficaram ativos';


    /**
     * @var Domain
     */
    private $domainModel;
    /**
     * @var CloudFlareService
     */
    private $cloudFlareService;
    /**
     * @var SendgridService
     */
    private $sendgridService;


    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return Domain|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getDomainModel()
    {
        if (!$this->domainModel) {
            $this->domainModel = app(Domain::class);
        }

        return $this->domainModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getCloudFlareService()
    {
        if (!$this->cloudFlareService) {
            $this->cloudFlareService = app(CloudFlareService::class);
        }

        return $this->cloudFlareService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed|SendgridService
     */
    private function getSendgridService()
    {
        if (!$this->sendgridService) {
            $this->sendgridService = app(SendgridService::class);
        }

        return $this->sendgridService;
    }
    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $domains = $this->getDomainModel()->where('status', '!=', $this->getDomainModel()->getEnum('status', 'approved'))->get();
        foreach ($domains as $domain) {

            $activated              = null;
            $responseValidateDomain = null;
            $responseValidateLink   = null;

            $activated = $this->getCloudFlareService()->activationCheck($domain->name);

            $linkBrandResponse = $this->getSendgridService()->getLinkBrand($domain->name);
            $sendgridResponse  = $this->getSendgridService()->getZone($domain->name);

            if (!empty($linkBrandResponse) && !empty($sendgridResponse)) {
                $responseValidateDomain = $this->getSendgridService()->validateDomain($sendgridResponse->id);
                $responseValidateLink   = $this->getSendgridService()->validateBrandLink($linkBrandResponse->id);
            }

            if ($activated && $responseValidateDomain && $responseValidateLink) {
                $domain->update([
                                  'status' => $this->getDomainModel()->getEnum('status', 'approved'),
                              ]);
            }

            if ($this->getCloudFlareService()
                     ->checkHtmlMetadata('https://checkout.' . $domain->name, 'checkout-cloudfox', '1')) {
                $domain->update([
                                  'status' => $this->getDomainModel()->getEnum('status', 'approved'),
                              ]);
            }
        }
    }
}
