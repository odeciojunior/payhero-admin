<?php

namespace Modules\Companies\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class CompanyResource
 * @package Modules\Companies\Transformers
 */
class CompanyResource extends JsonResource
{
    /**
     * The resource instance.
     * @var Company
     */
    public $resource;

    /**
     * @param Request $request
     * @return array
     * @throws PresenterException
     */
    public function toArray($request)
    {
        $presenter = $this->resource->present();

        $documentStatus = $presenter->allStatusPending() ? $presenter->getStatus(3) : $presenter->getStatus(1);
        $companyService = new CompanyService();
        $refusedDocuments = $companyService->getRefusedDocuments($this->resource->id);
        $user = $this->resource->user;
        $project = UserProject::where('company_id', $this->resource->id)->first();

        return [
            'id_code' => Hashids::encode($this->resource->id),
            'user_code' => Hashids::encode($this->resource->user_id),
            'business_website' => $this->resource->business_website ?? '',
            'support_email' => $this->resource->support_email ?? '',
            'support_telephone' => $this->resource->support_telephone ?? '',
            'fantasy_name' => $this->resource->company_type == 1 ? 'Pessoa física' : $this->resource->fantasy_name ?? '',
            'company_document' => strlen($this->resource->company_document) == 14 ? FoxUtils::mask($this->resource->company_document,
                '##.###.###/####-##') : (strlen($this->resource->company_document) == 11 ? FoxUtils::mask($this->resource->company_document,
                '###.###.###-##') : $this->resource->company_document),
            'zip_code' => $this->resource->zip_code ?? '',
            'country' => $this->resource->country ?? '',
            'country_translated' => $this->resource->country ? __('definitions.enum.country.' . $this->resource->country) : '',
            'state' => $this->resource->state ?? '',
            'city' => $this->resource->city ?? '',
            'street' => $this->resource->street ?? '',
            'complement' => $this->resource->complement ?? '',
            'neighborhood' => $this->resource->neighborhood ?? '',
            'number' => $this->resource->number ?? '',
            'bank' => $this->resource->bank ?? '',
            'agency' => $this->resource->agency ?? '',
            'agency_digit' => $this->resource->agency_digit ?? '',
            'account' => $this->resource->account ?? '',
            'account_digit' => $this->resource->account_digit ?? '',
            'document_status' => $documentStatus,
            'bank_document_status' => $presenter->getBankDocumentStatus($this->resource->bank_document_status),
            'address_document_status' => $presenter->getAddressDocumentStatus($this->resource->address_document_status),
            'contract_document_status' => $presenter->getContractDocumentStatus($this->resource->contract_document_status),
            'bank_document_translate' => __('definitions.enum.status.' . $presenter->getBankDocumentStatus()),
            'address_document_translate' => __('definitions.enum.status.' . $presenter->getAddressDocumentStatus()),
            'contract_document_translate' => __('definitions.enum.status.' . $presenter->getContractDocumentStatus()),
            'refusedDocuments' => $refusedDocuments,
            'type' => $this->company_type,
            'type_company' => $presenter->getCompanyType($this->company_type),
            'user_address_document_status' => $presenter->getAddressDocumentStatus($user->address_document_status),
            'user_personal_document_status' => $presenter->getAddressDocumentStatus($user->personal_document_status),
            'patrimony' => $this->patrimony ?? '',
            'state_fiscal_document_number' => $this->state_fiscal_document_number ?? '',
            'business_entity_type' => $this->business_entity_type ?? '',
            'economic_activity_classification_code' => $this->economic_activity_classification_code ?? '',
            'monthly_gross_income' => $this->monthly_gross_income ?? '',
            'federal_registration_status' => $this->federal_registration_status ?? '',
            'founding_date' => $this->founding_date ?? '',
            'account_type' => $this->account_type ?? '',
            'federal_registration_status_date' => $this->federal_registration_status_date ?? '',
            'social_value' => $this->social_value ?? '',
            'document_issue_date' => !empty($this->document_issue_date) ? Carbon::parse($this->document_issue_date)
                ->format('Y-m-d') : '',
            'document_issuer' => $this->document_issuer ?? '',
            'document_issuer_state' => $this->document_issuer_state ?? '',
            'document_number' => $this->document_number ?? '',
            'active_flag' => $this->active_flag,
            'has_project' => !empty($project),
            'capture_transaction_enabled' => $this->capture_transaction_enabled,
            'credit_card_tax' => $this->credit_card_tax,
            'boleto_tax' => $this->boleto_tax,
            'transaction_rate' => $this->transaction_rate,
            'credit_card_release_money' => $this->credit_card_release_money_days,
            'boleto_release_money' => $this->boleto_release_money_days,
            'gateway_tax' => $this->gateway_tax,
            'installment_tax' => $this->installment_tax,
        ];
    }
}
