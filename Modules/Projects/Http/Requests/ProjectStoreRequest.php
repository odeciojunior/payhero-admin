<?php

namespace Modules\Projects\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'photo'       => 'nullable',
            'name'        => 'required|string|max:255',
            'company'     => 'required',
            'description' => 'nullable|string|max:255',
            'photo_w'     => 'nullable',
            'photo_h'     => 'nullable',
            'photo_x1'    => 'nullable',
            'photo_y1'    => 'nullable',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'required' => 'O campo deve ser preenchido corretamente',
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
