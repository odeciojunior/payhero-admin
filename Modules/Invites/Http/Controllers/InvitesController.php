<?php

namespace Modules\Invites\Http\Controllers;

use App\Entities\Company;
use App\Entities\Invitation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Helpers\EmailHelper;
use Modules\Core\Helpers\StringHelper;
use App\Entities\SiteInvitationRequest;
use App\Entities\HubsmartInvitationRequest;
use Modules\Invites\Http\Requests\SendInvitationRequest;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class InvitesController
 * @package Modules\Invites\Http\Controllers
 */
class InvitesController extends Controller
{
    /**
     * @var Invitation
     */
    private $invitation;
    /**
     * @var Company
     */
    private $company;
    /**
     * @var SiteInvitationRequest
     */
    private $siteInvitationRequest;
    /**
     * @var HubsmartInvitationRequest
     */
    private $hubsmartInvitationRequest;

    /**
     * InvitesController constructor.
     * @param Invitation $invitation
     * @param Company $company
     * @param SiteInvitationRequest $siteInvitationRequest
     * @param HubsmartInvitationRequest $hubsmartInvitationRequest
     */
    function __construct(Invitation $invitation,
                         Company $company,
                         SiteInvitationRequest $siteInvitationRequest,
                         HubsmartInvitationRequest $hubsmartInvitationRequest)
    {
        $this->invitation                = $invitation;
        $this->company                   = $company;
        $this->siteInvitationRequest     = $siteInvitationRequest;
        $this->hubsmartInvitationRequest = $hubsmartInvitationRequest;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $invites = $this->invitation->where('invite', auth()->user()->id)->get();

        foreach ($invites as $invite) {

            if ($invite->status == 'Enviado') {
                $invite->status = "<span class='badge badge-info'>Enviado</span>";
            } else {
                $invite->status = "<span class='badge badge-success'>" . $invite->status . "</span>";
            }
        }

        return view('invites::index', [
            'invites' => $invites,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendInvitation(SendInvitationRequest $request)
    {
        try {

            $requestData = $request->validated();

            $requestData['invite'] = auth()->user()->id;
            $requestData['status'] = "Convite enviado";

            $newParameter = false;

            while (!$newParameter) {

                //$parameter = Hashids::encode(auth()->user()->id + 77991133);
                $parameter = StringHelper::randString(15);

                $invite = $this->invitation->where('parameter', $parameter)->first();

                if ($invite == null) {
                    $newParameter             = true;
                    $requestData['parameter'] = $parameter;
                }
            }

            $requestData['company'] = $this->company->where('user', auth()->user()->id)->first()->id;

            $invite = $this->invitation->create($requestData);

            if ($invite) {
                EmailHelper::enviarConvite($requestData['email_invited'], $requestData['parameter']);
            }

            return redirect()->route('invitations.invites');
        } catch (Exception $ex) {
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

        $requestData = $request->all();

        $this->siteInvitationRequest->create($requestData);

        return 'success';
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getHubsmartInvitation(Request $request)
    {

        $requestData = $request->all();

        $this->hubsmartInvitationRequest->create($requestData);

        return 'success';
    }
}
