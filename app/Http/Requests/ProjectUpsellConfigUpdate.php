<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUpsellConfigUpdate extends FormRequest
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
            'header'         => 'required|max:255',
            'title'          => 'required|max:255',
            'description'    => 'required',
            'countdown_time' => 'nullable',
            'countdown_flag' => 'nullable',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'header.required'      => 'O campo Cabeçalho é obrigatório',
            'header.max'           => 'O campo Cabeçalho permite apenas 255 caracteres',
            'description.required' => 'O campo Descrição é obrigatório',
            'title.required'       => 'O campo Título é obrigatório',
            'title.max'            => 'O campo Título permite apenas 255 caracteres',
        ];
    }
}
