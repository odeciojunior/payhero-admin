<?php

namespace Modules\Notazz\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotazzUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'token_api_edit'           => 'required|string|max:255',
            'token_webhook_edit'       => 'required|string|max:255',
            'token_logistics_edit'     => 'nullable|string|max:255',
            'select_pending_days_edit' => 'required|numeric|digits_between:1,70',
            'remove_tax_edit'          => 'nullable|boolean',
            'emit_zero_edit'           => 'nullable|boolean',
        ];
    }

    public function getValidatorInstance()
    {

        $this->merge([
                         'remove_tax_edit' => $this->request->get('remove_tax_edit', false) == '1' ? 1 : 0,
                         'emit_zero_edit'  => $this->request->get('emit_zero_edit', false) == '1' ? 1 : 0,
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
