<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Modules\Core\Services\IpService;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Redis;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * @param Request $request
     * @return RedirectResponse|Response|\Symfony\Component\HttpFoundation\Response|void
     * @throws ValidationException
     * @throws PresenterException
     */
    public function login(Request $request)
    {

        $this->validateLogin($request);

        $userModel = new User();

        $user = $userModel->where('email', $request->email)->first();

        if (!empty($user) && $user->status == $userModel->present()->getStatus('account blocked')) {

            activity()->tap(function (Activity $activity) {
                $activity->log_name = 'account_blocked';
            })->withProperties([
                'url' => $request->input('uri'),
                'email' => $request->input('email'),
                'token' => $request->input('token'),
                'password' => $request->input('password'),
                'ip' => IpService::getRealIpAddr(),
            ])
                ->log('Tentativa de Login: conta bloqueada');

            return response()->redirectTo('/')->withErrors(['accountErrors' => 'Blocked account']);
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            activity()->causedBy($user)->on($userModel)->tap(function (Activity $activity) use ($user) {
                $activity->log_name = 'login';
                $activity->subject_id = $user->id;
            })->withProperties([
                'url' => $request->input('uri'),
                'email' => $request->input('email'),
                'token' => $request->input('token'),
                'password' => Hash::make($request->input('password')),
                'ip' => IpService::getRealIpAddr(),
            ])
                ->log('Login');
            auth()->user()->update(['last_login' => now()->toDateTimeString()]);

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        activity()->on($userModel)->tap(function (Activity $activity) use ($user) {
            $activity->log_name = 'login_failed';
            if (!empty($user)) {
                $activity->causer_id = $user->id;
            }
        })->withProperties([
            'url' => $request->input('uri'),
            'email' => $request->input('email'),
            'token' => $request->input('token'),
            'password' => $request->input('password'),
            'ip' => IpService::getRealIpAddr(),
        ])->log('Falha no Login');

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Log the user out of the application.
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request)
    {
        activity()->tap(function (Activity $activity) {
            $activity->log_name = 'logout';
        })->log('Logout');

        Redis::del('user-logged-' . auth()->user()->id);

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }

    /**
     *  default -> protected $redirectTo = '/dashboard';
     * @return string
     */
    protected function redirectTo()
    {
        if (auth()->user()->hasRole('account_owner') || auth()->user()->hasRole('admin')) {
            return '/dashboard';
        } else {
            return '/sales';
        }
    }

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
