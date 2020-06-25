<?php

namespace Modules\Companies\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'business_website'                      => 'nullable|string|max:255',
            'support_email'                         => 'nullable|string|max:255',
            'support_telephone'                     => 'nullable|string|max:255',
            'fantasy_name'                          => 'nullable|string|max:255',
            'cnpj'                                  => 'nullable|string|max:255',
            'zip_code'                              => 'nullable|string|max:255',
            'state'                                 => 'nullable|string|max:255',
            'city'                                  => 'nullable|string|max:255',
            'neighborhood'                          => 'nullable|string|max:255',
            'street'                                => 'nullable|string|max:255',
            'number'                                => 'nullable|string|max:255',
            'complement'                            => 'nullable|string|max:255',
            'country'                               => 'nullable|string|max:255',
            'bank'                                  => 'nullable|string|max:255',
            'agency'                                => 'nullable|string|max:255',
            'agency_digit'                          => 'nullable|string|max:255',
            'account'                               => 'nullable|string|max:255',
            'account_digit'                         => 'nullable|string|max:255',
            'company_document'                      => 'nullable|string|max:255',
            'patrimony'                             => 'nullable',
            'state_fiscal_document_number'          => 'nullable',
            'business_entity_type'                  => 'nullable',
            'economic_activity_classification_code' => 'nullable',
            'monthly_gross_income'                  => 'nullable',
            'federal_registration_status'           => 'nullable',
            'founding_date'                         => 'nullable',
            'account_type'                          => 'nullable',
            'federal_registration_status_date'      => 'nullable',
            'social_value'                          => 'nullable',
            'document_issue_date'                   => 'nullable',
            'document_issuer'                       => 'nullable',
            'document_issuer_state'                 => 'nullable',
            //'routing_number'    => 'nullable|string|max:255',
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
