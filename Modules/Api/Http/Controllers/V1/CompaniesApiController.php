<?php

namespace Modules\Api\Http\Controllers\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Api\Http\Requests\CompaniesApiRequest;
use Modules\Api\Transformers\V1\CompaniesApiResource;
use Modules\Core\Entities\Company;
use Modules\Core\Services\Api\V1\CompaniesApiService;
use Vinkla\Hashids\Facades\Hashids;

class CompaniesApiController extends Controller
{
    public function createCompanies(CompaniesApiRequest $request)
    {
        try {
            $requestData = CompaniesApiService::prepareRequestData($request->all());

            $user = Company::create($requestData);

            return response()->json([
                'message' => 'Empresa cadastrada com sucesso.',
                'data' => new CompaniesApiResource($user)
            ], 201);
        } catch(Exception $exception) {
            report($exception);

            return response()->json([
                'message' => 'Ocorreu um erro ao tentar cadastrar a empresa.'
            ], 500);
        }
    }

    public function showCompanies($id)
    {
        try {
            $idDecode = current(Hashids::decode($id));
            $subseller_owner_id = request()->user_id;

            $company = Company::where('id', $idDecode)->where('user_id', $subseller_owner_id);

            return response()->json([
                'data' => new CompaniesApiResource($company),
            ], 200);
        } catch(Exception $exception) {
            report($exception);

            return response()->json([
                'message' => 'Ocorreu um erro ao tentar buscar a empresa.'
            ], 500);
        }
    }

    public function listCompanies()
    {
        try {
            $subseller_owner_id = request()->user_id;

            $company = Company::where('user_id', $subseller_owner_id)->simplePaginate(10);

            return CompaniesApiResource::collection($company);
        } catch(Exception $exception) {
            report($exception);

            return response()->json([
                'message' => 'Ocorreu um erro ao tentar listar as empresas.'
            ], 500);
        }
    }

    public function updateCompanies($id, Request $request)
    {
        try {
            $idDecode = current(Hashids::decode($id));

            $requestData = $request->all();

            Company::find($idDecode)->update($requestData);

            $company = Company::find($idDecode);

            return response()->json([
                'message' => 'Empresa atualizada com sucesso.',
                'data' => new CompaniesApiResource($company)
            ], 200);
        } catch(Exception $exception) {
            report($exception);

            return response()->json([
                'message' => 'Ocorreu um erro ao tentar atualizar a empresa.'
            ], 500);
        }
    }

    public function sendDocumentsCompanies($id, Request $request)
    {
        try {
            $requestData = $request->all();

            $document = CompaniesApiService::uploadDocuments($id, $requestData);

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
