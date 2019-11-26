<?php

namespace Modules\Register\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'email'            => 'required|unique:users|max:200',
            'name'             => 'required',
            'cellphone'        => 'required',
            'document'         => 'required',
            'date_birth'       => 'required',
            'password'         => 'required',
            'zip_code'         => 'required',
            'street'           => 'required',
            'number'           => 'required',
            'neighborhood'     => 'required',
            'complement'       => 'nullable',
            'city'             => 'required',
            'state'            => 'required',
            'company_document' => 'nullable',
            'fantasy_name'     => 'nullable',
            'company_type'     => 'nullable',
            'parameter'        => 'nullable',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
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
            'name.required'         => 'Precisamos do seu nome para continuar',
            'cellphone.required'    => 'Precisamos do seu celular para continuar',
            'document.required'     => 'Precisamos do seu CPF para continuar',
            'date_birth.required'   => 'Precisamos do sua data de nascimento para continuar',
            'zip_code.required'     => 'Precisamos do seu CEP para continuar',
            'street.required'       => 'Precisamos do nome da sua rua para continuar',
            'neighborhood.required' => 'Precisamos do nome do seu bairro para continuar',
            'city.required'         => 'Precisamos do nome da sua cidade para continuar',
            'state.required'        => 'Precisamos do nome do seu estado para continuar',
            'password.required'     => 'Password inválido',
        ];
    }
}
