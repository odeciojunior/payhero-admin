<?php

namespace Modules\Pixels\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PixelStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'project'         => 'required',
            'campaign'        => 'nullable',
            'name'            => 'required|max:100',
            'code'            => 'required',
            'platform'        => 'required',
            'status'          => 'nullable',
            'checkout'        => 'nullable',
            'purchase_boleto' => 'nullable',
            'purchase_card'   => 'nullable',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'     => 'O campo Descrição é obrigatório',
            'name.max'          => 'O campo Descrição permite apenas 100 caracteres',
            'code.required'     => 'O campo Código é obrigatório',
            'platform.required' => 'O campo Plataforma é obrigatório',
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
