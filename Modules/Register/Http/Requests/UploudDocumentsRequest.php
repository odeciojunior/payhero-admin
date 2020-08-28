<?php

namespace Modules\Register\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploudDocumentsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => 'required|mimes:jpeg,jpg,png,doc,pdf',
            'document' => 'required',
            'document_type' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'file.mimes' => 'Arquivo com formato inválido',
            'file.required' => 'Precisamos de um documento para continuar',
            'document.required' => 'Precisamos do seu CPF para continuar',
            'document_type.required' => 'Árquivo precisa ter de uma extenção para continuar',
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
