<?php

namespace Modules\Chargebacks\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Chargebacks\Transformers\ContestationResource;
use Modules\Chargebacks\Transformers\SaleContestationFileResource;
use Modules\Core\Entities\SaleContestation;
use Modules\Core\Entities\SaleContestationFile;
use Modules\Core\Services\ChargebackService;
use Modules\Core\Services\ContestationService;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class ContestationsApiController extends Controller
{
    public function show($id)
    {
        try {
            $contestationsId = current(Hashids::decode($id));
            $contestationsDetails = SaleContestation::with(["Sale"])->find($contestationsId);

            return new ContestationResource($contestationsDetails);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => __('messages.unexpected_error')], 400);
        }
    }

    public function getContestations(Request $request)
    {
        try {
            $contestationService = new ContestationService();
            $request->request->add(["from_contestation" => true]);
            $getnetChargebacks = $contestationService->getQuery($request->all());

            return ContestationResource::collection($getnetChargebacks->paginate(10));
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => __('messages.unexpected_error')], 400);
        }
    }

    public function getTotalValues(Request $request)
    {
        try {
            $request->request->add(["from_contestation" => true]);
            $requestValidated = $request->all();

            $contestationService = new ContestationService();
            $chargebackService = new ChargebackService();

            $totalContestationValue = $contestationService->getTotalValueContestations($requestValidated);
            $totalSalesApproved = $contestationService->getTotalApprovedSales($requestValidated);
            $totalContestations = $contestationService->getTotalContestations($requestValidated);
            $totalChargebackValue = $chargebackService->getTotalValueChargebacks($requestValidated);
            $totalSalesApprovedChargeback = $chargebackService->getTotalApprovedSales($requestValidated);
            $totalChargebacks = $chargebackService->getTotalChargebacks($requestValidated);

            $totalContestationsWon = $contestationService->getTotalWonContestations($requestValidated);

            return response()->json([
                "total_contestation" => $totalContestations,
                "total_contestation_value" => $totalContestationValue,
                "total_sale_approved" => $totalSalesApproved,
                "total_contestation_tax" => $contestationService->getChargebackTax(
                    $totalContestations,
                    $totalSalesApproved
                ),
                "total_chargeback" => $totalChargebacks,
                "total_chargeback_value" => $totalChargebackValue,
                "total_sale_approved_chargeback" => $totalSalesApprovedChargeback,
                "total_chargeback_tax" => $chargebackService->getChargebackTax($totalChargebacks, $totalContestations),
                "total_contestations_won" => $totalContestationsWon,
                "total_contestations_won_tax" => $contestationService->getWonContestationsTax(
                    $totalContestations,
                    $totalContestationsWon
                ),
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => __('messages.unexpected_error')], 400);
        }
    }

    public function updateIsFileCompleted(Request $request)
    {
        $data = $request->all();
        $data["contestation_id"] = current(Hashids::decode($data["contestation_id"]));
        if (empty($data["contestation_id"])) {
            return response()->json(
                [
                    "message" => __('messages.unexpected_error'),
                ],
                400
            );
        }

        $contestation = SaleContestation::find($data["contestation_id"]);

        if (empty($contestation)) {
            return response()->json(
                [
                    "message" => __('messages.unexpected_error'),
                ],
                400
            );
        }

        $contestation->file_user_completed = $data["file_is_completed"] == 1;
        $contestation->save();

        $contestationModel = new SaleContestation();
        activity()
            ->on($contestationModel)
            ->tap(function (Activity $activity) use ($contestation) {
                $activity->log_name = "updated";
                $activity->subject_id = $contestation->id;
            })
            ->log("Atualizou o contestado " . \Auth::user()->name);

        return response()->json(
            [
                "message" => "Atualizado com sucesso!",
            ],
            200
        );
    }

    public function getContestationFiles($salecontestation)
    {
        try {
            $saleContestation = SaleContestation::find(current(Hashids::decode($salecontestation)));
            return SaleContestationFileResource::collection($saleContestation->files);
        } catch (\stringEncode\Exception $e) {
            report($e);
        }
    }

    public function sendContestationFiles(Request $request)
    {
        try {
            $files = $request->allFiles();
            $data = $request->all();

            $validator = \Validator::make(
                $data,
                [
                    "files" => "required|array",
                    "files.*" => "required|mimes:jpg,jpeg,png,bmp|max:12000",
                ],
                [
                    "files.*.required" => "Arquivo obrigatório",
                    "files.*.mimes" => "Apenas arquivo de imagens",
                    "files.*.max" => "Desculpe! Máximo permitido é 12MB",
                ]
            );

            if ($validator->fails()) {
                return response()->json(
                    [
                        "message" => "Error no envio do arquivo",
                        "errors" => $validator->getMessageBag()->toArray(),
                    ],
                    403
                );
            }

            $contestationService = new ContestationService();
            $files_paths = $contestationService->sendContestationFiles($files);
            $saleContestation = SaleContestation::find(current(Hashids::decode($data["contestation"])));

            foreach ($files_paths as $file) {
                $contestationfile = new SaleContestationFile();
                $contestationfile->contestation_sale_id = $saleContestation->id;
                $contestationfile->user_id = \Auth::id();
                $contestationfile->type = $data["type"];
                $contestationfile->file = $file;
                $contestationfile->save();
            }

            return SaleContestationFileResource::collection($saleContestation->files);
        } catch (\stringEncode\Exception $e) {
            report($e);
            return response()->json(["message" => "Erro"], 400);
        }
    }

    public function removeContestationFiles($contestationfile)
    {
        try {
            $saleContestation = SaleContestationFile::find(current(Hashids::decode($contestationfile)));

            if ($saleContestation) {
                $contestationService = new ContestationService();
                $contestationService->removeFile($saleContestation);
            }

            return [];
        } catch (\stringEncode\Exception $e) {
            report($e);
            return response()->json(["message" => "Erro"], 400);
        }
    }

    public function getProjectsWithContestations(){
        $projects = ContestationService::getProjectsWithContestations();
        $projectsEncoded=[];
        foreach($projects as $item){
            $projectsEncoded[]= ($item->prefix??'').Hashids::encode($item->project_id);
        }
        return $projectsEncoded;
    }
}
