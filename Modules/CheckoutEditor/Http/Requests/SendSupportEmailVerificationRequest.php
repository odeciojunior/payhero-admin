<?php

namespace Modules\CheckoutEditor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendSupportEmailVerificationRequest extends FormRequest
{
    public function rules()
    {
        return [
            "id" => "required|string",
            "support_email" => "required|email",
        ];
    }

    public function authorize()
    {
        return true;
    }
}
