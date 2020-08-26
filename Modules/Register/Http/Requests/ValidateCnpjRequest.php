<?php

namespace Modules\Register\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateCnpjRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_document'  => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * @return array
     */
    public function messages()
    {
        return [
            // Coloque aqui as mensagens de erro da validação
            'company_document.required'     => 'Precisamos do seu CPF para continuar'
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
