<?php

namespace Modules\DemoAccount\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Dashboard\Http\Controllers\DashboardApiController;

class DashboardApiDemoController extends DashboardApiController
{
    

    public function getAchievements(){
        return response()->json([            
            "message"=> "Onboarding já lido",
            "read"=> true              
        ]);
    }

    public function verifyPixOnboarding(){
        return response()->json([            
            "message"=> "Onboarding já lido",
            "read"=> true              
        ]);
    }
}