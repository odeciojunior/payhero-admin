<?php

namespace Modules\Core\Services;

use Exception;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
use Modules\Core\Traits\GetnetPrepareCompanyData;

/**
 * Class GetnetService
 * @package Modules\Core\Services
 */
class GetnetBackOfficeService extends GetnetService
{
    use GetnetPrepareCompanyData;

    /**
     * @var string
     */
    private $urlCredentialAcessToken = 'credenciamento/auth/oauth/v2/token';
    /**
     * @var string
     */
    private $postFieldsAcessToken = 'scope=mgm&grant_type=client_credentials';

    public $authorizationToken;

    /**
     * GetnetBackOfficeService constructor.
     */
    public function __construct()
    {
        try {
            $this->authorizationToken = base64_encode(
                getenv('GET_NET_CLIENT_ID') . ':' . getenv('GET_NET_CLIENT_SECRET')
            );
            $this->setAccessToken($this->urlCredentialAcessToken, $this->postFieldsAcessToken);
        } catch (Exception $e) {
        }

        parent::__construct();
    }


    /**
     * @return mixed
     */
    public function getMerchantId()
    {
        return env('GET_NET_MERCHANT_ID');
    }

    /**
     * @return string[]
     */
    public function getAuthorizationHeader()
    {
        return [
            'authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
        ];
    }

    /**
     * Endpoint para solicitação de extrato eletrônico
     * @method GET
     * @param $pagination | Primeira chamada sempre se inicia com o número 1.
     * @return bool|string
     */
    public function getStatement($pagination = null)
    {
        $queryParameters = [
            'seller_id' => getenv('GET_NET_SELLER_ID'),
            'transaction_date_init' => '2020-06-01',
            'transaction_date_end' => '2020-07-09'
        ];

        if (is_null($pagination)) {
            $url = 'v1/mgm/statement?' . http_build_query($queryParameters);
        } else {
            $queryParameters = $queryParameters + ['page' => $pagination];
            $url = 'v1/mgm/paginatedstatement?' . http_build_query($queryParameters);
        }

        return $this->sendCurl($url, 'GET');
    }

    /**
     * Endpoint Consulta situação cadastral de um CPF
     * Method GET
     * @param string $cpf
     * @return bool|string
     */
    public function checkPfCompanyRegister(string $cpf)
    {
        $url = 'v1/mgm/pf/callback/' . $this->getMerchantId() . '/' . $cpf;

        return $this->sendCurl($url, 'GET');
    }

    /**
     * Endpoint Consulta planos de pagamentos configurados para a loja PF
     * Method GET
     */
    public function checkAvailablePaymentPlansPf()
    {
        $url = 'v1/mgm/pf/consult/paymentplans/' . $this->getMerchantId();

        return $this->sendCurl($url, 'GET');
    }

    /**
     * Endpoint Cria pré-cadastro da loja PF
     * Method POST
     * @param Company $company
     * @return bool|string
     * @throws PresenterException
     */
    public function createPfCompany(Company $company)
    {
        $url = 'v1/mgm/pf/create-presubseller';
        $data = $this->getPrepareDataCreatePfCompany($company);

        return $this->sendCurl($url, 'POST', $data,$company->id);
    }

    /**
     * Endpoint Complementar pré-cadastro da loja quando necessario
     * Method PUT
     * @param Company $company
     * @return bool|string
     */
    public function complementPfCompany(Company $company)
    {
        $url = 'v1/mgm/pf/complement';
        $data = $this->getPrepareDataComplementPfCompany($company);

        return $this->sendCurl($url, 'PUT', $data,$company->id);
    }

    /**
     * Endpoint para descredenciar Loja
     * Method POST
     * @param $subsellerGetnetId
     * @return bool|string
     */
    public function disqualifyPfCompany($subsellerGetnetId)
    {
        $url = 'v1/mgm/pf/de-accredit/' . $this->getMerchantId() . '/' . $subsellerGetnetId;

        return $this->sendCurl($url, 'POST');
    }

    /**
     * Endpoint Atualiza cadastro da loja
     * Method PUT
     * @param Company $company
     * @return bool|string
     * @throws PresenterException
     */
    public function updatePfCompany(Company $company)
    {
        $url = 'v1/mgm/pf/update-subseller';
        $data = $this->getPrepareDataUpdatePfCompany($company);

        return $this->sendCurl($url, 'PUT', $data,$company->id);
    }

    /**
     * Endpoint Consulta complemento cadastral de um CNPJ
     * @param $cnpj
     * @return bool|string
     */
    public function checkComplementPjCompanyRegister($cnpj)
    {
        $url = 'v1/mgm/pj/consult/' . $this->getMerchantId() . '/' . $cnpj;

        return $this->sendCurl($url, 'GET');
    }

    /**
     * Endpoint Consulta situação cadastral de um CNPJ
     * Method GET
     * @param $cnpj
     * @return bool|string
     */
    public function checkPjCompanyRegister($cnpj)
    {
        $url = 'v1/mgm/pj/callback/' . $this->getMerchantId() . '/' . $cnpj;

        return $this->sendCurl($url, 'GET');
    }

    /**
     * Endpoint Consulta planos de pagamentos connfigurados para loja PJ
     * Method GET
     */
    public function checkAvailablePaymentPlansPj()
    {
        $url = 'v1/mgm/pj/consult/paymentplans/' . $this->getMerchantId();

        return $this->sendCurl($url, 'GET');
    }

    /**
     * Endpoint Cria pré-cadastro da loja
     * Method POST
     * @param Company $company
     * @return bool|string
     * @throws PresenterException
     */
    public function createPjCompany(Company $company)
    {
        $url = 'v1/mgm/pj/create-presubseller';
        $data = $this->getPrepareDataCreatePjCompany($company);

        return $this->sendCurl($url, 'POST', $data,$company->id);
    }

    /**
     * Endpoint Complementa pré-cadastro da loja se necessario
     * Method PUT
     * @param Company $company
     * @return bool|string
     */
    public function complementPjCompany(Company $company)
    {
        $url = 'v1/mgm/pj/complement';
        $data = $this->getPrepareDataComplementPjCompany($company);

        return $this->sendCurl($url, 'PUT', $data,$company->id);
    }

    /**
     * Endpoint Atualiza cadastro da loja
     * Method PUT
     * @param Company $company
     * @return bool|string
     * @throws PresenterException
     */
    public function updatePjCompany(Company $company)
    {
        $url = 'v1/mgm/pj/update-subseller';
        $data = $this->getPrepareDataUpdatePjCompany($company);

        return $this->sendCurl($url, 'PUT', $data,$company->id);
    }

    /**
     * Endpoint Descredenciar Loja PJ
     * Method POST
     * @param $subsellerGetnetId
     * @return bool|string
     */
    public function disqualifyPjCompany($subsellerGetnetId)
    {
        $url = 'v1/mgm/pj/de-accredit/' . $this->getMerchantId() . '/' . $subsellerGetnetId;

        return $this->sendCurl($url, 'POST');
    }

}
