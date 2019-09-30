<?php

namespace Modules\Checkouts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutIndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'project'     => 'required|string',
            'type'        => 'required|string',
            'start_date'  => 'nullable',
            'end_date'    => 'nullable',
            'client_name' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'project.required' => 'O campo projeto é obrigatório',
            'project.string'   => 'O valor do projeto esta incorreto',
            'type.required'    => 'O campo tipo de venda é obrigatório',
            'type.string'    => 'O valor do campo tipo de venda esta incorreto',
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
