<?php

namespace Modules\Companies\Transformers;

use Illuminate\Http\Request;
use Modules\Core\Entities\UserProject;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Services\CompanyService;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CompanyCpfResource
 * @package Modules\Companies\Transformers
 */
class CompanyCpfResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $presenter = $this->resource->present();
        //        $documentStatus = $presenter->allStatusPending() ? $presenter->getStatus(3) : $presenter->getStatus(1);
        //        $companyService = new CompanyService();

        //        $refusedDocuments = $companyService->getRefusedDocuments($this->resource->id);
        $project = UserProject::where('company_id', $this->resource->id)->first();

        return [
            'id_code'         => Hashids::encode($this->resource->id),
            'bank'            => $this->resource->bank ?? '',
            'agency'          => $this->resource->agency ?? '',
            'agency_digit'    => $this->resource->agency_digit ?? '',
            'account'         => $this->resource->account ?? '',
            'account_digit'   => $this->resource->account_digit ?? '',
            'document_status' => $presenter->getBankDocumentStatus(),
            'country'         => $this->country ?? '',
            //            'bank_document_status' => $this->resource->bank_document_status,
            //            'refusedDocuments'     => $refusedDocuments,
            'type'            => $this->company_type,
            'account_type'    => $this->account_type ?? '',
            'active_flag'     => $this->active_flag,
            'has_project'     => !empty($project),
        ];
    }
}


