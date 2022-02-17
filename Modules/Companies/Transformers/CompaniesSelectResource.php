<?php

namespace Modules\Companies\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Services\CompanyService;
use Vinkla\Hashids\Facades\Hashids;

class CompaniesSelectResource extends JsonResource
{
    public function toArray($request): array
    {
        $companyService = new CompanyService();
        $companyDocumentValidated = $companyService->isDocumentValidated($this->id);

        return [
            'id' => Hashids::encode($this->id),
            'country' => $this->country,
            'name' => $this->company_type == 1 ? 'Pessoa física' : $this->fantasy_name,
            'company_document_status' => ($companyDocumentValidated) ? 'approved' : 'pending',
            'company_has_sale_before_getnet' => auth()->user()->has_sale_before_getnet,
            'active_flag' => $this->active_flag,
            'has_pix_key' => $this->has_pix_key,
            'company_type' => $this->present()->getCompanyType($this->company_type),
            'user_address_document_status' => (new User())->present()->getAddressDocumentStatus(
                $this->user->address_document_status
            ),
            'user_personal_document_status' => (new User())->present()->getAddressDocumentStatus(
                $this->user->personal_document_status
            ),
        ];
    }
}
