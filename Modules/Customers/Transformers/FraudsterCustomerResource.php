<?php

namespace Modules\Customers\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ClientResource
 * @property mixed name
 * @property mixed document
 * @property mixed email
 * @property mixed telephone
 * @property mixed id_code
 * @package Modules\Customers\Transformers
 */
class FraudsterCustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $customerName = $this->name;
        $name = explode(" ", $customerName);
        $customerName = $name[0];
        array_shift($name);
        $customerName .= " " . preg_replace("/\S/", "*", implode(" ", $name));

        $customerDocument = substr($this->document, 0, 3) . ".***.***-" . substr($this->document, -2);

        return [
            "code" => $this->id_code,
            "name" => $customerName,
            "document" => $customerDocument,
            "email" => preg_replace('/(?:^|@).\K|\.[^@]*$(*SKIP)(*F)|.(?=.*?\.)/', "*", $this->present()->getEmail()),
            "telephone" => "+55***********",
            //            'whatsapp_link' => $this->present()->getWhatsappLink(),
            "fraudster" => true,
        ];
    }
}
