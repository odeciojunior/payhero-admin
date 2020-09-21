<?php

namespace Modules\Register\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'name'              => 'required',
            'document'          => 'required',
            'email'             => 'required|unique:users|max:200',
            'cellphone'         => 'required',
            'password'          => 'required',
            'zip_code'          => 'required',
            'street'            => 'required',
            'number'            => 'required',
            'complement'        => 'nullable',
            'neighborhood'      => 'required',
            'city'              => 'required',
            'state'             => 'required',
            'country'           => 'required',
            'date_birth'        => 'nullable',
            'fantasy_name'      => 'nullable',
            'support_email'     => 'nullable',
            'support_telephone' => 'nullable',
            'company_type'      => 'nullable',
            'parameter'         => 'nullable',

            'bank'          => 'required',
            'agency'        => 'required',
            'agency_digit'  => 'nullable',
            'account'       => 'required',
            'account_digit' => 'nullable',

            'company_document'     => 'nullable',
            'zip_code_company'     => 'nullable',
            'complement_company'   => 'nullable',
            'city_company'         => 'nullable',
            'state_company'        => 'nullable',
            'neighborhood_company' => 'nullable',
            'street_company'       => 'nullable',
            'number_company'       => 'nullable',


            'privacy_terms'       => 'required',
            'use_terms'           => 'required',
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

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
