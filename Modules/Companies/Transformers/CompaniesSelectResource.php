<?php

namespace Modules\Companies\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Services\CompanyService;
use Vinkla\Hashids\Facades\Hashids;

class CompaniesSelectResource extends JsonResource
{
    public function toArray($request)
    {
        $companyService = new CompanyService();
        $companyDocumentValidated = $companyService->isDocumentValidated($this->id);
        $hasSaleCielo = Sale::where('owner_id', auth()->user()->account_owner_id)
            ->orWhere('affiliate_id', auth()->user()->account_owner)
            ->whereNotIn('gateway_id',  [14, 15])->exists();

        return [
            'id' => Hashids::encode($this->id),
            'country' => $this->country,
            'name' => $this->company_type == 1 ? 'Pessoa física' : $this->fantasy_name,
            'company_document_status' => ($companyDocumentValidated) ? 'approved' : 'pending',
            'antecipation_enabled_flag' => $this->user->antecipation_enabled_flag,
            'capture_transaction_enabled' => $this->capture_transaction_enabled,
            'company_has_sales_in_cielo' => $hasSaleCielo,
            'active_flag' => $this->active_flag,
            'company_type' => $this->present()->getCompanyType($this->company_type),
            'user_address_document_status' => (new User())->present()->getAddressDocumentStatus($this->user->address_document_status),
            'user_personal_document_status' => (new User())->present()->getAddressDocumentStatus($this->user->personal_document_status),
        ];
    }
}
