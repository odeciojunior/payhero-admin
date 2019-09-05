<?php

namespace Modules\Pixels\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PixelUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'name'            => 'required|max:100|string',
            'code'            => 'required|string',
            'platform'        => 'required',
            'project_id'      => 'nullable',
            'campaign'        => 'nullable',
            'status'          => 'nullable',
            'checkout'        => 'nullable',
            'purchase_boleto' => 'nullable',
            'purchase_card'   => 'nullable',
        ];
    }

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
