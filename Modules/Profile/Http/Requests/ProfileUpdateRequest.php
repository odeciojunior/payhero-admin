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
            'document'      => 'nullable|sometimes|string|max:255',
            'cellphone'     => 'nullable|sometimes|string|max:255',
            'date_birth'    => 'nullable|sometimes|date_format:Y-m-d',
            'photo_x1'      => 'nullable|numeric',
            'photo_y1'      => 'nullable|numeric',
            'photo_w'       => 'nullable|numeric',
            'photo_h'       => 'nullable|numeric',
            'zip_code'      => 'nullable|sometimes|string|max:255',
            'country'       => 'nullable|sometimes|string|max:255',
            'state'         => 'nullable|sometimes|string|max:255',
            'city'          => 'nullable|sometimes|string|max:255',
            'country'       => 'nullable|string|max:255',
            'neighborhood'  => 'nullable|sometimes|string|max:255',
            'street'        => 'nullable|sometimes|string|max:255',
            'number'        => 'nullable|sometimes|string|max:255',
            'complement'    => 'nullable|sometimes|string|max:255',
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
