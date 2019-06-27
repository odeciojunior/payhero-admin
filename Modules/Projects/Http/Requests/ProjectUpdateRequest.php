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
            'photo'                      => 'nullable',
            'visibility'                 => 'required',
            'status'                     => 'required',
            'name'                       => 'required',
            'description'                => 'nullable',
            'invoice_description'        => 'nullable|string|max:15',
            'url_page'                   => 'nullable',
            'shipment'                   => 'required',
            'shipment_responsible'       => 'required',
            'installments_amount'        => 'required',
            'installments_interest_free' => 'required',
            'carrier'                    => 'required',
            'logo'                       => 'nullable',
            'url_redirect'               => 'nullable',
            'ticket'                     => 'required',
            "photo_x1"                   => 'nullable',
            "photo_y1"                   => 'nullable',
            "photo_w"                    => 'nullable',
            "photo_h"                    => 'nullable',
            "contact"                    => 'nullable',
            "logo_x1"                    => 'nullable',
            "logo_y1"                    => 'nullable',
            "logo_w"                     => 'nullable',
            "logo_h"                     => 'nullable',
            "company"                    => 'nullable',

        ];
    }

    public function messages()
    {
        return [
            'required' => "O campo devem ser preenchidos corretamente",
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
