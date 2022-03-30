<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Core\Entities\User;
use Illuminate\Routing\Controller;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Cookie;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Contracts\Support\Renderable;

class CoreController extends Controller
{


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

            if (FoxUtils::isProduction()) {
                Cookie::queue(
                    Cookie::make(
                        'isManagerUser',
                        true,
                        time() + 60 * 60 * 24 * 1,
                    )
                );
            }

            if (auth()->user()->can('dashboard')) {
                return response()->redirectTo('/dashboard');
            }elseif (auth()->user()->can('sales')) {
                return response()->redirectTo('/sales');
            }else{
                $permissions =  auth()->user()->permissions->pluck('name');
                foreach($permissions as $permission){
                    $route = explode('_',$permission);
                    $redirect = $route['0'];
                    if(count($route) > 1){
                        if($route['0']=='report'){
                            $redirect= $route['0'].'s/'.$route['1'];
                        }
                    }
                    return response()->redirectTo("/{$redirect}");
                }
            }

        }

        return view('errors.404');
    }

}
