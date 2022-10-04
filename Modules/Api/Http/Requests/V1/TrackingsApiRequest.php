<?php

namespace Modules\Api\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Api\Validators\V1\TrackingsRule;

class TrackingsApiRequest extends FormRequest
{
    public function storeTrackings()
    {
        return TrackingsRule::storeTrackings();
    }

    public function messages()
    {
        return TrackingsRule::messages();
    }

    public function authorize()
    {
        return true;
    }
}
