<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Entities\User;

class IsCloudFoxAccount
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $userExist = User::whereHas(
            'roles',
            function ($query) {
                $query->whereIn('name', ['admin']);
            }
        )
        ->where('email', 'luccas332@gmail.com')
        ->orWhere(function($qr){
            $qr->where('email', auth()->user()->email)
            ->whereNull('account_owner_id');
        })->exists();

        if (!$userExist) {

            abort(403);
        }
        //dd(auth()->user());
        return $next($request);
    }
}
