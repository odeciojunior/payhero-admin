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
            'icon_type'  => 'required|max:20',
            'icon_color' => 'required|max:7',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'icon_type.required'  => 'O campo Tipo do Ícone é obrigatório',
            'icon_type.max'       => 'O campo Tipo do Ícone só permite 20 caracteres',
            'icon_color.required' => 'O campo Cor do Ícone é obrigatório'
        ];
    }
}
