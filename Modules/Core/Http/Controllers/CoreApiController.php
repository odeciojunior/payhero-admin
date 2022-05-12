<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Companies\Http\Requests\CompanyCreateRequest;
use Modules\Companies\Http\Requests\CompanyUpdateRequest;
use Modules\Companies\Http\Requests\CompanyUploadDocumentRequest;
use Modules\Core\Transformers\CompaniesSelectResource;
use Modules\Companies\Transformers\CompanyCpfResource;
use Modules\Companies\Transformers\CompanyDocumentsResource;
use Modules\Core\Transformers\CompanyResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Entities\Project;
use Modules\Core\Services\AmazonFileService;
use Modules\Core\Services\BankService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\CompanyServiceGetnet;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\UserService;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class CoreApiController
 * @package Modules\Core\Http\Controllers
 */
class CoreApiController extends Controller
{


    public function verifyDocuments()
    {
        try {
            $companyService = new CompanyService();
            $userService = new UserService();

            $userDocumentRefused = $userService->haveAnyDocumentRefused();

            $link = null;
            $refused = false;
            $analyzing = false;
            $user = auth()->user();
            $accountType = ($user->id == $user->account_owner_id) ? 'owner' : 'collaborator';

            if ($userDocumentRefused) {
                $refused = true;
                $link = '/personal-info';
            } else {
                $companyDocumentRefused = $companyService->companyDocumentRefused();
                $companyDocumentApproved = $companyService->companyDocumentApproved();
                if (empty($companyDocumentApproved) && !empty($companyDocumentRefused)) {
                    $refused = true;
                    $companyCode = Hashids::encode($companyDocumentRefused->id);
                    if ($companyDocumentRefused->company_type == $companyDocumentRefused->present()->getCompanyType(
                            'physical person'
                        )
                    ) {
                        $link = "/personal-info";
                    } else {
                        $link = "/companies/company-detail/${companyCode}";
                    }
                } else {
                    $userValid = $userService->isDocumentValidated();
                    if (!$userValid) {
                        $analyzing = true;
                    } else {
                        if (!auth()->user()->account_is_approved) {
                            $analyzing = true;
                        }
                    }
                }
            }

            if (env('ACCOUNT_FRONT_URL') && empty($link)) {
                $link = env('ACCOUNT_FRONT_URL') . $link;
            }

            return response()->json(
                [
                    'message' => 'Documentos verificados!',
                    'analyzing' => $analyzing,
                    'refused' => $refused,
                    'link' => $link,
                    'accountType' => $accountType,
                    'accountStatus' => $user->present()->getStatus($user->status),
                ]
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(['error' => 'Erro ao verificar documentos'], 400);
        }
    }


    public function companies(Request $request)
    {
        try {
            $companyService = new CompanyService();

            $paginate = true;
            if ($request->has('select') && $request->input('select')) {
                $paginate = false;
            }

            return $companyService->getCompaniesUser($paginate);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente mais tarde',
                ],
                400
            );
        }
    }


    public function getCompanies()
    {
        try {
            $companyModel = new Company();
            $companies = $companyModel->newQuery()->where('user_id', auth()->user()->account_owner_id)
                ->orderBy('order_priority')->get();

            return CompaniesSelectResource::collection($companies);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro ao tentar buscar dados, tente novamente mais tarde',
                ],
                400
            );
        }
    }

    public function updateCompanyDefault(Request $request){

        if(empty($request->company_id)){
            return response()->json(['message'=>'Informe a empresa selecionada'],400);
        }

        $companyId = 0;
        if($request->company_id <> 'demo'){
            $companyId = current(Hashids::decode($request->company_id));            
        }
        
        $user = Auth::user();
        if($user->company_default == $companyId){
            return response()->json(['message'=>'A empresa selecionada já é a default.'],400);
        }

        if($request->company_id <> 'demo'){
            $company = Company::where('user_id',$user->id)->where('id',$companyId)->exists();
            if(empty($company)){
                return response()->json(['message'=>'Não foi possivel identificar a empresa'],400);
            }
        }

        try{

            $user->company_default = $companyId;
            $user->save();
    
            return response()->json(['message'=>'Empresa atualizada.']);

        }catch(Exception $e){
            report($e);
            return response()->json(['message'=>'Não foi possivel atualizar a empresa default.']);
        }
        
    }

}
