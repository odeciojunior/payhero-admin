<?php

namespace Modules\PostBack\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\NotazzInvoice;
use Modules\Core\Entities\PostbackLog;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class PostBackNotazzController
 * @package Modules\PostBack\Http\Controllers
 */
class PostBackNotazzController extends Controller
{
    /**
     * @param Request $request
     */
    public function postBackListener(Request $request)
    {

        try {

            $postBackLogModel   = new PostbackLog();
            $notazzInvoiceModel = new NotazzInvoice();

            $requestData = $request->all();

            $postBackLogModel->create([
                                          'origin'      => $postBackLogModel->present()->getOrigin('notazz'),
                                          'data'        => json_encode($requestData),
                                          'description' => 'notazz',
                                      ]);

            if (!empty($requestData["external_id"])) {
                //hash ok
                $externalId    = preg_replace("/[^0-9]/", "", $requestData["external_id"]);
                $notazzInvoice = $notazzInvoiceModel->find($externalId);

                if ($notazzInvoice) {
                    switch ($requestData["statusNota"]) {
                        case 'Autorizada':
                            if (in_array($notazzInvoice->status, [1, 2])) {

                                $notazzInvoice->update([
                                                           'xml'            => $requestData["xml"],
                                                           'pdf'            => $requestData["pdf"],
                                                           'status'         => $notazzInvoiceModel->present()
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
                                                           'xml'           => $requestData["xml"],
                                                           'pdf'           => $requestData["pdf"],
                                                           'status'        => $notazzInvoiceModel->present()
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
                                                           'status'           => $notazzInvoiceModel->present()
                                                                                                    ->getStatus('rejected'),
                                                           'date_rejected'    => Carbon::now()->toDateTime(),
                                                           'postback_message' => $requestData['motivoStatus'],
                                                       ]);
                            }

                            return response()->json([
                                                        'message' => 'sucesso',
                                                    ], 200);
                            break;
                        default:
                    }
                } else {
                    //nota nao localizada
                    return response()->json([
                                                'message' => 'Invoice não encontrada',
                                            ], 400);
                }
            } else {
                //wrong hash
                return response()->json([
                                            'message' => 'Invoice não encontrada',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('SendNotazzInvoiceJob - Erro no job ');
            report($e);

            return response()->json([
                                        'message' => 'Invoice não encontrada',
                                    ], 400);
        }
    }
}
