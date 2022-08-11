<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Entities\User;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class CheckAccountStatusApi
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

            auth()
                ->user()
                ->token()
                ->revoke();

            return response()->json(["message" => "Unauthenticated"], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
