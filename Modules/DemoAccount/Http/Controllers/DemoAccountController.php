<?php

namespace Modules\DemoAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DemoAccountController extends Controller
{
    public function notAuthorized(){
        return response(['message'=>'Operação desabilitada na conta demo.'],403);
    }
}
