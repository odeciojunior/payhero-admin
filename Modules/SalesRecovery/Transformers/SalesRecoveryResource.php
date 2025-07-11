<?php

namespace Modules\SalesRecovery\Transformers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed created_at
 * @property mixed id
 * @property mixed project
 * @property mixed name
 * @property mixed status
 * @property mixed checkoutPlans
 * @property mixed telephone
 */
class SalesRecoveryResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
                //
            ];
    }
}
