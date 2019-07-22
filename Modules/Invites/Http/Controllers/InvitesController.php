<?php

namespace Modules\Invites\Http\Controllers;

use Exception;
use App\Entities\Company;
use App\Entities\Invitation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Helpers\EmailHelper;
use App\Entities\SiteInvitationRequest;
use Modules\Core\Services\EmailService;
use App\Entities\HubsmartInvitationRequest;
use Modules\Invites\Http\Requests\SendInvitationRequest;

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
        $invitationModel = new Invitation();
        $companyModel    = new Company();

        $invites = $invitationModel->where('invite', auth()->user()->id)->get();

        $companies = $companyModel->where('user_id', auth()->user()->id)->get();

        return view('invites::index', [
            'invites'   => $invites,
            'companies' => $companies,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendInvitation(SendInvitationRequest $request)
    {
        try {
            $invitationModel = new Invitation();

            $requestData = $request->validated();

            $requestData['invite']    = auth()->user()->id;
            $requestData['status']    = $invitationModel->getEnum('status', 'pending');
            $requestData['parameter'] = $requestData['company'];
            $requestData['company']   = current(Hashids::decode($requestData['company']));

            $invite = $invitationModel->where('email_invited', $requestData['email_invited'])->first();
            if ($invite) {
                $invite->delete();
            }

            $invite = $invitationModel->create($requestData);

            if ($invite) {
                EmailService::sendInvite($requestData['email_invited'], $requestData['parameter']);
            }

            return redirect()->route('invitations.invites');
        } catch (Exception $ex) {
            dd($ex);
            Log::warning('Erro ao enviar convite (InvitesController - sendInvitation)');
            report($ex);
        }
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

        $hubsmartInvitationRequestModel = HubsmartInvitationRequest();
        $requestData                    = $request->all();

        $hubsmartInvitationRequestModel->create($requestData);

        return 'success';
    }
}
