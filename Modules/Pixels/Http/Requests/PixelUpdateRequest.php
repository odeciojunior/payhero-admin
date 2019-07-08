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
            'name'            => 'required',
            'code'            => 'required',
            'platform'        => 'required',
            'project'         => 'nullable',
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
            'name.required'     => 'O campo Nome é obrigatório',
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
