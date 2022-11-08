<?php

namespace Modules\ProjectUpsellRule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class ProjectUpsellUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            "description" => "required",
            "discount" => "required|numeric|min:0|not_in:0",
            "type" => "nullable",
            "active_flag" => "nullable",
            "use_variants" => "nullable",
            "apply_on_shipping" => "required|array",
            "apply_on_plans" => "required|array",
            "offer_on_plans" => "required|array|max:5",
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

    public function messages()
    {
        return [
            "description.required" => "O campo Descrição é obrigatório",
            "discount.required" => "O campo Desconto é obrigatório",
            "discount.not_in" => "O campo Desconto não pode ser zero",
            "apply_on_shipping.required" => "O campo Ao selecionar o frete é obrigatório",
            "apply_on_plans.required" => "O campo Ao comprar o plano é obrigatório",
            "offer_on_plans.required" => "O campo Oferecer o plano é obrigatório",
            "offer_on_plans.max" => "O campo Oferecer o plano não pode ter mais de 5 planos",
        ];
    }
}