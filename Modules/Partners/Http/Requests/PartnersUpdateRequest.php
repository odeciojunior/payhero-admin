<?php

namespace Modules\Partners\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartnersUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'email_invited'      => 'required|email',
            'type'               => 'required',
            'value_remuneration' => 'required',
            'project'            => 'required',
        ];
    }

    public function messages()
    {
        return [
            'email_invited.required'      => 'O campo Email é obrigatório',
            'type.required'               => 'O campo Tipo de Parceiro é obrigatório',
            'value_remuneration.required' => 'O campo Valor(porcentagem) é obrigatório',
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
}
