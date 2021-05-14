<?php

namespace Modules\SalesBlackListAntifraud\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class SalesBlackListAntiFraudResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {

        $descriptionBlackList = [];
        if (!empty($this->saleWhiteBlackListResult) && $this->saleWhiteBlackListResult->count() > 0) {
            $saleBlackList = $this->saleWhiteBlackListResult->first();
            if ($saleBlackList->blacklist) {
                $descriptionBlackListJson = json_decode($saleBlackList->whiteblacklist_json);
                $descriptionBlackList[]   = $descriptionBlackListJson->blackList;
            }
        }

        $plansSale = $this->getRelation('plansSales')->first();
        $plan      = $plansSale ? $plansSale->getRelation('plan') : null;

        return [
            'sale_code'  => '#' . Hashids::connection('sale_id')->encode($this->id),
            'project'    => $this->project->name ?? '',
            'product'    => (count($this->getRelation('plansSales')) > 1) ? 'Carrinho' : Str::limit($plan->name ?? '', 25),
            'customer'   => $this->customer->name ?? '',
            'black_list' => $descriptionBlackList,
            'start_date' => $this->start_date ? with(new Carbon($this->start_date))->format('d/m/Y H:i:s') : '',
            'sale_id'    => $this->id,
            'status'     => $this->status,
        ];
    }
}
