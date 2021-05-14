<?php

namespace Modules\Domains\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DomainRecordsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'type-register' => 'required|string',
            'name-register' => 'required|string',
            'value-record' => 'required|string',
            'priority' => 'nullable|string',
            'proxy' => 'required|string',
            'project' => 'required|string',
            'domain' => 'required|string',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'type-register.required' => 'O campo tipo de entrada deve ser selecionado',
            'type-register.string' => 'O campo tipo deve ser selecionado corretamente',
            'name-register.required' => 'O campo nome deve ser preenchido corretamente',
            'name-register.string' => 'O campo nome deve ser preenchido corretamente',
            'value-record.required' => 'O campo tipo deve ser preenchido corretamente',
            'value-record.string' => 'O campo valor deve ser preenchido corretamente',
            'project.required' => 'Projeto nao encontrado',
            'domain.required' => 'Domínio nao econtrado',
        ];
    }
}
