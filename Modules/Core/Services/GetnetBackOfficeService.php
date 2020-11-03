<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Traits\GetnetPrepareCompanyData;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class GetnetService
 * @package Modules\Core\Services
 */
class GetnetBackOfficeService extends GetnetService
{
    use GetnetPrepareCompanyData;

    private string $urlCredentialAccessToken = 'credenciamento/auth/oauth/v2/token';

    public string $postFieldsAccessToken;

    public string $authorizationToken;

    private string $sellerId;

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
                getenv('GET_NET_CLIENT_ID_PRODUCTION') . ':' . getenv('GET_NET_CLIENT_SECRET_PRODUCTION')
            );

            $this->postFieldsAccessToken = 'scope=oob&grant_type=client_credentials';
        } else {
            $this->authorizationToken = base64_encode(
                getenv('GET_NET_CLIENT_ID_SANDBOX') . ':' . getenv('GET_NET_CLIENT_SECRET_SANDBOX')
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
            'authorization: Bearer ' . $this->accessToken,
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
     * @param null $subSellerId
     * @return bool|string
     */
    public function getStatement($subSellerId = null)
    {
        try {
            $dates = explode(' - ', request('dateRange') ?? '');

            if (is_array($dates) && count($dates) == 1) {
                $startDate = $dates[0];
                $endDate = $dates[0];
            }
        } catch (Exception $exception) {
        }

        if (!isset($startDate) || !isset($endDate)) {
            $today = today()->format('Y-m-d');
            $startDate = $today;
            $endDate = $today;
        }

        $startDate .= ' 00:00:00';
        $endDate .= ' 23:59:59';

        $queryParameters = [
            'seller_id' => $this->sellerId,
            // [Required] Id do marketplace (SellerId)
            // 'transaction_date_init' => $startDate,
            // Data de captura da transação Início.
            // 'transaction_date_end' => $endDate,
            // Data de captura da transação Fim.
            /*'liquidation_date_init' => $startDate,                                    // Data Liquidação Inicial - Emissão do extrato somente com dados da liquidação do período informado.
            'liquidation_date_end' => $endDate,                                         // Data Liquidação Final - Emissão do extrato somente com dados da liquidação do período informado.
            'confirmation_date_init' => $startDate,                                     // Data de confirmação inicial da transação.
            'confirmation_date_end' => $endDate,*/
            // Data de confirmação da transação Fim.
//            'page' => request('page') ?? 1,
        ];

        if (request('statement_data_type') == 'liquidation_date') {
            $queryParameters += ['liquidation_date_init' => $startDate, 'liquidation_date_end' => $startDate];
        } else {
            $queryParameters += ['transaction_date_init' => $startDate, 'transaction_date_end' => $startDate];
        }

        if (!empty($subSellerId)) {
            $queryParameters['subseller_id'] = $subSellerId;
        }

        if (request('sale')) {
            $sale = Sale::find(current(Hashids::connection('sale_id')->decode(request('sale'))));

            if ($sale) {
                try {
                    $gatewayResult = json_decode($sale->saleGatewayRequests->last()->gateway_result);
                    if (isset($gatewayResult->order_id)) {
                        $queryParameters['order_id'] = $gatewayResult->order_id;
                    }
                } catch (Exception $exception) {
                }
            }
        }

        // https://developers.getnet.com.br/backoffice#tag/Statement
        // https://api-homologacao.getnet.com.br/v1/mgm/paginatedstatement
        $url = 'v1/mgm/statement?' . http_build_query($queryParameters);

        $data = $this->sendCurl($url, 'GET');
        return $data;
    }

    public function checkPfCompanyRegister(string $cpf, $companyId)
    {
        $url = 'v1/mgm/pf/callback/' . $this->getMerchantId() . '/' . $cpf;

        return $this->sendCurl($url, 'GET', null, $companyId);
    }

    public function checkAvailablePaymentPlansPf()
    {
        $url = 'v1/mgm/pf/consult/paymentplans/' . $this->getMerchantId();

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
        $url = 'v1/mgm/pf/de-accredit/' . $this->getMerchantId() . '/' . $subsellerGetnetId;

        return $this->sendCurl($url, 'POST');
    }

    public function updatePfCompany(Company $company, $dataUpdate)
    {
        $data = array_merge($this->getPrepareDataUpdatePfCompany($company), $dataUpdate);

        return $this->sendCurl('v1/mgm/pf/update-subseller', 'PUT', $data, $company->id);
    }

    public function checkPaymentPlans()
    {
        $url = "v1/mgm/pj/consult/paymentplans/{$this->getMerchantId()}";

        return $this->sendCurl($url, 'GET');
    }

    public function checkComplementPjCompanyRegister($cnpj)
    {
        $url = 'v1/mgm/pj/consult/' . $this->getMerchantId() . '/' . $cnpj;

        return $this->sendCurl($url, 'GET');
    }

    public function checkPjCompanyRegister($cnpj, $companyId)
    {
        $url = 'v1/mgm/pj/callback/' . $this->getMerchantId() . '/' . $cnpj;

        return $this->sendCurl($url, 'GET', null, $companyId);
    }

    public function checkAvailablePaymentPlansPj()
    {
        $url = 'v1/mgm/pj/consult/paymentplans/' . $this->getMerchantId();

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

    public function updatePjCompany(Company $company, $dataUpdate)
    {
        $data = array_merge($this->getPrepareDataUpdatePjCompany($company), $dataUpdate);

        return $this->sendCurl('v1/mgm/pj/update-subseller', 'PUT', $data, $company->id);
    }

    public function disqualifyPjCompany($subsellerGetnetId)
    {
        $url = 'v1/mgm/pj/de-accredit/' . $this->getMerchantId() . '/' . $subsellerGetnetId;

        return $this->sendCurl($url, 'POST');
    }

}
