<?php

namespace Modules\DemoAccount\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Config;
use Modules\Core\Entities\Company;
use Modules\Core\Http\Controllers\CoreApiController;
use Modules\Core\Transformers\CompaniesSelectResource;
use Vinkla\Hashids\Facades\Hashids;

class CoreApiDemoController extends CoreApiController
{
    public function verifyDocuments(){
        return response()->json(
            [            
                "message"=> "Documentos verificados!",
                "analyzing"=> false,
                "refused"=>null,
                "accountType"=> "owner",
                "accountStatus"=> "active"                  
            ]
        );
    }


}