<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Modules\Core\Entities\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
     * Handle a login request to the application.
     * Overwritten from trait AuthenticatesUsers for handle user with account blocked
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        $userModel = new User();

        $user = $userModel->where('email', $request->email)->first();

        if (!empty($user) && $user->status == $userModel->present()->getStatus('account blocked')) {

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
            auth()->user()->update(['last_login' => now()->toDateTimeString()]);

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
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
