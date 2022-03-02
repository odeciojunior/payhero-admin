<?php

namespace Modules\CheckoutEditor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendSupportPhoneVerificationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'required|string',
            'support_phone' => 'required|string',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
