<?php

namespace Modules\Affiliates\Transformers;

use Illuminate\Http\Request;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\AffiliateRequest;
use Modules\Core\Entities\Company;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

/**
 * @property mixed id
 * @property mixed name
 */
class ProjectAffiliateResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function toArray($request)
    {
        $userId = auth()->user()->account_owner_id;

        //verifica se é o dono do projeto
        $userProject = $this->usersProjects[0];
        if ($userProject->user_id == $userId) {
            $producer = true;
        } else {
            $producer = false;
        }

        //verifica se é o projeto tem afiliação automatica
        $affiliateModel     = new Affiliate();
        $affiliatePresenter = $affiliateModel->present();
        $affiliate          = $affiliateModel->where('user_id', $userId)
                                             ->where('project_id', $this->id)
                                             ->first();
        if(!empty($affiliate->id)) {
            $affiliatedMessage  = !empty($affiliate) ? 'Você ja está afiliado a esse projeto.' : '';
        } else {
            $affiliateRequestModel = new AffiliateRequest();
            $affiliateRequest      = $affiliateRequestModel->where('user_id', $userId)
                                                           ->where('project_id', $this->id)
                                                           ->first();
            $affiliatedMessage     = !empty($affiliateRequest) ? 'Você já solicitou afiliação a esse projeto.' : '';
        }

        return [
            'id'                     => Hashids::encode($this->id),
            'photo'                  => $this->photo,
            'name'                   => $this->name,
            'description'            => $this->description,
            'created_at'             => (new Carbon($this->created_at))->format('d/m/Y'),
            'shopify_id'             => $this->shopify_id,
            'url_page'               => $this->url_page,
            'status'                 => isset($this->domains[0]->name) ? 1 : 0,
            "automatic_affiliation"  => $this->automatic_affiliation,
            "url_affiliates"         => route('affiliates.index', Hashids::encode($this->id)),
            'user_name'              => $this->users[0]->name,
            'terms_affiliates'       => $this->terms_affiliates ?? '',
            'percentage_affiliates'  => $this->percentage_affiliates ?? '',
            'affiliatedMessage'      => $affiliatedMessage,
            'producer'               => $producer,
            'status_url_affiliates'  => $this->status_url_affiliates,
            'cookie_duration'        => $this->cookie_duration ?? '',
            'billet_release_days'    => $userProject->company->gateway_release_money_days ?? '',
        ];
    }
}
