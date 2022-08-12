<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Entities\User;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class CheckAccountStatusWeb
{
    use AuthenticatesUsers;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (in_array(auth()->user()->status ?? null, [User::STATUS_ACCOUNT_BLOCKED, User::STATUS_ACCOUNT_EXCLUDED])) {
            activity()
                ->tap(function (Activity $activity) {
                    $activity->log_name = "logout";
                })
                ->log("Logout automatic system");

            if (!empty(auth()->user())) {
                Redis::del("user-logged-" . auth()->user()->id);
            }

            $this->guard()->logout();

            $request->session()->invalidate();

            return $this->loggedOut($request) ?: redirect("/");
        }

        return $next($request);
    }
}
