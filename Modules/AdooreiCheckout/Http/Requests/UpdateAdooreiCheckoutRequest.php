<?php

declare(strict_types=1);

namespace Modules\AdooreiCheckout\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdooreiCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'webhook' => ['required', 'url'],
            'x-signature' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'O token é obrigatório.',
            'token.string' => 'O token deve ser uma string.',
            'webhook.required' => 'O webhook é obrigatório.',
            'webhook.url' => 'O webhook não é válido.',
            'x-signature.required' => 'O X-signature é obrigatório.',
            'x-signature.string' => 'O X-signature deve ser uma string.',
        ];
    }
}
