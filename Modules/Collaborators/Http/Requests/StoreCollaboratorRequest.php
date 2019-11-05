<?php

namespace Modules\Collaborators\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCollaboratorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'email'     => 'required|unique:users|max:200',
            'name'      => 'required',
            'cellphone' => 'required',
            'document'  => 'required',
            'password'  => 'required',
            'role'      => 'required',
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
            'email.required'     => 'O campo email é obrigatório',
            'email.unique'       => 'Email informado ja esta sendo utilizado',
            'name.required'      => 'O campo Nome é obrigatório',
            'cellphone.required' => 'O campo Telefone é obrigatório',
            'document.required'  => 'O campo Documento é obrigatório',
            'password.required'  => 'O campo Senha é obrigatório',
        ];
    }
}
