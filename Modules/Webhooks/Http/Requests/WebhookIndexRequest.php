<?php

namespace Modules\Webhooks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;

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
        $user = auth()->user();
        $this->merge([
            "user_id" => $user->company_default == Company::DEMO_ID ? User::DEMO_ID : $user->account_owner_id
        ]);
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
        $connection = auth()->user()->company_default == Company::DEMO_ID ? 'demo':'mysql';
        return [
            "user_id" => "required|exists:{$connection}.users,id",
            "company_id" => "required|exists:{$connection}.companies,id",
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
