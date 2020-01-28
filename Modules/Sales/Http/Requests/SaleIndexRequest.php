<?php


namespace Modules\Sales\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class SaleIndexRequest extends FormRequest
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
            'project'        => 'nullable|string',
            'transaction'    => 'nullable',
            'payment_method' => 'nullable|string',
            'status'         => 'nullable',
            'client'         => 'nullable|string',
            'date_type'      => 'required',
            'date_range'     => 'required',
        ];
    }

    public function messages()
    {
        return [
            'date_type.required' => 'O campo data e obrigatório',
            'date_range.required' => 'É preciso selecionar um período',
        ];
    }
}
