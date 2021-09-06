<?php

namespace Modules\PostBack\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\NotazzInvoice;
use Modules\Core\Entities\PostbackLog;

/**
 * Class PostBackNotazzController
 * @package Modules\PostBack\Http\Controllers
 */
class PostBackNotazzController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postBackListener(Request $request)
    {
        try {
            $postBackLogModel = new PostbackLog();
            $notazzInvoiceModel = new NotazzInvoice();

            $requestData = $request->all();

            $dataJson = json_encode($requestData);

            if (strlen($dataJson) > 5) {
                $postBackLogModel->create([
                    'origin' => $postBackLogModel->present()->getOrigin('notazz'),
                    'data' => $dataJson,
                    'description' => 'notazz',
                ]);
            }

            if (empty($requestData["external_id"])) {
                return response()->json([
                    'message' => 'Invoice não encontrada',
                ], 400);
            }

            if(str_contains($requestData["external_id"],'cloudfox')){
                $externalId = preg_replace("/[^0-9]/", "", $requestData["external_id"]);
                $notazzInvoice = $notazzInvoiceModel->find($externalId);
                if (empty($notazzInvoice)) {
                    return response()->json([
                        'message' => 'Invoice não encontrada',
                    ], 400);
                }
            }else{
                return response()->json([
                    'message' => 'Invoice inválida',
                ], 400);
            }


            /**
             * webhook referente ao evento do rastreio disponivel, apenas ignoramos.
             */
            if (empty($requestData["statusNota"])) {
                return response()->json([
                    'message' => 'sucesso',
                ], 200);
            }

            /**
             * webhook referente ao evento de nota autorizada
             */
            switch ($requestData["statusNota"]) {
                case 'Autorizada':
                    if (in_array($notazzInvoice->status, [1, 2])) {
                        $notazzInvoice->update([
                            'xml' => $requestData["xml"],
                            'pdf' => $requestData["pdf"],
                            'status' => $notazzInvoiceModel->present()
                                ->getStatus('completed'),
                            'date_completed' => Carbon::now()->toDateTime(),
                        ]);
                    }

                    return response()->json([
                        'message' => 'sucesso',
                    ], 200);

                    break;
                case 'Cancelada':
                    if (in_array($notazzInvoice->status, [3])) {
                        $notazzInvoice->update([
                            'xml' => $requestData["xml"],
                            'pdf' => $requestData["pdf"],
                            'status' => $notazzInvoiceModel->present()
                                ->getStatus('canceled'),
                            'date_canceled' => Carbon::now()->toDateTime(),
                        ]);
                    }

                    return response()->json([
                        'message' => 'sucesso',
                    ], 200);
                    break;
                case 'Rejeitada':
                    if (in_array($notazzInvoice->status, [1, 2])) {
                        $notazzInvoice->update([
                            'status' => $notazzInvoiceModel->present()
                                ->getStatus('rejected'),
                            'date_rejected' => Carbon::now()->toDateTime(),
                            'postback_message' => $requestData['motivoStatus'],
                        ]);
                    }

                    return response()->json([
                        'message' => 'sucesso',
                    ], 200);
                    break;
                default:
            }
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Invoice não encontrada',
            ], 400);
        }
    }
}
