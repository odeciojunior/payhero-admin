<?php

namespace Modules\ProjectUpsellConfig\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\ProductPlan;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class PreviewUpsellResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {

        return [
            'id'             => Hashids::encode($this->id),
            'header'         => $this->header,
            'title'          => $this->title,
            'description'    => $this->description,
            'countdown_time' => $this->countdown_time ?? '',
            'countdown_flag' => $this->countdown_flag,
            'plans'          => $this->plans,
        ];
    }
}
