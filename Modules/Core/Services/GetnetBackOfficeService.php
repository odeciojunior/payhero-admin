<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use LogicException;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\PendingDebt;
use Modules\Core\Entities\Sale;
use Modules\Core\Traits\GetnetPrepareCompanyData;

/**
 * Class GetnetBackOfficeService
 * @package Modules\Core\Services
 */
class GetnetBackOfficeService extends GetnetBaseService
{
    use GetnetPrepareCompanyData;

    public const STATEMENT_DATE_SCHEDULE = 'schedule';
    public const STATEMENT_DATE_TRANSACTION = 'transaction';
    public const STATEMENT_DATE_LIQUIDATION = 'liquidation';
    public string $postFieldsAccessToken, $authorizationToken;
    protected ?string $sellerId, $statementSubSellerId = null;
    protected Carbon $statementStartDate, $statementEndDate;
    protected ?string $statementDateField, $statementSaleHashId = '';
    protected int $saleId = 0;
    private string $urlCredentialAccessToken = 'credenciamento/auth/oauth/v2/token';

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

    public function setSellerId()
    {
        if (FoxUtils::isProduction()) {
            $this->sellerId = getenv('GET_NET_SELLER_ID_PRODUCTION');
        } else {
            $this->sellerId = getenv('GET_NET_SELLER_ID_SANDBOX');
        }
    }

    /**
     * @param string|null $statementSubSellerId
     * @return GetnetBackOfficeService
     */
    public function setStatementSubSellerId(?string $statementSubSellerId): GetnetBackOfficeService
    {
        $this->statementSubSellerId = $statementSubSellerId;
        return $this;
    }

    /**
     * @param Carbon $statementEndDate
     * @return GetnetBackOfficeService
     */
    public function setStatementEndDate(Carbon $statementEndDate): GetnetBackOfficeService
    {
        $this->statementEndDate = $statementEndDate;
        return $this;
    }

    /**
     * @param string|null $statementSaleHashId
     * @return GetnetBackOfficeService
     */
    public function setStatementSaleHashId(?string $statementSaleHashId): GetnetBackOfficeService
    {
        if (Sale::find(hashids_decode($statementSaleHashId, 'sale_id'))->api_flag) {
            $statementSaleHashId = Sale::find(hashids_decode($statementSaleHashId, 'sale_id'))->productsSaleApi->first()->item_id;
        }

        $this->statementSaleHashId = $statementSaleHashId;
        return $this;
    }

    public function getAuthorizationHeader(): array
    {
        return [
            'authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
        ];
    }

    /**
     * Endpoint para solicitação de extrato eletrônico
     * @method GET
     * @return bool|string
     */
    public function getStatement($orderId = null)
    {
        if (empty($this->getStatementDateField()) && empty($this->getStatementSaleHashId())) {
            throw new LogicException('É obrigatório especificar um campo de data para a busca quando não é enviado um OrderId');
        } elseif (!empty($this->getStatementDateField()) && !in_array($this->getStatementDateField(),
                [self::STATEMENT_DATE_SCHEDULE, self::STATEMENT_DATE_LIQUIDATION, self::STATEMENT_DATE_TRANSACTION])) {
            throw new LogicException('O campo de data para a busca deve ser "' . self::STATEMENT_DATE_SCHEDULE . '", "' . self::STATEMENT_DATE_LIQUIDATION . '" ou "' . self::STATEMENT_DATE_TRANSACTION . '"');
        }

        if (empty($orderId)) {
            $queryParameters = ['seller_id' => $this->sellerId];
        } else {
            $queryParameters = [
                'order_id' => $orderId,
                'seller_id' => $this->sellerId,
            ];
        }

        if ($this->getStatementStartDate() && $this->getStatementEndDate()) {
            $startDate = $this->getStatementStartDate()->format('Y-m-d');
            $endDate = $this->getStatementEndDate()->format('Y-m-d');

            $startDate .= ' 00:00:00';
            $endDate .= ' 23:59:59';

            $queryParameters[$this->getStatementDateField() . '_date_init'] = $startDate;
            $queryParameters[$this->getStatementDateField() . '_date_end'] = $endDate;
        }

        if (!empty($this->getStatementSubSellerId())) {
            $queryParameters['subseller_id'] = $this->getStatementSubSellerId();
        }

        if (!empty($this->getStatementSaleHashId())) {
            $sale = Sale::find(hashids_decode($this->getStatementSaleHashId(), 'sale_id'));

            if ($sale) {
                $this->saleId = $sale->id;
                $queryParameters['order_id'] = $sale->gateway_order_id;
            }
        }

        if (request('debug')) {
            echo '<pre>';
            print_r($queryParameters);
            echo '</pre>';
            echo '<hr>';
        }

        $url = 'v1/mgm/statement?' . http_build_query($queryParameters);
        return $this->sendCurl($url, 'GET', null, null, false);
    }

