<?php

namespace Modules\Register\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'    => 'required|unique:users|max:200',
            'name'     => 'required',
            'celphone' => 'required',
            'password' => 'required',
            'invite'   => 'required',
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

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required'    => 'Precisamos do seu email para continuar',
            'email.unique'      => 'Email informado ja esta sendo utilizado',
            'name.required'     => 'Precisamos do seu nome para continuar',
            'celphone.required' => 'Precisamos do seu celular para continuar',
            'password.required' => 'Password inválido',
            'invite.required'   => 'Password inválido',
        ];
    }
}
