<?php

namespace Modules\Register\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Modules\Core\Entities\User;

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

    public function userFirstLoginByToken($token)
    {

        try {

            if (!$token)
                throw new \Exception('token não informado');

            $token_decode = base64_decode($token);
            $userid = Crypt::decrypt($token_decode);

            $user = User::find($userid);

            if (!$user)
                throw new \Exception('Usuário não existe');

            $date_now = \Carbon\Carbon::now();

            if ($date_now->diffInMinutes($user->created_at) > 30 || !is_null($user->last_login)) {
                throw new \Exception('token expirado ou usuário já fez o primeiro acesso');
            }

            $user->last_login = $date_now;
            $user->save();

            auth()->loginUsingId($user->id);
            return response()->redirectTo('/dashboard');

        } catch (Exception $e) {
            report($e);
            dd($e->getMessage());
        }


    }
}


