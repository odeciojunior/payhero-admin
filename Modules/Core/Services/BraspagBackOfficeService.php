<?php

namespace Modules\Core\Services;

use Exception;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
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
                $gateway = Gateway::where("name", "braspag_production")->first();

            } else {
                $gateway = Gateway::where("name", "braspag_sandbox")->first();

            }
            $this->postFieldsAcessToken = 'braspag_type=client_credentials';
            $configs = json_decode(FoxUtils::xorEncrypt($gateway->json_config, "decrypt"), true);
            $this->authorizationToken = base64_encode($configs['public_token'].':'.$configs['private_token']);

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

    public function createPfCompany(Company $company)
    {
        $url = '/api/subordinates';
        $data = $this->getPrepareDataCreatePfCompany($company);

        return $this->sendCurl($url, 'POST', $data, $company->id);
    }

    public function createPjCompany(Company $company)
    {
        $url = '/api/subordinates';
        $data = $this->getPrepareDataCreatePjCompany($company);

        return $this->sendCurl($url, 'POST', $data,$company->id);
    }



}
