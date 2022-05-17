<?php

namespace Modules\DemoAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DemoAccountController extends Controller
{
    public function notAuthorized(){
        return response(['message'=>'Sem permissão para realizar esta ação.'],403);
    }
}
