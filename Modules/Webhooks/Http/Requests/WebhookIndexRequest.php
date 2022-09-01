<?php

namespace Modules\Webhooks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebhookIndexRequest extends FormRequest
{
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
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        $this->merge(["user_id" => auth()->user()->account_owner_id]);
        $this->merge(["company_id" => hashids_decode($this->company_id)]);

        return $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "user_id" => "required|exists:users,id",
            "company_id" => "required|exists:companies,id",
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            "user_id.required" => "Utilize um usuário",
            "user_id.exists" => "Utilize um usuário válido",
            "company_id.required" => "Selecione uma empresa",
            "company_id.exists" => "Selecione uma empresa válida",
        ];
    }
}
