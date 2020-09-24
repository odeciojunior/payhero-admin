<?php

namespace Modules\Register\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        return view('register::create');
    }

    public function loginAsSomeUser($userId)
    {
        $userIdDecode = Hashids::decode($userId)[0];

        if (!empty($userIdDecode)) {
            auth()->loginUsingId($userIdDecode);

            return response()->redirectTo('/dashboard');
        }

        return view('errors.404');
    }
}


