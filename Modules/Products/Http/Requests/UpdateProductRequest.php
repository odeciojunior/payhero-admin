<?php

namespace Modules\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'name'          => 'required|max:100|string',
            'description'   => 'required|max:100|string',
            'format'        => 'nullable',
            'category'      => 'nullable',
            'cost'          => 'nullable',
            'price'         => 'nullable',
            'height'        => 'nullable',
            'width'         => 'nullable',
            'weight'        => 'nullable',
            'product_photo' => 'nullable',
            'photo_x1'      => 'nullable',
            'photo_y1'      => 'nullable',
            'photo_w'       => 'nullable',
            'photo_h'       => 'nullable',
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

    /**
     * Get the error messages for the defined validation rules.
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'        => 'O nome do produto é obrigatório',
            'description.required' => 'A descrição do produto é obrigatória',
            'description.max'      => 'O campo Descrição permite apenas 100 caracteres',
            'name.max'             => 'O campo Nome permite apenas 100 caracteres',
//            'name.regex'           => 'O nome contém caracteres inválidos',
//            'description.regex'     => 'A descrição contém caracteres inválidos',

        ];
    }
}
