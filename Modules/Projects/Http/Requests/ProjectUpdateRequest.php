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
            "name"                       => "required",
            "description"                => "nullable",
            "visibility"                 => "required",
            "url_page"                   => "nullable",
            "contact"                    => "required",
            "invoice_description"        => "nullable",
            "url_redirect"               => "nullable",
            "company"                    => "required",
            "installments_amount"        => "required",
            "installments_interest_free" => "required",
            "boleto"                     => "required",
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
