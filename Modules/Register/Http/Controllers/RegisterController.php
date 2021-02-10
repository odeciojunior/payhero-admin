<?php

namespace Modules\Register\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\User;
use Spatie\Activitylog\Models\Activity;
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
    public function loginAsSomeUser($managerId, $userId)
    {
        $userIdDecode = Hashids::decode($userId)[0];
        $managerIdDecode = Hashids::decode($managerId)[0];
        if (!empty($userIdDecode) && !empty($managerIdDecode)) {
            $user = auth()->loginUsingId($userIdDecode);
            $userModel = new User();
            activity()->on($userModel)->tap(
                function (Activity $activity) use ($managerIdDecode) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = $managerIdDecode;
                    $activity->causer_id = $managerIdDecode;
                }
            )->log('Fez login na conta do usuário ' . $user->name);

            if (auth()->user()->hasRole('account_owner') || auth()->user()->hasRole('admin')) {
                return response()->redirectTo('/dashboard');
            } else {
                return response()->redirectTo('/sales');
            }

        }

        return view('errors.404');
    }

    // Método que permite o registro apenas com o convite

    /**
     * @param Request $request
     * @return Application|Factory|JsonResponse|View
     */
    public function createInvitation(Request $request)
    {
        $companyIdRequest = preg_replace('/i=/', '', $request->segment(2));
        $companyId = current(Hashids::decode($companyIdRequest));

        if (!empty($companyId)) {
            $invitation = Invitation::where(['company_id' => $companyId])->first();

            if (env('APP_ENV') == 'local')
                if (!empty($invitation))
                    return view('register::create');

            return response()->json([
                'message' => 'Não Foi Possível acessar o convite'
            ]);

        } else {

            return response()->json([
                'message' => 'Não Foi Possível acessar o convite'
            ]);
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
        }

    }

}


