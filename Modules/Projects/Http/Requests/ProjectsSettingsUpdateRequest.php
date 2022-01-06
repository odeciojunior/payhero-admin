<?php

namespace Modules\Projects\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectsSettingsUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "commission_type_enum" => 'nullable|int|max:2',
            "cookie_duration" => 'nullable',
            "description" => "nullable|string|max:255",
            "name" => "nullable|string|max:100",
            "percentage_affiliates" => 'nullable',
            "status_url_affiliates" => 'nullable|int|max:1',
            "terms_affiliates" => 'nullable',
            "url_page" => "nullable|string|max:255",
            "automatic_affiliation" => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'O campo Nome do projeto permite apenas 100 caracteres',
            'description.max' => 'O campo Descrição permite apenas 100 caracteres',
            'url_page.max' => 'O campo URL da pagina principal permite apenas 100 caracteres',           
            'custom_message_title.required_if'=>'Informe o titulo da mensagem personalizada',
            'custom_message_content.required_if'=>'Informe a mensagem personalizada'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}