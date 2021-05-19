<?php

namespace Modules\Sales\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleUpdateRequest extends FormRequest
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
            'sale'         => 'required|string',
            'delivery'     => 'required|string',
            'trackingCode' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Preencha o campo Código Rastreio corretamente',
        ];
    }
}
