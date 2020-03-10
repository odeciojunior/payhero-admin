<?php

namespace Modules\ProjectUpsellRule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class ProjectUpsellStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'project_id'     => 'required',
            'description'    => 'required',
            'active_flag'    => 'nullable',
            'apply_on_plans' => 'required|array',
            'offer_on_plans' => 'required|array|max:5',
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
            'description.required'    => 'O campo Descrição é obrigatório',
            'apply_on_plans.required' => 'O campo Ao comprar o plano é obrigatório',
            'offer_on_plans.required' => 'O campo Oferecer o plano é obrigatório',
            'offer_on_plans.max'      => 'O campo Oferecer o plano não pode ter mais de 5 planos',
        ];
    }
}
