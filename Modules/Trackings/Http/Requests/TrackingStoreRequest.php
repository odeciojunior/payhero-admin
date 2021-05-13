<?php

namespace Modules\Trackings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackingStoreRequest extends FormRequest
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
            'tracking_code' => 'required|min:10|max:18|regex:/^[\w-]*$/',
            'product_plan_sale_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Erro ao salvar código de rastreio',
            'tracking_code.max' => 'Código de rastreio inválido',
            'tracking_code.min' => 'Código de rastreio inválido',
            'tracking_code.regex' => 'Código de rastreio inválido'
        ];
    }
}
