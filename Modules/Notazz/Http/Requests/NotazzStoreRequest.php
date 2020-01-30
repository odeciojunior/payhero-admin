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
            'select_pending_days_create' => 'required|numeric|digits_between:1,70',
            'remove_tax'                 => 'nullable|boolean',
            'emit_zero'                  => 'nullable|boolean',
        ];
    }

    public function getValidatorInstance()
    {

        $this->merge([
                         'remove_tax' => $this->request->get('remove_tax', 'false') == 'true' ? 1 : 0,
                         'emit_zero'  => $this->request->get('emit_zero', 'false') == 'true' ? 1 : 0,
                     ]);

        return parent::getValidatorInstance();
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
