<?php

namespace Modules\DemoAccount\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;

class CoreApiDemoController extends Controller
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