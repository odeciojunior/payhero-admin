<?php

namespace Modules\ProjectReviews\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectReviewsStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            "project_id" => "required",
            "apply_on_plans" => "required|array",
            "photo" => "nullable",
            "name" => "required",
            "description" => "required",
            "stars" => "required",
            "active_flag" => "required|int",
            "photo_w" => "nullable",
            "photo_h" => "nullable",
            "photo_x1" => "nullable",
            "photo_y1" => "nullable",
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            "description.required" => "O campo Descrição é obrigatório",
            "apply_on_plans.required" => "Necessário informar em quais planos o review irá ser exibido",
        ];
    }
}
