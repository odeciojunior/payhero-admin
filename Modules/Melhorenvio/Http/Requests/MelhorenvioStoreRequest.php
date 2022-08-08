<?php

namespace Modules\Melhorenvio\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MelhorenvioStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "name" => "required",
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            "name.required" => "O campo nome é obrigatório",
        ];
    }
}
