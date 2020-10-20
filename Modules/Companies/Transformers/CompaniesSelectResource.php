<?php

namespace Modules\Companies\Transformers;

use Modules\Core\Services\CompanyService;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class CompaniesSelectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $companyService           = new CompanyService();
        $companyDocumentValidated = $companyService->isDocumentValidated($this->id);

        return [
            'id'                        => Hashids::encode($this->id),
            'country'                   => $this->country,
            'name'                      => $this->company_type == 1 ? 'Pessoa física' : $this->fantasy_name,
            'company_document_status'   => ($companyDocumentValidated) ? 'approved' : 'pending',
            'antecipation_enabled_flag' => $this->user->antecipation_enabled_flag,
            'capture_transaction_enabled' => $this->capture_transaction_enabled,
            'active_flag'               => $this->active_flag,
        ];
    }
}
