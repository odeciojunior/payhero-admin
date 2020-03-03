<?php

namespace Modules\ProjectUpsellRule\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class ProjectsUpsellResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {
        return [
            'id'          => Hashids::encode($this->id),
            'description' => Str::limit($this->description, 15),
            'active_flag' => $this->active_flag,
        ];
    }
}
