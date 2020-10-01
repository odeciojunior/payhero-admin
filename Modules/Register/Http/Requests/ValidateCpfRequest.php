<?php

namespace Modules\Register\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateCpfRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'document' => 'required|min:11|max:11|unique:users,document,NULL,id,deleted_at,NULL',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * @return array
     */
    public function messages()
    {
        return [
            'document.required' => 'Precisamos do seu CPF para continuar',
            'document.unique'     => 'CPF já cadastrado.',
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
