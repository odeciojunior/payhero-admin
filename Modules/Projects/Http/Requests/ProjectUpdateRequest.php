<?php

namespace Modules\Projects\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUpdateRequest extends FormRequest {

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
            "name"                       => "nullable",
            "description"                => "nullable",
            "visibility"                 => "nullable",
            "url_page"                   => "nullable",
            "contact"                    => "nullable",
            "invoice_description"        => "nullable",
            "url_redirect"               => "nullable",
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
            'required' => "Os campos devem ser preenchidos corretamente",
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
