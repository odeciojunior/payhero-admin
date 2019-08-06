<?php

namespace Modules\Invites\Http\Controllers;

use App\Entities\Invitation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\EmailService;
use Modules\Invites\Transformers\InviteResource;
use Vinkla\Hashids\Facades\Hashids;

class InvitesApiController extends Controller
{
    public function index()
    {
        try {

            $invitationModel = new Invitation();

            $invites = $invitationModel->where('invite', auth()->user()->id);

            return InviteResource::collection($invites->orderBy('register_date', 'DESC')->paginate(5));
        } catch (Exception $e) {
            Log::warning('Erro ao tentar listar convites (InvitesApiController - index)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao tentar listar convites ',
                                    ], 400);
        }
    }

    public function store(Request $request)
    {
        try {

            $invitationModel = new Invitation();
            $inviteSaved     = null;

            $company = current(Hashids::decode($request->input('company')));
            $invite  = $invitationModel->where([['email_invited', $request->input('email')], ['company', $company]])
                                       ->first();

            if (!$invite) {
                $data   = [
                    'invite'        => auth()->user()->id,
                    'status'        => $invitationModel->getEnum('status', 'pending'),
                    'company'       => current(Hashids::decode($request->input('company'))),
                    'email_invited' => $request->input('email'),
                    'parameter'     => $request->input('company'),
                ];
                $invite = $invitationModel->create($data);
            }
            try {

                if (!$invite) {
                    return response()->json([
                                                'message' => 'Erro ao tentar enviar convite',
                                            ], 400);
                } else {
                    EmailService::sendInvite($invite['email_invited'], $invite['parameter']);

                    return response()->json([
                                                'message' => 'Convite enviado com sucesso!',
                                            ], 200);
                }
            } catch (Exception $e) {
                Log::warning('Erro ao tentar enviar convite (InvitesApiController - store)');
                report($e);

                return response()->json([
                                            'message' => 'Erro ao tentar enviar convite',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar enviar convite (InvitesApiController - index)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao tentar enviar convite',
                                    ], 400);
        }
    }
}
