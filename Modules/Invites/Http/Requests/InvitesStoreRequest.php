<?php

namespace Modules\Invites\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvitesStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'email_invited' => 'required|email',
            'company'       => 'required',
        ];
    }

    public function messages()
    {
        return [
            'email_invited.required' => 'O campo Email do Convidado é obrigatório',
            'company.required'       => 'O campo Empresa para receber é obirgatório',
        ];
    }
}
