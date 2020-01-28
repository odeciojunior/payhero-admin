<?php


namespace Modules\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|max:100|string',
            'shopify' => 'nullable|in:1,0',
            'project' => 'nullable|string'
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
            'name.max' => 'O campo Nome permite apenas 100 caracteres',
        ];
    }
}
