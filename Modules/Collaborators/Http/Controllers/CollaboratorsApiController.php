<?php

namespace Modules\Collaborators\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\User;
use Modules\Collaborators\Transformers\CollaboratorsResource;
use Modules\Collaborators\Http\Requests\StoreCollaboratorRequest;
use Modules\Collaborators\Http\Requests\UpdateCollaboratorRequest;

/**
 * Class CollaboratorsApiController
 * @package Modules\Collaborators\Http\Controllers
 */
class CollaboratorsApiController extends Controller
{
    /**
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index()
    {
        try {
            $userModel = new User();
            $user = $userModel->where(
                [
                    ['account_owner_id', auth()->user()->account_owner_id],
                    ['id', '!=', auth()->user()->account_owner_id],
                ]
            );

            activity()->on($userModel)->tap(
                function (Activity $activity) {
                    $activity->log_name = 'visualization';
                }
            )->log('Visualizou tela todos colaboradores');

            return CollaboratorsResource::collection($user->orderBy('id', 'ASC')->paginate(5));
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Erro ao tentar listar colaboradores ',
                ],
                400
            );
        }
    }

    /**
     * @param  StoreCollaboratorRequest  $request
     * @return JsonResponse
     */
    public function store(StoreCollaboratorRequest $request)
    {
        try {
            $data = $request->validated();
            $userModel = new User();
            $data['password'] = bcrypt($data['password']);
            $data['transaction_rate'] = '1.00';
            $data['balance'] = '0';
            $data['antecipation_tax'] = '0';
            $data['boleto_release_money_days'] = 0;
            $data['invites_amount'] = 1;
            $data['address_document_status'] = 3;
            $data['personal_document_status'] = 3;
            $data['account_owner_id'] = auth()->user()->account_owner_id;
            $user = $userModel->create($data);
            $user->assignRole($data['role']);

            if ($data['role'] == 'attendance' && !empty($data['refund_permission'])) {
                $user->givePermissionTo('refund');
            }
            if (!empty($user)) {
                return response()->json(
                    [
                        'message' => 'Calaborador cadastrado com sucesso!',
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'message' => 'Ocorreu um erro ao cadastrar colaborador',
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro ao cadastrar colaborador'], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse|CollaboratorsResource
     */
    public function show($id)
    {
        try {
            $userModel = new User();
            $userId = current(Hashids::decode($id));

            if (empty($userId)) {
                return response()->json(['message' => 'Ocorreu um erro ao buscar colaborador'], 400);
            }

            $user = $userModel->with('roles')->find($userId);

            activity()->on($userModel)->tap(
                function (Activity $activity) use ($userId) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = $userId;
                }
            )->log('Visualizou tela editar colaborador '.$user->name);

            if (Gate::denies('show', [$user])) {
                return response()->json(
                    [
                        'message' => 'Sem permissão',
                    ],
                    Response::HTTP_FORBIDDEN
                );
            }

            if (!empty($user)) {
                return new CollaboratorsResource($user);
            }

            return response()->json(['message' => 'Ocorreu um erro ao buscar colaborador'], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro ao buscar colaborador'], 400);
        }
    }

    /**
     * @param  UpdateCollaboratorRequest  $request
     * @param $id
     * @return JsonResponse
     */
    public function update(UpdateCollaboratorRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $userModel = new User();
            $userId = current(Hashids::decode($id));

            if (empty($userId)) {
                return response()->json(['message' => 'Ocorreu um erro ao atualizar colaborador'], 400);
            }

            $user = $userModel->find($userId);

            if (Gate::denies('update', [$user])) {
                return response()->json(['message' => 'Sem permissão'], Response::HTTP_FORBIDDEN);
            }

            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            } else {
                $data['password'] = $user->password;
            }

            $userFind = $userModel->where('email', $data['email'])->first();

            if ((!empty($userFind) && $userFind->id == $user->id) || empty($userFind)) {


                $userUpdated = $user->update($data);


                if ($user->hasPermissionTo('refund') && empty($data['refund_permission'])) {
                    $user->revokePermissionTo('refund');
                }
                if (!$user->hasPermissionTo('refund') && $data['role'] == 'attendance' && !empty($data['refund_permission'])) {
                    $user->givePermissionTo('refund');
                }
                $user->syncRoles([$data['role']]);

                if ($userUpdated) {
                    return response()->json(
                        [
                            'message' => 'Colaborador atualizado com sucesso!',
                        ],
                        200
                    );
                } else {
                    return response()->json(
                        [
                            'message' => 'Ocorreu um erro ao atualizar colaborador',
                        ],
                        400
                    );
                }
            } else {
                return response()->json(
                    [
                        'message' => 'Email informado ja esta sendo utilizado',
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro ao atualizar colaborador',
                ],
                400
            );
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $userModel = new User();
        $userId = current(Hashids::decode($id));

        if (empty($userId)) {
            return response()->json(['message' => 'Ocorreu um erro ao remover colaborador'], 400);
        }

        $user = $userModel->find($userId);

        if (Gate::denies('destroy', [$user]) && Gate::denies('update', [$user])) {
            return response()->json(['message' => 'Sem permissão'], Response::HTTP_FORBIDDEN);
        }

        $userUpdated = $user->update(['email' => uniqid() . $user->email]);
        $userDeleted = $user->delete();
        if ($userDeleted && $userUpdated) {
            return response()->json(['message' => 'Colaborador removido com sucesso!'], 200);
        } else {
            return response()->json(['message' => 'Ocorreu um erro ao remover colaborador'], 400);
        }
    }
}
