<?php

namespace Modules\Affiliates\Transformers;

use Illuminate\Http\Request;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\AffiliateRequest;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;

/**
 * @property mixed id
 * @property mixed name
 */
class ProjectAffiliateResource extends Resource
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
        if ($this->automatic_affiliation) {
            $affiliateModel     = new Affiliate();
            $affiliatePresenter = $affiliateModel->present();
            $affiliate          = $affiliateModel->where('user_id', $userId)
                                                 ->where('project_id', $this->id)
                                                 ->where('status_enum', $affiliatePresenter->getStatus('approved'))
                                                 ->first();
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
            'logo'                   => $this->logo,
            'url_page'               => $this->url_page,
            'contact'                => $this->contact ?? '',
            'contact_verified'       => $this->contact_verified,
            'support_phone'          => $this->support_phone ?? '',
            'support_phone_verified' => $this->support_phone_verified,
            'status'                 => isset($this->domains[0]->name) ? 1 : 0,
            "cookie_duration"        => $this->cookie_duration,
            "automatic_affiliation"  => $this->automatic_affiliation,
            "url_affiliates"         => route('index', Hashids::encode($this->id)),
            'user_name'              => $this->users[0]->name,
            'terms_affiliates'       => $this->terms_affiliates ?? '',
            'percentage_affiliates'  => $this->percentage_affiliates ?? '',
            'affiliatedMessage'      => $affiliatedMessage,
            'producer'               => $producer,
            'status_url_affiliates'  => $this->status_url_affiliates,
            'cookie_duration'        => $this->cookie_duration ?? '',
        ];
    }
}
