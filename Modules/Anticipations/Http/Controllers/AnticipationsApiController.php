<?php

namespace Modules\Anticipations\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\AnticipatedTransaction;
use Modules\Core\Entities\Anticipation;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\AnticipationService;
use Vinkla\Hashids\Facades\Hashids;

class AnticipationsApiController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $companyModel        = new Company();
            $company             = $companyModel->find(current(Hashids::decode($request->input('company_id'))));
            $anticipationService = new AnticipationService();

            if(!$company->user->antecipation_enabled_flag) {
                return response()->json([
                    'data' => [
                                'message' => 'Antecipação não está disponível'
                              ]
                ], 200);
            }

            $anticipationResult = $anticipationService->performAnticipation($company);

            return response()->json([
                'data' => [
                            'message' => $anticipationResult['message']
                          ]
                ], 200);

        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'data' => [
                            'message' => 'Ocorreu algum erro'
                          ]
                ], 400);
        }
    }

    /**
     * @param Request $request
     * @param $company
     * @return JsonResponse
     */
    public function show($companyId)
    {

        try {
            $companyId = current(Hashids::decode($companyId));

            $anticipationService = new AnticipationService();

            return response()->json([
                                        'message' => 'success',
                                        'data'    => $anticipationService->getAntecipationData(Company::find($companyId))
                                    ], 200);

        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'error',
            ], 400);
        }
    }

}

