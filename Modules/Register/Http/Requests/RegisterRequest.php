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
            'name'              => 'required|max:200',
            'document'          => 'required|min:11|max:11|unique:users,document,NULL,id,deleted_at,NULL',
            'email'             => 'required|unique:users|max:200',
            'cellphone'         => 'required',
            'password'          => 'required',
            'zip_code'          => 'required',
            'street'            => 'required',
            'number'            => 'nullable',
            'complement'        => 'nullable|max:200',
            'neighborhood'      => 'required|max:200',
            'city'              => 'required',
            'state'             => 'required',
            'country'           => 'required',
            'date_birth'        => 'nullable',
            'fantasy_name'      => 'nullable|max:200',
            'support_email'     => 'nullable|max:200',
            'support_telephone' => 'nullable',
            'parameter'         => 'nullable',

            'company_type'      => 'required|in:physical person,juridical person',

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
            'name.required'             => 'Precisamos do seu nome para continuar.',
            'document.required'         => 'Precisamos do seu CPF para continuar.',
            'document.unique'           => 'CPF informado ja esta sendo utilizado.',
            'email.required'            => 'Precisamos do seu email para continuar.',
            'email.unique'              => 'Email informado ja esta sendo utilizado.',
            'cellphone.required'        => 'Precisamos do seu celular para continuar.',
            'password.required'         => 'Password inválido.',
            'zip_code.required'         => 'Precisamos do seu CEP para continuar.',
            'street.required'           => 'Precisamos do nome da sua rua para continuar.',
            'number.required'           => 'Precisamos do número do estabelecimento para continuar.',
            'neighborhood.required'     => 'Precisamos do nome do seu bairro para continuar.',
            'city.required'             => 'Precisamos do nome da sua cidade para continuar.',
            'state.required'            => 'Precisamos do nome do seu estado para continuar.',
            'country.required'          => 'Precisamos saber seu país para continuar.',

            'bank.required'             => 'Precisamos do seu banco para continuar.',
            'agency.required'           => 'Precisamos de sua agência para continuar.',
            'account.required'          => 'Precisamos de sua conta para continuar.',

            'company_type.required'     => 'Precisamos saber o tipo de cadastro para continuar. (Pessoa Física/Pessoa Júridica).',
            'company_type.in'           => 'Tipo de cadastro inválido.',

            'privacy_terms.required'    => 'É preciso aceitar os "Termos de Privacidade" para finalizar o cadastro.',
            'use_terms.required'        => 'É preciso aceitar os "Termos de Uso" para finalizar o cadastro.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
