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

        $linkPlan = $linkAffiliate = 'Domínio não configurado';

        if (!empty($this->affiliate->project->domains[0]->name)) {
            $linkPlan      = (!empty($this->plan->code)) ? 'https://checkout.' . $this->affiliate->project->domains[0]->name . '/' . $this->plan->code : null;
            $linkAffiliate = (!empty($this->parameter)) ? 'https://affiliate.' . $this->affiliate->project->domains[0]->name . '/' . $this->parameter : null;
        }

        return [
            'id'              => Hashids::encode($this->id),
            'plan_name'       => $this->plan->name ?? null,
            'description'     => $this->plan->description ?? null,
            'link'            => $this->link ?? null,
            'clicks'          => $this->clicks_amount ?? null,
            'link_project'    => $this->affiliate->project->url_page ?? null,
            'project_name'    => $this->affiliate->project->name ?? null,
            'link_plan'       => $linkPlan,
            'link_affiliate'  => $linkAffiliate,
            'domain'          => $this->affiliate->project->domains[0]->name ?? '',
            'price'           => $this->plan ? 'R$ ' . number_format(intval(preg_replace("/[^0-9]/", "", $this->plan->price ?? 0)) / 100, 2, ',', '.') : '',
            'commission'      => $this->plan ? 'R$ ' . number_format((preg_replace("/[^0-9]/", "", $this->plan->price) / 100) * $this->affiliate->percentage / 100, 2, ',', '.') : '',
            'document_status' => ($companyDocumentValidated && $userDocumentValidated) ? 'approved' : 'pending',
        ];
    }
}
