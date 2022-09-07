<?php

namespace Modules\Api\Http\Controllers\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Api\Http\Requests\V1\SubsellersApiRequest;
use Modules\Api\Http\Requests\V1\SubsellersDocumentsApiRequest;
use Modules\Api\Transformers\V1\SubsellersApiResource;
use Modules\Core\Services\Api\V1\SubsellersApiService;
use Modules\Core\Entities\User;
use Vinkla\Hashids\Facades\Hashids;

class SubsellersApiController extends Controller
{
    public function createSubsellers(SubsellersApiRequest $request)
    {
        try {
            $requestData = SubsellersApiService::prepareRequestData($request->all());

            $user = User::create($requestData);

            return response()->json([
                'message' => 'Usuário cadastrado com sucesso.',
                'data' => new SubsellersApiResource($user)
            ], 201);
        } catch(Exception $exception) {
            report($exception);

            return response()->json([
                'message' => 'Ocorreu um erro ao tentar cadastrar o usuário.'
            ], 500);
        }
    }

    public function showSubsellers($id)
    {
        try {
            $idDecode = current(Hashids::decode($id));

            $user = User::find($idDecode);

            return response()->json([
                'data' => new SubsellersApiResource($user),
            ], 200);
        } catch(Exception $exception) {
            report($exception);

            return response()->json([
                'message' => 'Ocorreu um erro ao tentar buscar o usuário.'
            ], 500);
        }
    }

    public function getSubsellers()
    {
        try {
            $subseller_owner_id = request()->user_id;

            $users = User::where('subseller_owner_id', $subseller_owner_id)->simplePaginate(10);

            return SubsellersApiResource::collection($users);
        } catch(Exception $exception) {
            report($exception);

            return response()->json([
                'message' => 'Ocorreu um erro ao tentar listar os usuários.'
            ], 500);
        }
    }

    public function updateSubsellers($id, Request $request)
    {
        try {
            $idDecode = current(Hashids::decode($id));

            $requestData = $request->all();

            User::find($idDecode)->update($requestData);

            $user = User::find($idDecode);

            return response()->json([
                'message' => 'Usuário atualizado com sucesso.',
                'data' => new SubsellersApiResource($user)
            ], 200);
        } catch(Exception $exception) {
            report($exception);

            return response()->json([
                'message' => 'Ocorreu um erro ao tentar atualizar o usuário.'
            ], 500);
        }
    }

    public function sendDocumentsSubsellers($id, SubsellersDocumentsApiRequest $request)
    {
        try {
            $requestData = $request->all();

            $document = SubsellersApiService::uploadDocuments($id, $requestData);

            return response()->json([
                'data' => $document
            ], 200);
        } catch(Exception $exception) {
            report($exception);

            return response()->json([
                'message' => ''
            ], 500);
        }
    }
}
