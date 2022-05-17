<?php

namespace Modules\DemoAccount\Http\Controllers;

use Modules\Core\Http\Controllers\CoreApiController;

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