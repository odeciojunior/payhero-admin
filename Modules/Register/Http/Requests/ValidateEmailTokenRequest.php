<?php

namespace Modules\Register\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateEmailTokenRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'     => 'required|max:200',
            'code' => 'required|min:4|max:4',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * @return array
     */
    public function messages()
    {
        return [
            'email.required'    => 'Precisamos do seu email para continuar',
            'code.required'      => 'Precisamos do token',
            'code.min'      => 'O token deve conter no mínimo 4 caracteres',
            'code.max'      => 'O token deve conter no máximo 4 caracteres',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
