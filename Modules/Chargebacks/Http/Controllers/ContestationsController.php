<?php

namespace Modules\Chargebacks\Http\Controllers;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Chargebacks\Transformers\ContestationResource;
use Modules\Chargebacks\Transformers\SaleContestationFileResource;
use Modules\Core\Entities\SaleContestation;
use Modules\Core\Services\ChargebackService;
use Modules\Core\Services\ContestationService;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

//use Modules\Core\Entities\GetnetChargeback;

class ContestationsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Factory|RedirectResponse|View
     */
    public function index()
    {
        return view('chargebacks::contestations-index');
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function getContestations(Request $request)
    {
        try {

            $contestationService = new ContestationService();
            $request->request->add(['from_contestation' => true]);
            $getnetChargebacks = $contestationService->getQuery($request->all());

            return ContestationResource::collection($getnetChargebacks->paginate(10));

        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    public function getTotalValues(Request $request)
    {
        try {
            $request->request->add(['from_contestation' => true]);
            $requestValidated = $request->all();

            $contestationService = new ContestationService();
            $chargebackService = new ChargebackService();

            $totalContestationValue = $contestationService->getTotalValueChargebacks($requestValidated);
            $totalSalesApproved = $contestationService->getTotalApprovedSales($requestValidated);
            $totalContestations = $contestationService->getTotalChargebacks($requestValidated);

            $totalChargebackValue = $chargebackService->getTotalValueChargebacks($requestValidated);
            $totalSalesApprovedChargeback = $chargebackService->getTotalApprovedSales($requestValidated);
            $totalChargebacks = $chargebackService->getTotalChargebacks($requestValidated);

            return response()->json([
                'total_contestation' => $totalContestations,
                'total_contestation_value' => $totalContestationValue,
                'total_sale_approved' => $totalSalesApproved,
                'total_contestation_tax' => $contestationService->getChargebackTax($totalContestations, $totalSalesApproved),

                'total_chargeback'           => $totalChargebacks,
                'total_chargeback_value'     => $totalChargebackValue,
                'total_sale_approved_chargeback'  => $totalSalesApprovedChargeback,
                'total_chargeback_tax'       => $chargebackService->getChargebackTax($totalChargebacks, $totalContestations)

            ]);

        } catch (Exception $e) {
            dd($e->getMessage());
            report($e);
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }


    /**
     * @param $id
     * @return Factory|View
     * @throws PresenterException
     */
    public function show($id)
    {

        try {
            $contestationsId = current(Hashids::decode($id));
            $contestationsDetails = SaleContestation::with(
                [
                    'Sale'
                ]
            )->find($contestationsId);

            return view('chargebacks::contestations-show', ['contestationsDetails' => $contestationsDetails]);


        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }

    }

    public function getObservation(Request $request, $id)
    {
        try {
            if (!empty($id)) {
                $id = current(Hashids::decode($id));
                $contestationModel = new SaleContestation();
                $contestation = $contestationModel->find($id);

                return new ContestationResource($contestation);
            } else {
                return false;
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function setValueObservation(Request $request, $id)
    {
        try {
            if (!empty($id)) {
                $contestationModel = new SaleContestation();
                $contestation = $contestationModel->find(current(Hashids::decode($id)));
                $contestation->update(
                    [
                        'observation' => $request->input('observation'),
                    ]
                );
                activity()->on($contestationModel)->tap(
                    function (Activity $activity) use ($contestation) {
                        $activity->log_name = 'updated';
                        $activity->subject_id = $contestation->id;
                    }
                )->log('Adicionou observação ao usuario ' . \Auth::user()->name);
                return response()->json(['message' => 'Observaçao atualizada com sucesso!'], 200);
            } else {
                return response()->json(['message' => 'Erro ao atualizar observaçao!'], 400);
            }
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao atualizar observaçao!'], 400);
        }
    }

    public function updateIsContested(Request $request)
    {
        $data = $request->all();

        $data['contestation_id'] = current(Hashids::decode($data['contestation_id']));

        if (empty($data['contestation_id'])) {
            return response()->json(
                [
                    'message' => 'Ocorreu algum erro',
                ],
                400
            );
        }

        $contestation = SaleContestation::find($data['contestation_id']);

        if (empty($contestation)) {
            return response()->json(
                [
                    'message' => 'Ocorreu algum erro',
                ],
                400
            );
        }

        $contestation->update(
            [
                'is_contested' => $data['is_contested'],
            ]
        );

        $contestationModel = new SaleContestation();
        activity()->on($contestationModel)->tap(
            function (Activity $activity) use ($contestation) {
                $activity->log_name = 'updated';
                $activity->subject_id = $contestation->id;
            }
        )->log('Atualizou o contestado ' . \Auth::user()->name);

        return response()->json(
            [
                'message' => 'Atualizado com sucesso!',
            ],
            200
        );
    }


    public function generateDispute($salecontestation_id)
    {

        try {
            $contestationService = new ContestationService();
            $saleContestation = SaleContestation::find(current(Hashids::decode($salecontestation_id)));
            $data = $contestationService->generateDispute($saleContestation);

            $header = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$data['file_name'].'"'
            ];

            return response()->make(file_get_contents($data['file_path']), 200, $header);



        } catch (\stringEncode\Exception $e) {
            report($e);
        }

        return response()->json(['message' => 'Erro ao gerar o pdf!'], 400);
    }

    public function sendContestation(Request $request)
    {

        try {

            $validator = \Validator::make($request->all(), [
                'file_contestation' => 'required|file',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => 'Erro ao gerar ao enviar'], 400);
            }

            $this->sendGmail($request);

            return response()->json(
                [
                    'message' => 'Enviado com sucesso',
                ],
                200
            );

        } catch (Exception $e) {
            report($e);
            dd($e->getMessage());
        }

    }

    private function sendGmail($request):void
    {

        $files = $request->file('file_contestation');
        $filename = str_replace(".pdf", "", $files->getClientOriginalName());

        Mail::raw('Segue em anexo o documento para defesa do chargeback: ' .$filename , function ($message) use ($request, $filename) {
            $message
                ->subject($filename)
                ->from("chargebackgetnet@gmail.com", 'Getnet Chargebacks')
                ->to("marcosmarion@cloudfox.net")
                ->attach($request->file('file_contestation')->getRealPath(), [
                    'as' => $request->file('file_contestation')->getClientOriginalName(),
                    'mime' => $request->file('file_contestation')->getMimeType()
                ])
               ;
        });
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


}
