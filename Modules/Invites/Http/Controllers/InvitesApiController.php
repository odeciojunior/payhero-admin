<?php

namespace Modules\Invites\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Services\FoxUtils;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\Invitation;
use Modules\Core\Services\EmailService;
use Modules\Invites\Transformers\InviteResource;

class InvitesApiController extends Controller
{
    public function index()
    {
        try {

            $invitationModel = new Invitation();

            $invites = $invitationModel->where('invite', auth()->user()->id)->with('company');

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
                $userModel       = new User();
                $inviteSaved     = null;

                $invitesSent = Invitation::where('invite', auth()->user()->id)->count();

                if ($invitesSent == auth()->user()->invites_amount) {
                    return response()->json([
                                                'message' => 'Envio de convites indisponível, limite atingido!',
                                            ], 400);
                }

                $company = current(Hashids::decode($request->input('company')));
                if (FoxUtils::validateEmail($request->input('email')) && !empty($company)) {
                    try {
                        $invite          = $invitationModel->where([['email_invited', $request->input('email')], ['company_id', $company]])
                                                           ->first();
                        $user            = $userModel->where('email', $request->input('email'))->first();
                        $sendgridService = new EmailService();
                        if (empty($user)) {
                            $emailInvited = $sendgridService->sendInvite($request->input('email'), Hashids::encode($company));
                        } else {
                            return response()->json([
                                                        'message' => 'Já existe um usuário cadastrado com esse Email.',
                                                    ], 400);
                        }

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
                                        'status'        => $invitationModel->present()->getStatus('pending'),
                                        'company_id'    => current(Hashids::decode($request->input('company'))),
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
                    } catch (Throwable $e) {
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

    public function destroy($id)
    {
        try {
            $invitationModel = new Invitation();
            $invitationId    = current(Hashids::decode($id));
            if ($invitationId) {
                $invitation        = $invitationModel->find($invitationId);
                $invitationDeleted = $invitation->delete();
                if ($invitationDeleted) {
                    return response()->json([
                                                'message' => 'Convite excluído com sucesso',
                                            ], 200);
                } else {
                    return response()->json([
                                                'message' => 'Erro ao excluir convite',
                                            ], 400);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar excluir convite (InvitesApiController - destroy)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao excluir convite',
                                    ], 400);
        }
    }

    public function getInvitationData()
    {
        try {
            $invitationModel         = new Invitation();
            $transactionModel        = new Transaction();
            $invitationAcceptedCount = $invitationModel->where([
                                                                   ['invite', auth()->user()->id], [
                    'status', $invitationModel->present()->getStatus('accepted'),
                ],
                                                               ])->count();
            $invitationSentCount     = $invitationModel->where('invite', auth()->user()->id)->count();
            $userIdInvites           = $invitationModel->where('invite', auth()->user()->id)->pluck('id')->toArray();
            $commissionPaid          = $transactionModel->whereIn('invitation_id', $userIdInvites)
                                                        ->where('status', 'transfered')->sum('value');
            $commissionPending       = $transactionModel->whereIn('invitation_id', $userIdInvites)
                                                        ->where('status', 'paid')->sum('value');

            return response()->json([
                                        'message' => 'Dados dos convites retornados com sucesso',
                                        'data'    => [
                                            'invitation_accepted_count' => $invitationAcceptedCount,
                                            'invitation_sent_count'     => $invitationSentCount,
                                            'commission_paid'           => 'R$ ' . number_format(intval($commissionPaid) / 100, 2, ',', '.'),
                                            'commission_pending'        => 'R$ ' . number_format(intval($commissionPending) / 100, 2, ',', '.'),
                                            'invitations_available'     => auth()->user()->invites_amount - $invitationSentCount,
                                        ],
                                    ], 200);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar listar dados dos convites (InvitesApiController - getInvitationData)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao tentar listar dados dos convites ',
                                    ], 400);
        }
    }

    public function resendInvitation(Request $request)
    {
        try {
            $invitationModel = new Invitation();
            $userModel       = new User();
            $data            = $request->all();
            $invitationId    = current(Hashids::decode($data['invitationId']));
            if ($invitationId) {
                $sendgridService = new EmailService();
                $invitation      = $invitationModel->find($invitationId);
                if (FoxUtils::validateEmail($invitation->email_invited) && !empty($invitation->company_id)) {
                    $user = $userModel->where('email', $invitation->email_invited)->first();
                    if (!$user) {
                        $emailInvited = $sendgridService->sendInvite($invitation->email_invited, Hashids::encode($invitation->company_id));
                    } else {
                        return response()->json([
                                                    'message' => 'Já existe um usuário cadastrado com esse Email.',
                                                ], 400);
                    }
                    if ($emailInvited == 'error') {
                        return response()->json([
                                                    'message' => 'Erro ao tentar reenviar convite, tente novamente mais tarde.',
                                                ], 400);
                    } else {
                        if ($emailInvited->statusCode() != 202) {
                            return response()->json([
                                                        'message' => 'Erro ao tentar reenviar convite, tente novamente mais tarde.',
                                                    ], 400);
                        } else {
                            return response()->json([
                                                        'message' => 'Convite reenviado com sucesso',
                                                    ], 200);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar reenviar convite (InvitesApiController - resendInvitation)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao tentar reenviar convite',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @param $inviteId
     * @return JsonResponse
     */
    public function verifyInviteRegistration(Request $request, $inviteId)
    {
        try {
            $companyModel    = new Company();
            $invitationModel = new Invitation();

            $company = $companyModel->find(current(Hashids::decode($inviteId)));

            if (empty($company)) {
                return response()->json([
                                            'message' => 'Link convite inválido!',
                                            'data'    => 'invalido',
                                        ], 400);
            } else {
                $invitesSent = $invitationModel->where('invite', $company->user_id)->count();

                if ($invitesSent > $company->user->invites_amount) {
                    return response()->json([
                                                'message' => 'Convite indisponivel, limite atingido!',
                                                'data'    => 'invalido',
                                            ], 400);
                } else {
                    return response()->json([
                                                'message' => 'Convite valido!',
                                                'data'    => 'valido',
                                            ], 200);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao verificar convite (InviteApiController - VerifyInviteRegistration)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro com o link do convite, tente novamente mais tarde',
                                    ], 400);
        }
    }
}
