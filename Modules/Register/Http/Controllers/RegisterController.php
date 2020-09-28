<?php

namespace Modules\Register\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        return view('register::create');
    }

    /**
     * @param $userId
     * @return Application|Factory|RedirectResponse|View
     */
    public function loginAsSomeUser($userId)
    {
        $userIdDecode = Hashids::decode($userId)[0];

        if (!empty($userIdDecode)) {
            auth()->loginUsingId($userIdDecode);

            return response()->redirectTo('/dashboard');
        }

        return view('errors.404');
    }

    // Método que permite o registro apenas com o convite

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function createInvitation(Request $request)
    {

        $companyId = current(Hashids::decode($request->segment(2)));

        if (!empty($companyId)) {
            $invitation = Invitation::where(['company_id' => $companyId])->first();

            if (env('APP_ENV') == 'local')
                if (!empty($invitation))
                    return view('register::create');

            return view('register::notInvite');

        } else {

            return view('register::notInvite');
        }

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


