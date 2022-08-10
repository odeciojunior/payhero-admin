<?php

namespace Modules\ProjectReviewsConfig\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectReviewsConfigUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            "reviews_config_icon_type" => "required|max:20",
            "reviews_config_icon_color" => "required|max:7",
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            "reviews_config_icon_type.required" => "O campo Tipo do Ícone é obrigatório",
            "reviews_config_icon_type.max" => "O campo Tipo do Ícone só permite 20 caracteres",
            "reviews_config_icon_color.required" => "O campo Cor do Ícone é obrigatório",
        ];
    }
}
