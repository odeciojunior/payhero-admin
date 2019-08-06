<?php

namespace Modules\Invites\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Entities\SiteInvitationRequest;
use  App\Entities\HubsmartInvitationRequest;

/**
 * Class InvitesController
 * @package Modules\Invites\Http\Controllers
 */
class InvitesController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('invites::index');
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getInvitation(Request $request)
    {
        $siteInvitationRequestModel = new SiteInvitationRequest();
        $requestData                = $request->all();

        $siteInvitationRequestModel->create($requestData);

        return 'success';
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getHubsmartInvitation(Request $request)
    {

        $hubsmartInvitationRequestModel = new  HubsmartInvitationRequest();
        $requestData                    = $request->all();

        $hubsmartInvitationRequestModel->create($requestData);

        return 'success';
    }
}
