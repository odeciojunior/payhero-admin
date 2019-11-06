<?php

namespace Modules\Collaborators\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Collaborators\Http\Requests\UpdateCollaboratorRequest;
use Modules\Collaborators\Transformers\CollaboratorsResource;
use Modules\Core\Entities\User;
use Modules\Collaborators\Http\Requests\StoreCollaboratorRequest;
use Vinkla\Hashids\Facades\Hashids;

class CollaboratorsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        try {
            $userModel = new User();
            $user      = $userModel->where([['account_owner', auth()->user()->account_owner], ['id', '!=', auth()->user()->account_owner]]);

            return CollaboratorsResource::collection($user->orderBy('id', 'ASC')->paginate(5));
        } catch (Exception $e) {
            Log::warning('Erro ao tentar listar colaboradores (CollaboratorsApiController - index)');
            report($e);

            return response()->json(
                [
                    'message' => 'Erro ao tentar listar colaboradores ',
                ], 400
            );
        }
    }

    /**
     * @param StoreCollaboratorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCollaboratorRequest $request)
    {
        try {
            $data                                        = $request->validated();
            $userModel                                   = new User();
            $data['password']                            = bcrypt($data['password']);
            $data['percentage_rate']                     = '5.9';
            $data['transaction_rate']                    = '1.00';
            $data['balance']                             = '0';
            $data['foxcoin']                             = '0';
            $data['credit_card_antecipation_money_days'] = '30';
            $data['release_money_days']                  = '30';
            $data['boleto_antecipation_money_days']      = '2';
            $data['antecipation_tax']                    = '0';
            $data['percentage_antecipable']              = '100';
            $data['email_amount']                        = '0';
            $data['call_amount']                         = '0';
            $data['score']                               = '0';
            $data['sms_zenvia_amount']                   = '0';
            $data['invites_amount']                      = 1;
            $data['account_owner']                       = auth()->user()->account_owner;
            $user                                        = $userModel->create($data);
            $user->assignRole($data['role']);
            if (!empty($user)) {
                return response()->json([
                                            'message' => 'Calaborador cadastrado com sucesso!',
                                        ], 200);
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro ao cadastrar colaborador',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao cadastrar colaborador  CollaboratorsApiController - store');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao cadastrar colaborador',
                                    ], 400);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $userModel = new User();
            $userId    = current(Hashids::decode($id));

            if ($userId) {
                $user = $userModel->with('roles')->find($userId);
                if (!empty($user)) {
                    return new CollaboratorsResource($user);
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro ao buscar colaborador',
                                            ], 400);
                }
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro ao buscar colaborador',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar colaboradorer (CollaboratorsApiController - show)');
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro ao buscar colaborador',
                ], 400
            );
        }
    }

    /**
     * @param UpdateCollaboratorRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCollaboratorRequest $request, $id)
    {
        try {
            $data      = $request->validated();
            $userModel = new User();
            $userId    = current(Hashids::decode($id));
            if ($userId) {
                $user = $userModel->find($userId);
                if (!empty($data['password'])) {
                    $data['password'] = bcrypt($data['password']);
                }
                $userUpdated = $user->update($data);
                $user->syncRoles([$data['role']]);
                if ($userUpdated) {
                    return response()->json([
                                                'message' => 'Colaborador atualizado com sucesso!',
                                            ], 200);
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro ao atualizar colaborador',
                                            ], 400);
                }
            } else {

                return response()->json([
                                            'message' => 'Ocorreu um erro ao atualizar colaborador',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao atualizar colaborador  CollaboratorsApiController - update');
            report($e);
            dd($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao atualizar colaborador',
                                    ], 400);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $userModel = new User();
        $userId    = current(Hashids::decode($id));
        if ($userId) {
            $user        = $userModel->find($userId);
            $userDeleted = $user->delete();
            if ($userDeleted) {
                return response()->json([
                                            'message' => 'Colaborador removido com sucesso!',
                                        ], 200);
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro ao remover colaborador',
                                        ], 400);
            }
        } else {
            return response()->json([
                                        'message' => 'Ocorreu um erro ao remover colaborador',
                                    ], 400);
        }
    }
}
