<?php

namespace Modules\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'          => 'required|max:200',
            'description'   => 'required|max:200',
            'format'        => 'required',
            'category'      => 'required',
            'cost'          => '',
            'height'        => '',
            'width'         => '',
            'weight'        => '',
            'product_photo' => '',
            'photo_x1'      => 'required',
            'photo_y1'      => 'required',
            'photo_w'       => 'required',
            'photo_h'       => 'required',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'        => 'O nome do produto é obrigatório',
            'description.required' => 'A descrição do produto é obrigatória',
        ];
    }

}
