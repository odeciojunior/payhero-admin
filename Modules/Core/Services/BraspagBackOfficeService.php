<?php

namespace Modules\Core\Services;

use Exception;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
use Modules\Core\Traits\BraspagPrepareCompanyData;

/**
 * Class BraspagService
 * @package Modules\Core\Services
 */
class BraspagBackOfficeService extends BraspagService
{
    use BraspagPrepareCompanyData;


    /**
     * @var string
     */
    private $urlCredentialAcessToken = '/oauth2/token';
    /**
     * @var string
     */
    private $postFieldsAcessToken;

    public $authorizationToken;

    /**
     * GetnetBackOfficeService constructor.
     */
    public function __construct()
    {
        try {
            if (FoxUtils::isProduction()) {
                $this->authorizationToken = base64_encode(
                    getenv('BRASPAG_CLIENT_ID_PRODUCTION').':'.getenv('BRASPAG_CLIENT_SECRET_PRODUCTION')
                );

                $this->postFieldsAcessToken = 'grant_type=client_credentials';
            } else {
                $this->authorizationToken = base64_encode(
                    getenv('BRASPAG_CLIENT_ID_SANDBOX').':'.getenv('BRASPAG_CLIENT_SECRET_SANDBOX')
                );

                $this->postFieldsAcessToken = 'grant_type=client_credentials';
            }

            $this->setAccessToken($this->urlCredentialAcessToken, $this->postFieldsAcessToken);
        } catch (Exception $e) {
            report($e);
        }

        parent::__construct();
    }


    /**
     * @return mixed
     */
    public function getMerchantId()
    {
        if (FoxUtils::isProduction()) {
            return env('BRASPAG_MERCHANT_ID_PRODUCTION');
        }

        return env('BRASPAG_MERCHANT_ID_SANDBOX');
    }

    /**
     * @return string[]
     */
    public function getAuthorizationHeader()
    {
        return [
            'authorization: Bearer '.$this->accessToken,
            'Content-Type: application/json',
        ];
    }

    public function checkPjCompanyRegister($cnpj)
    {
        $url = 'api/subordinates/'.$this->getMerchantId();

        return $this->sendCurl($url, 'GET');
    }

    public function createPjCompany(Company $company)
    {
        //$url = 'v1/mgm/pj/create-presubseller';
        $url = '/api/subordinates';
        $data = $this->getPrepareDataCreatePjCompany($company);

        return $this->sendCurl($url, 'POST', $data,$company->id);
    }



}
