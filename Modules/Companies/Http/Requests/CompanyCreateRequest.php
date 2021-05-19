<?php

namespace Modules\Companies\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            //            'country'          => 'required|string|max:255,|in:"usa","brazil"',
            'country'          => 'required|string|max:255',
            'fantasy_name'     => 'required_if:company_type,==,2|string|max:255',
            'company_type'     => 'required|integer',
            'company_document' => 'required_if:company_type,==,2',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'company_document.required_if' => 'O campo CNPJ é obrigatório',
            'fantasy_name.required_if'     => 'O campo Razão Social é obrigatório',
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
