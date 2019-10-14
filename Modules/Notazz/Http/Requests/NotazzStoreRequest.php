<?php

namespace Modules\Notazz\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotazzStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'select_projects_create'     => 'required|string|max:100',
            'select_invoice_type_create' => 'required|numeric|digits_between:1,10',
            'token_api_create'           => 'required|string|max:255',
            'token_webhook_create'       => 'required|string|max:255',
            'token_logistics_create'     => 'nullable|string|max:255',
            'start_date_create'          => 'nullable|date',
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
