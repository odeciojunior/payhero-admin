<?php

namespace Modules\Register\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\User;
use Illuminate\Routing\Controller;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Register\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{

    public function create($parameter)
    {
        return view('register::create');
    }

    public function loginAsSomeUser($userId){

        auth()->loginUsingId($userId);

        return response()->redirectTo('/dashboard');
    }
}


