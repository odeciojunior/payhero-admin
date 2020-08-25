<?php

namespace Modules\Register\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateEmailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'             => 'required|max:200',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * @return array
     */
    public function messages()
    {
        return [
            'email.required'        => 'Precisamos do seu email para continuar',
            'email.unique'          => 'Email informado ja esta sendo utilizado',
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
