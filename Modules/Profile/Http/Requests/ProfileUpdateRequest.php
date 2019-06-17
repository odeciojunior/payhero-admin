<?php

namespace Modules\Profile\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [

            'name'          => 'sometimes|string|max:255',
            'email'         => 'sometimes|email|max:255',
            'document'      => 'sometimes|string|max:255',
            'cellphone'     => 'sometimes|string|max:255',
            'date_birth'    => 'sometimes|date_format:d/m/Y',
            'photo_x1'      => 'sometimes|numeric',
            'photo_y1'      => 'sometimes|numeric',
            'photo_w'       => 'sometimes|numeric',
            'photo_h'       => 'sometimes|numeric',
            'zip_code'      => 'sometimes|string|max:255',
            'country'       => 'sometimes|string|max:255',
            'state'         => 'sometimes|string|max:255',
            'city'          => 'sometimes|string|max:255',
            'neighborhood'  => 'sometimes|string|max:255',
            'street'        => 'sometimes|string|max:255',
            'number'        => 'sometimes|string|max:255',
            'complement'    => 'sometimes|string|max:255',

            'profile_photo' => 'nullable|image|mimes:jpeg,jpg,png',

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
