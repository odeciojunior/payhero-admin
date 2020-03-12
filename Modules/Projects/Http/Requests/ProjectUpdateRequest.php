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
            "company_id"                 => "nullable",
            "installments_amount"        => "nullable",
            "installments_interest_free" => "nullable",
            "boleto"                     => "nullable",
            "credit_card"                => "nullable",
            "boleto_due_days"            => "nullable",
            "logo_x1"                    => "nullable",
            "logo_y1"                    => "nullable",
            "logo_w"                     => "nullable",
            "logo_h"                     => "nullable",
            "ratioImage"                 => "nullable",
            "photo"                      => 'nullable',
            "logo"                       => 'nullable',
            "support_phone"              => 'nullable',
            "discount_recovery_status"   => 'nullable',
            "discount_recovery_value"    => 'nullable',
            "cost_currency_type"         => 'required|string|max:5',
            "checkout_type"              => 'required',
            "terms_affiliates"           => 'nullable',
            "percentage_affiliates"      => 'nullable',
            "cookie_duration"            => 'nullable',
            "automatic_affiliation"      => 'nullable',
            "status_url_affiliates"      => 'nullable|int|max:1',
            "commission_type_enum"       => 'nullable|int|max:2',
            "whatsapp_button"            => 'nullable',
            "credit_card_discount"       => 'int|max:100',
            "billet_discount"            => 'int|max:100',

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
