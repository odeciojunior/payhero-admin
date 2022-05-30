<?php

namespace Modules\DemoAccount\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Dashboard\Http\Controllers\DashboardApiController;

class DashboardApiDemoController extends DashboardApiController
{
    public function index(): JsonResponse{
        
        $companies = Company::where('user_id', User::DEMO_ID)
                    ->where('active_flag', true)
                    ->orderBy('order_priority')
                    ->get() ?? collect();

        return response()->json(['companies' => $companies]);
    }

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