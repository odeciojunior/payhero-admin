<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\Company;
use Modules\Core\Traits\BraspagPrepareCompanyData;

/**
 * Class BraspagService
 * @package Modules\Core\Services
 */
class BraspagBackOfficeService extends BraspagService
{
    use BraspagPrepareCompanyData;

    public $merchantId;

    public $clientSecret;

    public $authorizationToken;

    public $dataCreateCompany = null;

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
            $this->merchantId = getenv('BRASPAG_PRODUCTION_MERCHANT_ID');
            $this->clientSecret = getenv('BRASPAG_PRODUCTION_CLIENT_SECRET');
        } else {
            $this->merchantId = getenv('BRASPAG_HOMOLOG_MERCHANT_ID');
            $this->clientSecret = getenv('BRASPAG_HOMOLOG_CLIENT_SECRET');
        }

        $this->authorizationToken = base64_encode($this->merchantId.':'.$this->clientSecret);
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
