<?php

namespace Modules\ProjectReviewsConfig\Transformers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\ProjectReviews;
use Vinkla\Hashids\Facades\Hashids;

class ProjectReviewsConfigResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {
        $projectReviewsModel = new ProjectReviews();
        $review = $projectReviewsModel->where('project_id', $this->project_id)->first();

        return [
            'id'         => Hashids::encode($this->id),
            'icon_type'  => $this->icon_type,
            'icon_color' => $this->icon_color,
            'has_review' => $review ? true : false,
        ];
    }
}