    /**
     * @return string|null
     */
    public function getStatementDateField(): ?string
    {
        return $this->statementDateField ?? null;
    }

    /**
     * @param string|null $statementDateField
     * @return GetnetBackOfficeService
     */
    public function setStatementDateField(?string $statementDateField): GetnetBackOfficeService
    {
        $this->statementDateField = $statementDateField;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatementSaleHashId(): ?string
    {
        return $this->statementSaleHashId ?? null;
    }

    /**
     * @return Carbon|null
     */
    public function getStatementStartDate(): ?Carbon
    {
        return $this->statementStartDate ?? null;
    }

    /**
     * @param Carbon $statementStartDate
     * @return GetnetBackOfficeService
     */
    public function setStatementStartDate(Carbon $statementStartDate): GetnetBackOfficeService
    {
        $this->statementStartDate = $statementStartDate;
        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getStatementEndDate(): ?Carbon
    {
        return $this->statementEndDate ?? null;
    }

    /**
     * @return string|null
     */
    public function getStatementSubSellerId(): ?string
    {
        return $this->statementSubSellerId;
    }

    public function getSaleId(): int
    {
        return $this->saleId;
    }

    public function checkPfCompanyRegister(string $cpf, $companyId)
    {
        $url = 'v1/mgm/pf/callback/' . $this->getMerchantId() . '/' . FoxUtils::onlyNumbers($cpf);

        return $this->sendCurl($url, 'GET', null, $companyId);
    }

    public function getMerchantId()
    {
        if (FoxUtils::isProduction()) {
            return env('GET_NET_MERCHANT_ID_PRODUCTION');
        }

        return env('GET_NET_MERCHANT_ID_SANDBOX');
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
        $url = 'v1/mgm/pj/consult/' . $this->getMerchantId() . '/' . FoxUtils::onlyNumbers($cnpj);

        return $this->sendCurl($url, 'GET');
    }

    public function checkPjCompanyRegister($cnpj, $companyId)
    {
        $url = 'v1/mgm/pj/callback/' . $this->getMerchantId() . '/' . FoxUtils::onlyNumbers($cnpj);

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
        $data = $this->getDataToCreatePjCompany($company);

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

    public function getStatementFromManager(array $filters = [], int $page = null)
    {
        $queryParameters = $filters;
        $queryParameters['seller_id'] = $this->sellerId;

        if (is_null($page)) {
            $url = 'v1/mgm/statement?' . http_build_query($queryParameters);
        } else {
            $queryParameters['page'] = $page;
            $url = 'v1/mgm/paginatedstatement?' . http_build_query($queryParameters);
        }

        return $this->sendCurl($url, 'GET');
    }

    public function getStatementWithoutSaveRequest(array $filters = [], int $page = null)
    {
        $queryParameters = $filters;
        $queryParameters['seller_id'] = $this->sellerId;

        $url = 'v1/mgm/statement?' . http_build_query($queryParameters);
        return $this->sendCurlWithoutSaveRequest($url, 'GET');
    }

    private function sendCurlWithoutSaveRequest(string $url, string $method, $data = null, $companyId = null)
    {
        $curl = curl_init($this->getUrlApi() . $url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        if (!is_null($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

    public function requestAdjustment(AdjustmentRequest $adjustmentRequest): AdjustmentResponse
    {
        if ($adjustmentRequest->isValid()) {
            $url = 'v1/mgm/adjustment/request-adjustments';
            $response = $this->sendCurl($url, 'POST', $adjustmentRequest->formatToSendApi());
            $response = json_decode($response);

            $adjustmentResponse = new AdjustmentResponse();
            $adjustmentResponse->code = $response->cod ?? '';
            $adjustmentResponse->errorMessage = $response->msg_Erro ?? '';
            $adjustmentResponse->errorCode = $response->cod_Erro ?? '';
            $adjustmentResponse->isSuccess = is_null($response->msg_Erro);

            if (
                $adjustmentResponse->isSuccess &&
                $adjustmentRequest->getTypeAdjustment() == AdjustmentRequest::DEBIT_ADJUSTMENT
            ) {
                $this->saveAdjustment($adjustmentRequest, $response);
            }

            return $adjustmentResponse;
        } else {
            throw new LogicException('Todos os parâmetros de AdjustmentRequest são obrigatórios');
        }
    }

    private function saveAdjustment($adjustmentRequest, $response)
    {
        try {
            PendingDebt::create(
                [
                    'type' => AdjustmentRequest::DEBIT_ADJUSTMENT,
                    'sale_id' => $adjustmentRequest->getSaleId(),
                    'request_date' => Carbon::parse($response->date_adjustment)->format('Y-m-d H:i:s'),
                    'company_id' => $adjustmentRequest->getCompanyId(),
                    'value' => $adjustmentRequest->getAmount(),
                    'reason' => $adjustmentRequest->getDescription(),
                ]
            );
        } catch (Exception $e) {
            report($e);
        }
    }
}
