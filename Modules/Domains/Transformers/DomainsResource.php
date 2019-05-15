<?php

namespace Modules\Dominios\Transformers;

use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\Zones;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class DomainsResource extends Resource {

    public function toArray($request) {

        $key = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');

        $adapter = new Guzzle($key);
        $zones   = new Zones($adapter);

        $status = '';
        try{
            $zoneID = $zones->getZoneID($dominio->name); 
            $status = $zones->activationCheck($zoneID);
            if($status){
                $status = 'conectado';
            }
            else{
                $status = 'Desconectado';
            }
        }
        catch(\Exception $e){
            $status = $this->status;
        }

        return [
            'id'         => Hashids::encode($this->id),
            'dominio'    => $this->name,
            'ip_dominio' => $this->domain_ip,
            'status'     => $status   
        ];
    }

}
