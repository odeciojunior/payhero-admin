<?php

namespace Modules\ProjectUpsellConfig\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\ProjectUpsellRule;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class ProjectUpsellConfigResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {
        $projectUpsellModel = new ProjectUpsellRule();
        $projectUpsell      = $projectUpsellModel->where('project_id', $this->project_id)->first();

        return [
            'id'             => Hashids::encode($this->id),
            'header'         => $this->header,
            'title'          => $this->title,
            'description'    => $this->description,
            'countdown_time' => $this->countdown_time ?? '',
            'countdown_flag' => $this->countdown_flag,
            'has_upsell'      => $projectUpsell ? true : false,
        ];
    }
}
