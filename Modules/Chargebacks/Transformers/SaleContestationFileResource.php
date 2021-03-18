<?php

namespace Modules\Chargebacks\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Modules\Core\Entities\SaleWhiteBlackListResult;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\FoxUtilsService;
use Modules\Core\Services\SaleService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ContestationResource
 * @package Modules\Companies\Transformers
 */
class SaleContestationFileResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array,
     * @throws Exception
     */
    public function toArray($request)
    {

        return [
            'id' => Hashids::encode($this->id),
            'user_id' => 1,
             'contestation_sale_id' => 1,
            'type' => 1,
            'file' => 1,
            'created_at' => with(new Carbon($this->created_at))->format('d/m/Y') ,
        ];
    }


}
