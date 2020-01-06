<?php

namespace Modules\Mobile\Http\Controllers\Apis\v10;

use App\Jobs\SendNotazzInvoiceJob;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Notification;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\PushNotification;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\UserProject;
use Modules\Notifications\Transformers\NotificationResource;
use Vinkla\Hashids\Facades\Hashids;
use App\Jobs\PushNotificationJob;

/**
 * Class NotificationApiService
 * @package Modules\Mobile\Http\Controllers\Apis\v10
 */
class NotificationApiService
{
    /**
     * NotificationApiService constructor.
     */
    public function __construct() { }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadNotifications()
    {
        try {
            $notifications = auth()->user()->unreadNotifications;

            if (count($notifications) < 10) {
                $notificationsRead = auth()->user()->readNotifications()->take(10 - count($notifications))
                                           ->orderBy('created_at', 'desc')->get();
                foreach ($notificationsRead as $notificationRead) {
                    $notifications->push($notificationRead);
                }
            }
            $notificationCollection = NotificationResource::collection($notifications);

            return response()->json(compact('notificationCollection'), 200);
        } catch (Exception $e) {
            Log::warning('Erro ao obter notificações');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao carregar as notificações - NotificationApiService - getUnreadNotifications',
                                    ], 400);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPushNotifications()
    {
        try {
            $notifications = PushNotification::where('user_id', auth()->user()->id)->get();
            $return        = [];

            foreach ($notifications as $notification) {

                $notification_text = $this->notificationCreateText($notification);

                /*NOTIFICATION TYPE
                    1 -> BOLETO PAGO
                    2 -> BOLETO GERADO
                    3 -> VENDA POR CARTÃO
                */

                $retrun_object = [
                    'user_id'              => $notification['user_id'] ?? '',
                    'sale_id'              => $notification['sale_id'] ?? '',
                    'transaction_id'       => $notification['transaction_id'] ?? '',
                    'notification_company' => $notification_text['notification_company'],
                    'notification_header'  => $notification_text['notification_header'],
                    'notification_content' => $notification_text['notification_body'],
                    'notification_type'    => $notification_text['notification_type'] ?? '',
                    'notification_date'    => $notification['created_at']->format('d/m/Y'),
                    'notification_hour'    => $notification['created_at']->format('H:i:s'),
                ];
                array_push($return, $retrun_object);
            }

            return response()->json($return, 200);
        } catch (Exception $e) {
            Log::warning('Erro ao obter push notificações');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao carregar as notificações - NotificationApiService - getPushNotifications',
                                    ], 400);
        }
    }

    /**
     * @param $params
     * @return array
     */
    public function notificationCreateText($params)
    {
        $notificationReturn = [
            'notification_company' => '',
            'notification_header'  => '',
            'notification_body'    => '',
            'notification_type'    => '',
        ];

        if (json_decode($params['postback_data'])->notification_type == 'sale') {

            $sale         = Sale::where('id', $params['sale_id'])->first();
            $user_project = UserProject::where('project_id', $sale->project_id)->first();
            $companie     = Company::where('id', $user_project->company_id)->first();

            if ($sale->payment_method == '2') {

                if ($sale->status == '1') {
                    $notificationReturn = [
                        'notification_company' => $companie->fantasy_name,
                        'notification_header'  => 'Você acaba de realizar uma venda',
                        'notification_body'    => 'Um boleto acabou de ser pago',
                        'notification_type'    => '1',
                    ];
                } else {
                    $notificationReturn = [
                        'notification_company' => $companie->fantasy_name,
                        'notification_header'  => 'Você acaba de realizar uma venda',
                        'notification_body'    => 'Foi realizada uma venda por boleto',
                        'notification_type'    => '2',
                    ];
                }
            } else {
                $notificationReturn = [
                    'notification_company' => $companie->fantasy_name,
                    'notification_header'  => 'Você acaba de realizar uma venda',
                    'notification_body'    => 'Foi realizada uma venda por cartão',
                    'notification_type'    => '3',
                ];
            }
        }

        return $notificationReturn;
    }

    /**
     * @param Request $request
     * @return bool|string
     */
    public function sendMessage(Request $request)
    {
        $notificationChannel = [
            'nil'              => 'nil',
            'venda'            => '2a478392-e1b7-4e35-a80c-3942c893c20f',
            'boleto'           => '532e65cb-a690-4438-ab8e-8739d82eb4da',
            'notificacao'      => '803f7e0a-687b-40d5-9cd2-2972bc94fc19',
            'pedido_afiliacao' => '6cef3efa-2acf-43eb-b9d8-b5f90dcb1143',
        ];

        $headings = [
            "en" => $request['headings'],
        ];
        $content  = [
            "en" => $request['content'],
        ];
        $fields   = [
            'app_id'             => env('ONESIGNAL_APP_ID'),
            'android_channel_id' => $notificationChannel[$request['notification_sound']],
            'included_segments'  => $request['included_segments'],
            'contents'           => $content,
            'headings'           => $headings,
            'ios_sound'          => $request['notification_sound'] . '.wav',
        ];

        //        return $fields['included_segments'];

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . env('ONESIGNAL_REST_API_KEY'),
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function sendNotification($params)
    {
        try {

            $notificationChannel = [
                'nil'              => 'nil',
                'venda'            => '2a478392-e1b7-4e35-a80c-3942c893c20f',
                'boleto'           => '532e65cb-a690-4438-ab8e-8739d82eb4da',
                'notificacao'      => '803f7e0a-687b-40d5-9cd2-2972bc94fc19',
                'pedido_afiliacao' => '6cef3efa-2acf-43eb-b9d8-b5f90dcb1143',
            ];

            $headings = [
                "en" => $params['headings'],
            ];

            $content  = [
                "en" => $params['content'],
            ];

            $fields   = [
                'app_id'             => env('ONESIGNAL_APP_ID'),
                'include_player_ids' => $params['include_player_ids'],
                'contents'           => $content,
                'headings'           => $headings,
            ];

            if ($params['device_type'] == 0) {
                $fields[] = [
                    'ios_sound' => $params['notification_sound'] . '.wav'
                ];
            } else {
                $fields[] = [
                    'android_channel_id' => $notificationChannel[$params['notification_sound']]
                ];
            }

            $fields = json_encode($fields);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic ' . env('ONESIGNAL_REST_API_KEY'),
            ]);

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

            $response    = curl_exec($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $return = [
                'response' => $response,
                'status'   => $http_status,
            ];

            return $return;
        } catch (Exception $ex) {
            $this->exceptions[] = $ex->getMessage();
            throw $ex;
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function processPostback(Request $request)
    {
        try {
            $saleModel = new Sale();

            $saleId = current(Hashids::connection('sale_id')->decode($request->external_reference));

            if ($saleId) {
                //hash ok
                $sale             = $saleModel->find($saleId);
                $pushNotification = PushNotification::create([
                                                                 'sale_id'       => $saleId,
                                                                 'user_id'       => $sale->owner_id,
                                                                 'postback_data' => $request->getContent(),
                                                             ]);

                PushNotificationJob::dispatch($pushNotification);

                return response()->json('success', 200);
            } else {
                //hash wrong
                return response()->json('error', 400);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
