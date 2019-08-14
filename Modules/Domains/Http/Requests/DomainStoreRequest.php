<?php

namespace Modules\Domains\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DomainStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'project_id' => 'required|string|max:255',
            'name'       => 'required|string|max:100',
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
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'O campo Dominio deve ser preenchido corretamente',
            'name.max'      => 'O campo Dominio permite apenas 100 caracteres',
        ];
    }
}
