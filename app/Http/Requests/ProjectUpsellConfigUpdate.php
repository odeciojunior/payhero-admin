<?php

declare(strict_types=1);

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
            "header" => "required|max:255",
            "countdown_time" => "nullable",
            "countdown_flag" => "nullable",
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            "header.required" => "O campo Cabeçalho é obrigatório",
            "header.max" => "O campo Cabeçalho permite apenas 255 caracteres",
        ];
    }
}
