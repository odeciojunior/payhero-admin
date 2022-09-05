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
            'cellphone' => 'required',
            'email' => 'required|email|max:200|unique:users,email,NULL,id,deleted_at,NULL'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O campo nome é permitido no máximo 200 caracteres.',
            'document.required' => 'O documento é obrigatório.',
            'document.unique' => 'O documento informado já existe.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.unique' => 'O e-mail informado já existe.',
            'cellphone.required' => 'O campo celular é obrigatório.'
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
