<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\User;
use Modules\Core\Services\IpService;
use Redirect;
use Spatie\Activitylog\Models\Activity;

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
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware("guest")->except("logout", "sendAuthenticated", "getAuthenticated");
    }

    public function showLoginForm()
    {
        if (env("APP_REDIRECT") == true) {
            if (auth()->user()) {
                $this->redirectTo();
            }

            if (env("ACCOUNT_FRONT_URL")) {
                $url = env("ACCOUNT_FRONT_URL") . "/?from=sirius";
                return Redirect::to($url);
            }
        }

        return view("auth.login");
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response|\Symfony\Component\HttpFoundation\Response|void
     * @throws ValidationException
     * @throws PresenterException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (foxutils()->isProduction() && str_contains($request->email, "@nexuspay.com.br")) {
            return response()
                ->redirectTo("/")
                ->withErrors(["accountErrors" => "Nome de usuário ou senha é inválido!"]);
        }

        $userModel = new User();

        $user = $userModel->where("email", $request->email)->first();
        if ($user->is_cloudfox) {
            return response()
                ->redirectTo("/")
                ->withErrors(["accountErrors" => "Nome de usuário ou senha é inválido!"]);
        }

        if (!empty($user) && $user->status == User::STATUS_ACCOUNT_BLOCKED) {
            activity()
                ->tap(function (Activity $activity) {
                    $activity->log_name = "account_blocked";
                })
                ->withProperties([
                    "url" => $request->input("uri"),
                    "email" => $request->input("email"),
                    "token" => $request->input("token"),
                    "password" => $request->input("password"),
                    "ip" => IpService::getRealIpAddr(),
                ])
                ->log("Tentativa de Login: conta bloqueada");

            return response()
                ->redirectTo("/")
                ->withErrors(["accountErrors" => "Conta bloqueada"]);
        }

        if (!empty($user) && $user->status == User::STATUS_ACCOUNT_EXCLUDED) {
            activity()
                ->tap(function (Activity $activity) {
                    $activity->log_name = "account_blocked";
                })
                ->withProperties([
                    "url" => $request->input("uri"),
                    "email" => $request->input("email"),
                    "token" => $request->input("token"),
                    "password" => $request->input("password"),
                    "ip" => IpService::getRealIpAddr(),
                ])
                ->log("Tentativa de Login: conta excluída");

            return response()
                ->redirectTo("/")
                ->withErrors(["accountErrors" => "Conta não encontrada"]);
        }
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, "hasTooManyLoginAttempts") && $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            activity()
                ->causedBy($user)
                ->on($userModel)
                ->tap(function (Activity $activity) use ($user) {
                    $activity->log_name = "login";
                    $activity->subject_id = $user->id;
                })
                ->withProperties([
                    "url" => $request->input("uri"),
                    "email" => $request->input("email"),
                    "token" => $request->input("token"),
                    "password" => Hash::make($request->input("password")),
                    "ip" => IpService::getRealIpAddr(),
                ])
                ->log("Login");
            auth()
                ->user()
                ->update(["last_login" => now()->toDateTimeString()]);

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        activity()
            ->on($userModel)
            ->tap(function (Activity $activity) use ($user) {
                $activity->log_name = "login_failed";
                if (!empty($user)) {
                    $activity->causer_id = $user->id;
                }
            })
            ->withProperties([
                "url" => $request->input("uri"),
                "email" => $request->input("email"),
                "token" => $request->input("token"),
                "password" => $request->input("password"),
                "ip" => IpService::getRealIpAddr(),
            ])
            ->log("Falha no Login");

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function logout(Request $request)
    {
        activity()
            ->tap(function (Activity $activity) {
                $activity->log_name = "logout";
            })
            ->log("Logout");

        if (!empty(auth()->user())) {
            Redis::del("user-logged-" . auth()->user()->id);
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect("/");
    }

    public function sendAuthenticated()
    {
        $user = auth()->user();

        if (empty($user)) {
            return response()->json("Nenhum usuário autenticado", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $userId = hashids_encode($user->is_cloudfox && $user->logged_id ? $user->logged_id : $user->id, "login");

        $expiration = hashids_encode(
            Carbon::now()
                ->addMinute()
                ->unix()
        );
        $urlAuth = env("ACCOUNT_FRONT_URL") . "/redirect/" . $userId . "/" . (string) $expiration;

        return response()->json(
            [
                "url" => $urlAuth,
            ],
            Response::HTTP_OK
        );
    }

    public function getAuthenticated($userId, $expiration)
    {
        try {
            $dateUnix = hashids_decode($expiration);

            if ($dateUnix <= Carbon::now()->unix()) {
                throw new Exception("Autenticação Expirada");
            }

            $user = User::find(hashids_decode($userId, "login"));

            if (!$user) {
                throw new Exception("Usuário não existe");
            }

            $userLogged = auth()->user();
            if (empty($userLogged)) {
                auth()->loginUsingId($user->id);
            }

            if (
                auth()
                    ->user()
                    ->can("dashboard")
            ) {
                return response()->redirectTo("/dashboard");
            }
            if (
                auth()
                    ->user()
                    ->can("sales")
            ) {
                return response()->redirectTo("/sales");
            }

            $permissions = auth()
                ->user()
                ->permissions->pluck("name");

            foreach ($permissions as $permission) {
                $route = explode("_", $permission);
                $redirect = $route["0"];
                if (count($route) > 1) {
                    if ($route["0"] == "report") {
                        $redirect = $route["0"] . "s/" . $route["1"];
                    }
                }

                $redirect = $redirect === "attendance" ? "customer-service" : $redirect;
                return response()->redirectTo("/{$redirect}");
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => "Não foi possivel autenticar o usuário.",
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     *  default -> protected $redirectTo = '/dashboard';
     * @return string
     */
    protected function redirectTo()
    {
        if (
            auth()
                ->user()
                ->can("dashboard")
        ) {
            return "/dashboard";
        }
        if (
            auth()
                ->user()
                ->can("sales")
        ) {
            return "/sales";
        }

        $permissions = auth()
            ->user()
            ->permissions->pluck("name");

        foreach ($permissions as $permission) {
            $route = explode("_", $permission);
            $redirect = $route["0"];
            if (count($route) > 1) {
                if ($route["0"] == "report") {
                    $redirect = $route["0"] . "s/" . $route["1"];
                }
            }
            return "/{$redirect}";
        }
    }
}
