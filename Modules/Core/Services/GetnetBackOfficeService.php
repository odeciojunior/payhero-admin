<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use LogicException;
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

    public const STATEMENT_DATE_SCHEDULE = 'schedule';
    public const STATEMENT_DATE_TRANSACTION = 'transaction';
    public const STATEMENT_DATE_LIQUIDATION = 'liquidation';

    private string $urlCredentialAccessToken = 'credenciamento/auth/oauth/v2/token';
    public string $postFieldsAccessToken, $authorizationToken;
    protected ?string $sellerId, $statementSubSellerId = null;
    protected Carbon $statementStartDate, $statementEndDate;
    protected ?string $statementDateField, $statementSaleHashId = '';
    protected int $statementPage = 1;

    /**
     * @return string|null
     */
    public function getStatementSubSellerId(): ?string
    {
        return $this->statementSubSellerId;
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
     * @return Carbon
     */
    public function getStatementStartDate(): Carbon
    {
        return $this->statementStartDate;
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
     * @return Carbon
     */
    public function getStatementEndDate(): Carbon
    {
        return $this->statementEndDate;
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
     * @return string|null
     */
    public function getStatementDateField(): ?string
    {
        return $this->statementDateField;
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
        return $this->statementSaleHashId;
    }

    /**
     * @param string|null $statementSaleHashId
     * @return GetnetBackOfficeService
     */
    public function setStatementSaleHashId(?string $statementSaleHashId): GetnetBackOfficeService
    {
        $this->statementSaleHashId = $statementSaleHashId;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatementPage(): int
    {
        return $this->statementPage;
    }

    /**
     * @param int $statementPage
     * @return GetnetBackOfficeService
     */
    public function setStatementPage(int $statementPage): GetnetBackOfficeService
    {
        $this->statementPage = $statementPage;
        return $this;
    }

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
     * @return bool|string
     */
    public function getStatement()
    {
        if (empty($this->getStatementDateField())) {
            throw new LogicException('É obrigatório especificar um campo de data para a busca');
        } elseif (!in_array($this->getStatementDateField(), [self::STATEMENT_DATE_SCHEDULE, self::STATEMENT_DATE_LIQUIDATION, self::STATEMENT_DATE_TRANSACTION])) {
            throw new LogicException('O campo de data para a busca deve ser "' . self::STATEMENT_DATE_SCHEDULE . '", "' . self::STATEMENT_DATE_LIQUIDATION . '" ou "' . self::STATEMENT_DATE_TRANSACTION . '"');
        }

        $startDate = $this->getStatementStartDate()->format('Y-m-d');
        $endDate = $this->getStatementEndDate()->format('Y-m-d');

        $startDate .= ' 00:00:00';
        $endDate .= ' 23:59:59';

        $queryParameters = [
            'seller_id' => $this->sellerId,
            $this->getStatementDateField() . '_date_init' => $startDate,
            $this->getStatementDateField() . '_date_end' => $endDate,
        ];

        if (!empty($this->getStatementSubSellerId())) {

            $queryParameters['subseller_id'] = $this->getStatementSubSellerId();
        }

        if (!empty($this->getStatementSaleHashId())) {
            $sale = Sale::find(current(Hashids::connection('sale_id')->decode($this->getStatementSaleHashId())));

            if ($sale) {
                if ($sale->created_at > '2020-10-30 13:28:51.0') {
                    $orderId = $this->getStatementSaleHashId() . '-' . $sale->id . '-' . $sale->attempts;
                } else {
                    $orderId = $this->getStatementSaleHashId() . '-' . $sale->attempts;
                }

                $queryParameters['order_id'] = $orderId;
            }
        }

        if (request('debug')) {

            echo '<pre>';
            print_r($queryParameters);
            echo '</pre>';
            echo '<hr>';
        }

        // https://developers.getnet.com.br/backoffice#tag/Statement
        // https://api-homologacao.getnet.com.br/v1/mgm/paginatedstatement
        $url = 'v1/mgm/statement?' . http_build_query($queryParameters);
        //$url = 'v1/mgm/statement/get-paginated-statement?' . http_build_query($queryParameters);

        //dd($startDate, $endDate, $url);
        return $this->sendCurl($url, 'GET', null, null, false);
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

}
