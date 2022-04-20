<?php

namespace Modules\Shipping\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Entities\Plan;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ShippingResource
 * @package Modules\Shipping\Transformers
 */
class ShippingResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $applyPlanArray = [];
        $notApplyPlanArray = [];
        $planModel = new Plan();

        if (!empty($this->apply_on_plans)) {
            $applyPlanDecoded = json_decode($this->apply_on_plans);
            if (in_array('all', $applyPlanDecoded)) {
                $applyPlanArray[] = ['id' => 'all', 'name' => 'Qualquer plano', 'description' => ''];
            } else {
                foreach ($applyPlanDecoded as $key => $value) {
                    $plan = $planModel->find($value);
                    if (!empty($plan)) {
                        $applyPlanArray[] = [
                            'id' => Hashids::encode($plan->id),
                            'name' => $plan->name,
                            'description' => $plan->description,
                        ];
                    }
                }
            }
        }

        if (!empty($this->not_apply_on_plans)) {
            $notApplyPlanDecoded = json_decode($this->not_apply_on_plans);
            if (in_array('all', $notApplyPlanArray)) {
                $notApplyPlanArray[] = ['id' => 'all', 'name' => 'Qualquer plano', 'description' => ''];
            } else {
                foreach ($notApplyPlanDecoded as $key => $value) {
                    $plan = $planModel->find($value);
                    if (!empty($plan)) {
                        $notApplyPlanArray[] = [
                            'id' => Hashids::encode($plan->id),
                            'name' => $plan->name,
                            'description' => $plan->description,
                        ];
                    }
                }
            }
        }
        if($this->regions_values){
            $this->value = 'Por regiões';
        }

        return [
            'id_code' => Hashids::encode($this->id),
            'shipping_id' => Hashids::encode($this->id),
            'name' => $this->name,
            'regions_values' => $this->regions_values,
            'information' => $this->type_enum !== 4 ? $this->information : 'Calculado automaticamente',
            'value' => $this->value == null || $this->type_enum != 1 ? 'Calculado automaticamente' : $this->value,
            'type' => $this->present()->getTypeEnum($this->type_enum),
            'type_name' => Lang::get('definitions.enum.shipping.type.' . $this->present()->getTypeEnum($this->type_enum)),
            'type_enum' => $this->type_enum,
            'zip_code_origin' => $this->zip_code_origin,
            'melhorenvio_integration_id' => Hashids::encode($this->melhorenvio_integration_id),
            'status' => $this->status,
            'rule_value' => number_format(($this->rule_value ?? 0) / 100, 2, ',', '.'),
            'status_translated' => Lang::get('definitions.enum.shipping.status.' . $this->present()->getStatus($this->status)),
            'pre_selected' => $this->pre_selected,
            'pre_selected_translated' => Lang::get('definitions.enum.shipping.pre_selected.' . $this->present()->getPreSelectedStatus($this->pre_selected)),
            'receipt' => $this->receipt,
            'own_hand' => $this->own_hand,
            'apply_on_plans' => $applyPlanArray,
            'not_apply_on_plans' => $notApplyPlanArray,
        ];
    }
}
