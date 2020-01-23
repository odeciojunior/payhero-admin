<?php


namespace Modules\Checkouts\Transformers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Vinkla\Hashids\Facades\Hashids;

class CheckoutResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {
        return [
            'id'                 => Hashids::encode($this->id ?? ''),
            'ip'                 => $this->ip ?? '',
            'operational_system' => $this->operational_system ?? '',
            'browser'            => $this->browser ?? '',
            'src'                => $this->src ?? '',
            'source'             => $this->utm_source ?? '',
            'utm_medium'         => $this->utm_medium ?? '',
            'utm_campaign'       => $this->utm_campaign ?? '',
            'utm_term'           => $this->utm_term ?? '',
            'utm_content'        => $this->utm_content ?? '',
        ];
    }
}
