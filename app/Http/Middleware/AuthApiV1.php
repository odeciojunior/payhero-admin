<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Entities\ApiToken;

class AuthApiV1
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $bearerToken = explode(" ", $request->header("Authorization"));
        if (isset($bearerToken[1])) {
            $apiToken = ApiToken::where("access_token", $bearerToken[1])
                ->where("integration_type_enum", 5)
                ->first();

            if ($apiToken) {
                $request->user_id = $apiToken->user->id;

                return $next($request);
            }
        }

        return response()->json("Unauthorized", 403);
    }
}
