<?php

declare(strict_types=1);

namespace Modules\Projects\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "photo_x1" => "nullable",
            "photo_y1" => "nullable",
            "photo_w" => "nullable",
            "photo_h" => "nullable",
            "name" => "nullable|string|max:100",
            "description" => "nullable|string|max:255",
            "visibility" => "nullable",
            "url_page" => "nullable|string|max:255",
            "boleto_redirect" => "nullable",
            "card_redirect" => "nullable",
            "pix_redirect" => "nullable",
            "analyzing_redirect" => "nullable",
            "ratioImage" => "nullable",
            "photo" => "nullable",
            "discount_recovery_status" => "nullable",
            "discount_recovery_value" => "nullable",
            "terms_affiliates" => "nullable",
            "percentage_affiliates" => "nullable",
            "cookie_duration" => "nullable",
            "automatic_affiliation" => "nullable",
            "status_url_affiliates" => "nullable|int|max:1",
            "commission_type_enum" => "nullable|int|max:2",
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
