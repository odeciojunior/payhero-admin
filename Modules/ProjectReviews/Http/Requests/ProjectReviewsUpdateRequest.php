<?php

namespace Modules\ProjectReviews\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectReviewsUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            "apply_on_plans" => "required|array",
            "photo" => "nullable",
            "name" => "required",
            "description" => "required",
            "stars" => "required",
            "active_flag" => "required",
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
            "discount.required" => "O campo Desconto é obrigatório",
            "apply_on_plans.required" => "O campo Ao comprar o plano é obrigatório",
        ];
    }
}
