<?php

namespace Modules\Register\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        return view('register::create');
    }

    public function loginAsSomeUser($userId)
    {
        auth()->loginUsingId($userId);

        return response()->redirectTo('/dashboard');
    }
}


