<?php

namespace Modules\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifySupportPhoneRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'required|string',
            'verification_code' => 'required|string',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
