<?php

namespace Modules\Invites\Http\Controllers;

use App\Entities\Invitation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\EmailService;
use Modules\Core\Services\FoxUtils;
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
            if (!empty($request->input('email')) && !empty($request->input('company'))) {
                $invitationModel = new Invitation();
                $inviteSaved     = null;

                $company = current(Hashids::decode($request->input('company')));

                if (FoxUtils::validateEmail($request->input('email')) && !empty($company)) {
                    try {
                        $invite = $invitationModel->where([['email_invited', $request->input('email')], ['company', $company]])
                                                  ->first();

                        $sendgridService = new EmailService();
                        $emailInvited    = $sendgridService->sendInvite($request->input('email'), $company);

                        if ($emailInvited == 'error') {
                            return response()->json([
                                                        'message' => 'Erro ao tentar enviar convite, tente novamente mais tarde.',
                                                    ], 400);
                        } else {
                            if ($emailInvited->statusCode() != 202) {
                                return response()->json([
                                                            'message' => 'Erro ao tentar enviar convite, tente novamente mais tarde.',
                                                        ], 400);
                            } else {
                                if (!$invite) {
                                    $data = [
                                        'invite'        => auth()->user()->id,
                                        'status'        => $invitationModel->getEnum('status', 'pending'),
                                        'company'       => current(Hashids::decode($request->input('company'))),
                                        'email_invited' => $request->input('email'),
                                        'parameter'     => $request->input('company'),
                                    ];
                                    $invitationModel->create($data);
                                }

                                return response()->json([
                                                            'message' => 'Convite enviado com sucesso!',
                                                        ]);
                            }
                        }
                    } catch (Exception $e) {
                        Log::warning('Erro ao tentar enviar convite (InvitesApiController - store)');
                        report($e);

                        return response()->json([
                                                    'message' => 'Erro ao tentar enviar convite, tente novamente mais tarde.',
                                                ], 400);
                    } catch (\Throwable $e) {
                        Log::warning('Erro ao tentar enviar convite (InvitesApiController - store)');
                        report($e);

                        return response()->json([
                                                    'message' => 'Erro ao tentar enviar convite, tente novamente mais tarde.',
                                                ], 400);
                    }
                } else {
                    return response()->json([
                                                'message' => 'Erro ao tentar enviar convite, Email inválido.',
                                            ], 400);
                }
            } else {
                return response()->json([
                                            'message' => 'Erro ao tentar enviar convite, Email e Empresa a receber são campos obrigatórios.',
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
