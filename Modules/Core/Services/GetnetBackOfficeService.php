<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\Company;
use Modules\Core\Traits\GetnetPrepareCompanyData;

/**
 * Class GetnetService
 * @package Modules\Core\Services
 */
class GetnetBackOfficeService extends GetnetService
{
    use GetnetPrepareCompanyData;

    private $urlCredentialAccessToken = 'credenciamento/auth/oauth/v2/token';

    public $postFieldsAccessToken;

    public $authorizationToken;

    private $sellerId;

    public function __construct()
    {
        try {
            $this->setAuthorizationToken();

            $this->setAccessToken($this->urlCredentialAccessToken, $this->postFieldsAccessToken);

            $this->setSellerId();
        } catch (Exception $e) {
            report($e);
        }

        parent::__construct();
    }

    private function setAuthorizationToken()
    {
        if (FoxUtils::isProduction()) {
            $this->authorizationToken = base64_encode(
                getenv('GET_NET_CLIENT_ID_PRODUCTION').':'.getenv('GET_NET_CLIENT_SECRET_PRODUCTION')
            );

            $this->postFieldsAccessToken = 'scope=oob&grant_type=client_credentials';
        } else {
            $this->authorizationToken = base64_encode(
                getenv('GET_NET_CLIENT_ID_SANDBOX').':'.getenv('GET_NET_CLIENT_SECRET_SANDBOX')
            );

            $this->postFieldsAccessToken = 'scope=mgm&grant_type=client_credentials';
        }
    }

    public function getMerchantId()
    {
        if (FoxUtils::isProduction()) {
            return env('GET_NET_MERCHANT_ID_PRODUCTION');
        }

        return env('GET_NET_MERCHANT_ID_SANDBOX');
    }

    public function getAuthorizationHeader()
    {
        return [
            'authorization: Bearer '.$this->accessToken,
            'Content-Type: application/json',
        ];
    }

    public function setSellerId()
    {
        if (FoxUtils::isProduction()) {
            $this->sellerId = getenv('GET_NET_SELLER_ID_PRODUCTION');
        } else {
            $this->sellerId = getenv('GET_NET_SELLER_ID_SANDBOX');
        }
    }

    /**
     * Endpoint para solicitação de extrato eletrônico
     * @method GET
     * @param $pagination  | Primeira chamada sempre se inicia com o número 1.
     * @param  null  $subsellerId  |
     * @return bool|string
     */
    public function getStatement($subsellerId = null, $pagination = null)
    {
        $queryParameters = [
            'seller_id' => $this->sellerId,
            'transaction_date_init' => '2020-06-01',
            'transaction_date_end' => '2020-07-10'
        ];

        if (!is_null($subsellerId)) {
            $queryParameters = $queryParameters + ['subseller_id' => $subsellerId];
        }

        if (is_null($pagination)) {
            $url = 'v1/mgm/statement?'.http_build_query($queryParameters);
        } else {
            $queryParameters = $queryParameters + ['page' => $pagination];
            $url = 'v1/mgm/paginatedstatement?'.http_build_query($queryParameters);
        }

        return $this->sendCurl($url, 'GET');
    }

    public function checkPfCompanyRegister(string $cpf, $companyId)
    {
        $url = 'v1/mgm/pf/callback/'.$this->getMerchantId().'/'.$cpf;

        return $this->sendCurl($url, 'GET', null, $companyId);
    }

    public function checkAvailablePaymentPlansPf()
    {
        $url = 'v1/mgm/pf/consult/paymentplans/'.$this->getMerchantId();

        return $this->sendCurl($url, 'GET');
    }

    public function createPfCompany(Company $company)
    {
        $url = 'v1/mgm/pf/create-presubseller';
        $data = $this->getPrepareDataCreatePfCompany($company);

        return $this->sendCurl($url, 'POST', $data, $company->id);
    }

    public function complementPfCompany(Company $company)
    {
        $url = 'v1/mgm/pf/complement';
        $data = $this->getPrepareDataComplementPfCompany($company);

        return $this->sendCurl($url, 'PUT', $data, $company->id);
    }

    public function disqualifyPfCompany($subsellerGetnetId)
    {
        $url = 'v1/mgm/pf/de-accredit/'.$this->getMerchantId().'/'.$subsellerGetnetId;

        return $this->sendCurl($url, 'POST');
    }

    public function updatePfCompany(Company $company)
    {
        $url = 'v1/mgm/pf/update-subseller';
        $data = $this->getPrepareDataUpdatePfCompany($company);

        return $this->sendCurl($url, 'PUT', $data, $company->id);
    }

    public function checkPaymentPlans()
    {
        $url = "v1/mgm/pj/consult/paymentplans/{$this->getMerchantId()}";

        return $this->sendCurl($url, 'GET');
    }

    public function checkComplementPjCompanyRegister($cnpj)
    {
        $url = 'v1/mgm/pj/consult/'.$this->getMerchantId().'/'.$cnpj;

        return $this->sendCurl($url, 'GET');
    }

    public function checkPjCompanyRegister($cnpj, $companyId)
    {
        $url = 'v1/mgm/pj/callback/'.$this->getMerchantId().'/'.$cnpj;

        return $this->sendCurl($url, 'GET', null, $companyId);
    }

    public function checkAvailablePaymentPlansPj()
    {
        $url = 'v1/mgm/pj/consult/paymentplans/'.$this->getMerchantId();

        return $this->sendCurl($url, 'GET');
    }

    public function createPjCompany(Company $company)
    {
        $url = 'v1/mgm/pj/create-presubseller';
        $data = $this->getPrepareDataCreatePjCompany($company);

        return $this->sendCurl($url, 'POST', $data, $company->id);
    }

    public function complementPjCompany(Company $company)
    {
        $url = 'v1/mgm/pj/complement';
        $data = $this->getPrepareDataComplementPjCompany($company);

        return $this->sendCurl($url, 'PUT', $data, $company->id);
    }

    public function updatePjCompany(Company $company)
    {
        $url = 'v1/mgm/pj/update-subseller';
        $data = $this->getPrepareDataUpdatePjCompany($company);

        return $this->sendCurl($url, 'PUT', $data, $company->id);
    }

    public function disqualifyPjCompany($subsellerGetnetId)
    {
        $url = 'v1/mgm/pj/de-accredit/'.$this->getMerchantId().'/'.$subsellerGetnetId;

        return $this->sendCurl($url, 'POST');
    }

}
