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

    const HOMOLOG_MERCHANT_ID = 'eb25ce51-f685-41c5-a76a-d8ed09f373c9';

    const HOMOLOG_CLIENT_SECRET = 'yFFWG75RNkfte0WIgRPUlnHdSAKtJ3GYnaNdH8GVVAE=';

    const PRODUCTION_MERCHANT_ID = '';

    const PRODUCTION_CLIENT_SECRET = '';

    public $authorizationToken;

    /**
     * GetnetBackOfficeService constructor.
     */
    public function __construct()
    {
        try {
            if (FoxUtils::isProduction()) {
                $this->authorizationToken = base64_encode(self::PRODUCTION_MERCHANT_ID.':'.self::PRODUCTION_CLIENT_SECRET);
            } else {
                $this->authorizationToken = base64_encode(self::HOMOLOG_MERCHANT_ID.':'.self::HOMOLOG_CLIENT_SECRET);
            }
            $this->setAccessToken();

        } catch (Exception $e) {
            report($e);
        }

        parent::__construct();
    }


    /**
     * @return string
     */
    public function getMerchantId()
    {
        if (FoxUtils::isProduction()) {
            return self::PRODUCTION_MERCHANT_ID;
        }
        else{
            return self::HOMOLOG_MERCHANT_ID;
        }
    }

    public function checkPjCompanyRegister($cnpj)
    {
        $url = 'api/subordinates/'.$this->getMerchantId();

        return $this->sendCurl($url, 'GET');
    }

    public function createPfCompany(Company $company)
    {
        $url = 'api/subordinates';
        $data = $this->getPrepareDataCreatePfCompany($company);

        return $this->sendCurl($url, 'POST', $data, $company->id);
    }

    public function createPjCompany(Company $company)
    {
        $url = 'api/subordinates';
        $data = $this->getPrepareDataCreatePjCompany($company);

        return $this->sendCurl($url, 'POST', $data,$company->id);
    }



}
