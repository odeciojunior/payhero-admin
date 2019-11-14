<?php

namespace Modules\Domains\Transformers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\UserService;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

/**
 * Class DomainResource
 * @property mixed name
 * @property mixed status
 * @property mixed id
 * @package Modules\Domains\Transformers
 */
class DomainResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $companyService           = new CompanyService();
        $userService              = new UserService();
        $companyId                = $this->project->usersProjects[0]->company->id;
        $companyDocumentValidated = $companyService->isDocumentValidated($companyId);
        $userDocumentValidated    = $userService->isDocumentValidated();

        return [
            'id'                => Hashids::encode($this->id),
            'domain'            => $this->name,
            'status'            => $this->status,
            'status_translated' => Lang::get('definitions.enum.status.' . $this->present()->getStatus($this->status)),
            'document_status'   => ($companyDocumentValidated && $userDocumentValidated) ? 'approved' : 'pending',
        ];
    }
}
