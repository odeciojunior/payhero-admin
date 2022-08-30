<?php

namespace Modules\Api\Http\Controllers\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Api\Http\Requests\V1\SubsellersApiRequest;
use Modules\Api\Transformers\V1\SubsellerApiResource;
use Modules\Core\Services\Api\V1\SubsellersApiService;
use Modules\Core\Entities\User;

class SubsellersApiController extends Controller
{
    private SubsellersApiService $subsellersApiService;

    public function __construct(SubsellersApiService $subsellersApiService)
    {
        $this->subsellersApiService = $subsellersApiService;
    }

    public function createSubseller(SubsellersApiRequest $request)
    {
        try {
            $requestData = $this->subsellersApiService->prepareRequestData();

            $user = User::create($requestData);

            return response()->json([
                'data' => new SubsellerApiResource($user),
                'message' => 'Usuário cadastrado com sucesso.'
            ], 201);
        } catch(Exception $exception) {
            report($exception);

            return response()->json([
                'message' => 'Ocorreu um erro ao tentar cadastrar o usuário.'
            ], 500);
        }
    }

    public function showSubseller($id)
    {
        try {
            return response()->json([
                'data' => ''
            ], 200);
        } catch(Exception $exception) {
            report($exception);

            return response()->json([
                'message' => ''
            ], 500);
        }
    }

    public function updateSubseller($id)
    {
        try {
            return response()->json([
                'data' => ''
            ], 200);
        } catch(Exception $exception) {
            report($exception);

            return response()->json([
                'message' => ''
            ], 500);
        }
    }

    public function sendDocumentsSubseller($id, Request $request)
    {
        try {
            return response()->json([
                'data' => ''
            ], 200);
        } catch(Exception $exception) {
            report($exception);

            return response()->json([
                'message' => ''
            ], 500);
        }
    }
}
