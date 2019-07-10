<?php

namespace Modules\Shopify\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopifyAddIntegrationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [

            'token'    => 'required|string|max:255',
            'photo_x1' => 'nullable|numeric',
            'photo_y1' => 'nullable|numeric',
            'photo_w'  => 'nullable|numeric',
            'photo_h'  => 'nullable|numeric',

            'photo' => 'nullable|image|mimes:jpeg,jpg,png',

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
