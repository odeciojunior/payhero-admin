<?php

namespace Modules\Api\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class SubsellersApiRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:200',
            'document' => 'required|min:11|cpf|unique:users,document,NULL,id,deleted_at,NULL',
            'email' => 'required|email|max:200|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O campo nome é permitido no máximo 200 caracteres.',
            'document.required' => 'O documento nome é obrigatório.',
            'document.unique' => 'O documento informado já existe.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.unique' => 'O e-mail informado já existe.',
            'password.required' => 'O campo senha é obrigatório.'
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
