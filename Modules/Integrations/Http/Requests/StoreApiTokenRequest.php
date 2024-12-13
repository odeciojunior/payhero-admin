<?php

declare(strict_types=1);

namespace Modules\Integrations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApiTokenRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'description' => 'required|string',
            'platform_enum' => 'nullable|string',
            'company_id' => 'required|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'platform_enum' => $this->input('platform_enum', 'WEBAPI') ?? 'WEBAPI',
        ]);
    }

    public function messages(): array
    {
        return [
            'description.required' => 'O campo Descrição é obrigatório.',
        ];
    }
}
