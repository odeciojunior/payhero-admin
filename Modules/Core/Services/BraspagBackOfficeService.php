<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Traits\BraspagPrepareCompanyData;

class BraspagBackOfficeService extends BraspagService
{
    use BraspagPrepareCompanyData;

    public $merchantId;

    public $clientSecret;

    public $authorizationToken;

    public function __construct()
    {
        try {
            $this->setCredentials();
            $this->setAccessToken();
        } catch (Exception $e) {
            report($e);
        }

        parent::__construct();
    }

    public function setCredentials()
    {
        if (FoxUtils::isProduction()) {
            $gateway = Gateway::where("name", "braspag_production")->first();
        } else {
            $gateway = Gateway::where("name", "braspag_sandbox")->first();
        }

        $configs = json_decode(FoxUtils::xorEncrypt($gateway->json_config, 'decrypt'), true);
        $this->authorizationToken = base64_encode($configs['public_token'].':'.$configs['private_token']);
    }

    public function checkPjCompanyRegister($cnpj)
    {
        $url = 'api/subordinates/'.$this->merchantId;

        return $this->sendCurl($url, 'GET');
    }

    public function createPfCompany(Company $company)
    {
        $url = 'api/subordinates';
        $data = $this->getPrepareDateCreateCompany($company);

        return $this->sendCurl($url, 'POST', $data, $company->id);
    }

    public function createPjCompany(Company $company)
    {
        $url = 'api/subordinates';
        $data = $this->getPrepareDateCreateCompany($company);

        return $this->sendCurl($url, 'POST', $data, $company->id);
    }
}
