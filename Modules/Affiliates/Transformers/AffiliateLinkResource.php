<?php

namespace Modules\Affiliates\Transformers;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\UserService;

/**
 * @property mixed id
 * @property mixed name
 */
class AffiliateLinkResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $companyService           = new CompanyService();
        $userService              = new UserService();
        $companyId                = $this->affiliate->company->id;
        $companyDocumentValidated = $companyService->isDocumentValidated($companyId);
        $userDocumentValidated    = $userService->isDocumentValidated();

        return [
            'id'                => Hashids::encode($this->id),
            'plan_name'         => $this->plan->name ?? null,
            'description'       => $this->plan->description ?? null,
            'link'              => isset($this->affiliate->project->domains[0]->name) ? 'https://affiliate.' . $this->affiliate->project->domains[0]->name . '/' . $this->parameter : 'Domínio não configurado',
            'price'             => 'R$ ' . number_format(intval(preg_replace("/[^0-9]/", "", $this->plan->price ?? 0)) / 100, 2, ',', '.'),
            'commission'        => 'R$ ' . number_format(intval(preg_replace("/[^0-9]/", "", (($this->plan->price ?? 0 * $this->affiliate->percentage) / 100))) / 100, 2, ',', '.'),
            'document_status'   => ($companyDocumentValidated && $userDocumentValidated) ? 'approved' : 'pending',
        ];
    }
}
