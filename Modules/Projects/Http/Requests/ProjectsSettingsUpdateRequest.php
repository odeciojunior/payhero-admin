<?php

declare(strict_types=1);

namespace Modules\Projects\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectsSettingsUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "project_photo" => "sometimes|nullable",
            "name" => "nullable|string|max:100",
            "description" => "nullable|string|max:255",
            "url_page" => "nullable|string|max:255",
            "cookie_duration" => "nullable",
            "percentage_affiliates" => "nullable",
            "commission_type_enum" => "nullable|int|max:2",
            "terms_affiliates" => "nullable",
            "status_url_affiliates" => "nullable|int|max:1",
            "automatic_affiliation" => "nullable",
        ];
    }

    public function messages(): array
    {
        return [
            "name.max" => "O campo Nome do projeto permite apenas 100 caracteres",
            "description.max" => "O campo Descrição permite apenas 100 caracteres",
            "url_page.max" => "O campo URL da pagina principal permite apenas 100 caracteres",
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
