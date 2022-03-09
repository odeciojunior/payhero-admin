<?php

namespace Modules\Trackings\Transformers;

use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackingShowResource extends JsonResource
{
    public function toArray($request)
    {
        $trackingCode = $this->tracking_code == "CLOUDFOX000XX"
            ? ''
            : $this->tracking_code;

        if(!empty($this->product)) {
            $product = $this->product;
            $domain =  $this->productPlanSale->plan->project->domains->first();
            $linkBase = $domain ? $domain->name : 'cloudfox.net';
        } else {
            $productSaleApi = $this->productPlanSale->productSaleApi;
            $product = (object) [
                'name' => $productSaleApi->name,
                'description' => '',
                'photo' => mix('modules/global/img/produto.svg')
            ];
            $linkBase = 'cloudfox.net';
        }

        return [
            'id' => Hashids::encode($this->id),
            'tracking_code' => $trackingCode,
            'tracking_status_enum' => $this->tracking_status_enum,
            'tracking_status' => $this->tracking_status_enum ? __('definitions.enum.tracking.tracking_status_enum.' . $this->present()->getTrackingStatusEnum($this->tracking_status_enum)) : 'Não informado',
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'amount' => $this->amount,
            'product' => [
                'name' => $product->name,
                'description' => $product->description,
                'photo' => $product->photo,
            ],
            'delivery' => [
                'street' => $this->delivery->street ?? '',
                'number' => $this->delivery->number ?? '',
                'neighborhood' => $this->delivery->neighborhood ?? '',
                'zip_code' => $this->delivery->zip_code ?? '',
                'city' => $this->delivery->city ?? '',
                'state' => $this->delivery->state ?? '',
            ],
            'checkpoints' => $this->checkpoints ?? [],
            'link' => $linkBase ? 'https://tracking.' . $linkBase . '/' . $this->tracking_code : '',
        ];
    }
}
