<?php

namespace Modules\OrderBump\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vinkla\Hashids\Facades\Hashids;

class OrderBumpRequest extends FormRequest
{
    public function rules()
    {
        return [
            'project_id' => 'required',
            'description' => 'required',
            'discount' => 'required',
            'active_flag' => 'boolean',
            'apply_on_plans' => 'required|array',
            'offer_plans' => 'required|array|max:5',
        ];
    }

    public function messages()
    {
        return [
            'description.required' => 'O campo Descrição é obrigatório',
            'discount.required' => 'O campo Desconto é obrigatório',
            'apply_on_plans.required' => 'O campo Ao comprar o plano é obrigatório',
            'offer_plans.required' => 'O campo Oferecer o plano é obrigatório',
            'offer_plans.max' => 'O campo Oferecer o plano não pode ter mais de 5 planos',
        ];
    }

    public function getData()
    {
        $data = parent::validated();

        $data['project_id'] = current(Hashids::decode($data['project_id']));

        if (!in_array('all', $data['apply_on_plans'])) {
            $data['apply_on_plans'] = array_map(function ($plan) {
                return current(Hashids::decode($plan));
            }, $data['apply_on_plans']);
        }
        $data['apply_on_plans'] = json_encode($data['apply_on_plans']);
        $data['offer_plans'] = json_encode(array_map(function ($plan) {
            return current(Hashids::decode($plan));
        }, $data['offer_plans']));

        $data['active_flag'] = boolval($data['active_flag']);

        return $data;
    }
}
