<?php

namespace Modules\Projects\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            "photo_x1"                   => "nullable",
            "photo_y1"                   => "nullable",
            "photo_w"                    => "nullable",
            "photo_h"                    => "nullable",
            "name"                       => "nullable|string|max:100",
            "description"                => "nullable|string|max:255",
            "visibility"                 => "nullable",
            "url_page"                   => "nullable|string|max:255",
            "contact"                    => "nullable",
            "invoice_description"        => "nullable",
            "boleto_redirect"            => "nullable",
            "card_redirect"              => "nullable",
            "analyzing_redirect"         => "nullable",
            "company"                    => "nullable",
            "installments_amount"        => "nullable",
            "installments_interest_free" => "nullable",
            "boleto"                     => "nullable",
            "logo_x1"                    => "nullable",
            "logo_y1"                    => "nullable",
            "logo_w"                     => "nullable",
            "logo_h"                     => "nullable",
            "ratioImage"                 => "nullable",
            "photo"                      => 'nullable',
            "logo"                       => 'nullable',

        ];
    }

    public function messages()
    {
        return [
            'name.max'        => 'O campo Nome do projeto permite apenas 100 caracteres',
            'description.max' => 'O campo Descrição permite apenas 100 caracteres',
            'url_page.max'    => 'O campo URL da pagina principal permite apenas 100 caracteres',
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
