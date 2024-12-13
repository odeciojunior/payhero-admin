<?php

declare(strict_types=1);

namespace Modules\Webhooks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebhookStoreRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function validationData(): array
    {
        $this->merge(["user_id" => auth()->user()->account_owner_id]);
        $this->merge(["company_id" => hashids_decode($this->company_id)]);

        return $this->all();
    }

    public function rules(): array
    {
        return [
            "user_id" => "required|exists:users,id",
            "company_id" => "required|exists:companies,id",
            "description" => "required|string",
            "url" => "required|url",
        ];
    }

    public function messages(): array
    {
        return [
            "user_id.required" => "Utilize um usuário",
            "user_id.exists" => "Utilize um usuário válido",
            "company_id.required" => "Selecione uma empresa",
            "company_id.exists" => "Selecione uma empresa válida",
            "description.required" => "Digite uma descrição para seu webhook",
            "description.string" => "Digite uma descrição para seu webhook",
            "url.required" => "Digite uma URL",
            "url.url" => "Digite uma URL válida",
        ];
    }
}
