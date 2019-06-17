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
            'name'            => 'required',
            'code'            => 'required',
            'platform'        => 'required',
            'status'          => 'nullable',
            'checkout'        => 'nullable',
            'purchase_boleto' => 'nullable',
            'purchase_card'   => 'nullable',
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
