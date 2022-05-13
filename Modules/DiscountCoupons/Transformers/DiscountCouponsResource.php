<?php

namespace Modules\DiscountCoupons\Transformers;

use Illuminate\Support\Facades\Lang;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class DiscountCouponsResource extends JsonResource
{
    public function toArray($request)
    {
        $plans = json_decode($this->plans, true);
        if(gettype($plans)=='array'){
            $plans_description = 'em '.count($plans).' plano'.(count($plans)>1?'s':'');
            if(count($plans)==0){
                $plans_description = 'em todos os planos';

            }
        }else{
            $plans_description = 'em todos os planos';

        };
        
        $prog_rules = json_decode($this->progressive_rules, true);
        if(gettype($prog_rules)=='array'){
            if(count($prog_rules)==1){
                if(empty($prog_rules[0]['buy'])) $prog_rules[0]['buy'] = 'above_of';
                $rule = $prog_rules[0]['buy']=='above_of'?'apartir de ':'na compra de ';
                $value = $prog_rules[0]['type'] == 'percent' ? $prog_rules[0]['value'].'%' : 'R$'.str_replace('.',',', $prog_rules[0]['value']);
                $rules_description = $value.'<br><span class="small-text">'.$rule.$prog_rules[0]['qtde'].' ite'.($prog_rules[0]['qtde']==1?'m':'ns').'</span>';
            }else{
                $types = '';
                foreach ($prog_rules as $value) {
                    $types .= $value['type'].' ';
                }
                
                if(stristr($types, 'percent'))
                    $type='%';

                if(stristr($types, 'value'))
                    $type='R$';
                
                if(stristr($types, 'percent') && stristr($types, 'value'))
                    $type='R$ e %';

                $rules_description = count($prog_rules).' regras<br><span class="small-text">descontos em '.$type.'</span>';

            }
        }else{
            if($this->type == 0){
                $rules_description = $this->value.'%';
            }else{
                $rules_description = 'R$'.number_format($this->value / 100, 2, ',', '.');

            }
            if($this->rule_value>0){

                $rules_description .= '<br><span class="small-text"> a partir de R$'.number_format($this->rule_value / 100, 2, ',', '.').'</span>';
            }else{
                $rules_description .= '<br>sem valor mínimo';

            }
        };
        
        
        $code_description = '<br><span class="small-text">%</span>';
        if(!empty($this->expires)){
            $expires = date("d/m/Y", strtotime($this->expires));
            $verb = 'vence';
            if(strtotime($this->expires) < strtotime(date("Y-m-d"))){
                $verb = 'venceu';
                
                $this->status = 0;
            }
            $code_description = str_replace('%',$verb.' em '.$expires, $code_description);
        }else{
            
            $code_description = str_replace('%','não vence', $code_description);

        }
        
        

        return [
            'id'                => Hashids::encode($this->id),
            'name'              => $this->name,
            'type'              => $this->type == 0 ? 'Porcentagem' : 'Valor',
            'discount'          => $this->discount == 1 ? 'Progressivo' : 'Cupom',
            'rule_value'        => number_format($this->rule_value / 100, 2, ',', '.'),
            'code'              => $this->code == ''?'Automático':$this->code.$code_description,
            'plans'             => isset($plans_description)?$plans_description:'',
            // 'expires'           => $expires,
            'value'             => isset($rules_description)?$rules_description:'',
            'status'            => $this->status,
            'status_translated' => $this->present()->getStatus($this->status)=='active'?'Ativo':'Desativado',
        ];
    }
}
