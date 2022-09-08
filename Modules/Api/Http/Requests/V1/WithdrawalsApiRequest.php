<?php

namespace Modules\Api\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalsApiRequest extends FormRequest
{
    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        $this->merge(["user_id" => request()->user_id]);

        if ($this->company_id) {
            $this->merge(["company_id" => hashids_decode($this->company_id)]);
        }

        if ($this->gateway_id) {
            $this->merge(["gateway_id" => hashids_decode($this->gateway_id)]);
        }

        return $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "user_id" => "required|exists:users,id",
            "company_id" => "required|exists:companies,id",
            "gateway_id" => "required|exists:gateways,id",
            "withdrawal_value" => "required",
        ];
    }

    public function messages()
    {
        return [
            "user_id.required" => "Usuário não informado",
            "user_id.exists" => "Usuário não encontrado",
            "company_id.required" => "Empresa não informada",
            "company_id.exists" => "Empresa não encontrada",
            "gateway_id.required" => "Gateway não informado",
            "gateway_id.exists" => "Gateway não encontrado",
            "withdrawal_value.required" => "Valor para saque não informado",
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
