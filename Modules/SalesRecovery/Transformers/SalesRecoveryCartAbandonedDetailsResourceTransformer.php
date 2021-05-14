<?php

namespace Modules\SalesRecovery\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesRecoveryCartAbandonedDetailsResourceTransformer extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request)
    {

        $products = [];
        foreach ($this['products'] as $product) {
            $products[] = [
                'photo'  => $product['photo'],
                'name'   => $product['name'],
                'amount' => $product['amount'],
            ];
        }

        $client = [
            'name'          => $this['client']->name ?? '',
            'telephone'     => $this['client']->telephone ?? '',
            'whatsapp_link' => $this['client']->whatsapp_link ?? '',
            'email'         => $this['client']->email ?? '',
            'document'      => $this['client']->document ?? '',
            'error'         => $this['client']->error ?? '',
        ];

        $delivery = [
            'street'   => $this['checkout']->street ?? '',
            'zip_code' => $this['checkout']->zip_code ?? '',
            'city'     => $this['checkout']->city ?? '',
            'state'    => $this['checkout']->state ?? '',
        ];

        $checkout = [
            'date'               => $this['checkout']->date,
            'hours'              => $this['checkout']->hours,
            'total'              => $this['checkout']->total,
            'ip'                 => $this['checkout']->ip,
            'is_mobile'          => $this['checkout']->is_mobile,
            'operational_system' => $this['checkout']->operational_system,
            'browser'            => $this['checkout']->browser,
            'src'                => $this['checkout']->src,
            'utm_source'         => $this['checkout']->utm_source,
            'utm_medium'         => $this['checkout']->utm_medium,
            'utm_campaign'       => $this['checkout']->utm_campaign,
            'utm_term'           => $this['checkout']->utm_term,
            'utm_content'        => $this['checkout']->utm_content,
        ];

        return [
            'checkout' => $checkout,
            'client'   => $client,
            'products' => $products,
            'delivery' => $delivery,
            'status'   => $this['status'],
            'link'     => $this['link'],
            'method'   => 'cartAbandoned',

        ];
    }
}
