<?php

namespace Modules\Mobile\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use stringEncode\Exception;

/**
 * Class MobileController
 * @package Modules\Mobile\Http\Controllers
 */
class MobileController extends Controller
{
    const lastVersion = 'v10';
    /**
     * @var IntegrationService
     */
    private $integrationApiService;

    /**
     * MobileController constructor.
     * @param Request $request
     * @throws Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(Request $request)
    {
        $version = $request->route('version');

        if ($version) {
            $this->getMobileApiService($version);
        } else {
            $this->getMobileApiService(self::lastVersion);
        }
    }

    /**
     * @param $version
     * @return mixed|IntegrationService
     * @throws Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getMobileApiService($version)
    {
        if (!$this->integrationApiService) {
            switch ($version) {
                case 'v10':
                    $this->integrationApiService = app()->make("Modules\Mobile\Http\Controllers\Apis\\" . $version . "\IntegrationApiService");
                    break;
                default:
                    throw new Exception('Versão inválida.');
                    break;
            }
        }

        return $this->integrationApiService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $dataRequest = $request->json()->all();
            $validator   = Validator::make($dataRequest, [
                'email'             => 'required|string|email',
                'password'          => 'required|string',
                'mobile_push_token' => 'sometimes|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                                            'error' => 'Invalid Data',
                                        ], 400);
            }

            return $this->integrationApiService->login($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboardGetData(Request $request)
    {
        try {
            return $this->integrationApiService->dashboardGetData($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function financeGetData(Request $request)
    {
        try {
            return $this->integrationApiService->financeGetData($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function financeWithdraw(Request $request)
    {
        try {
            return $this->integrationApiService->financeWithdraw($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function financeAccountInformation(Request $request)
    {
        try {
            return $this->integrationApiService->financeAccountInformation($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileGetData(Request $request)
    {
        try {
            return $this->integrationApiService->profileGetData($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileChangePassword(Request $request)
    {
        try {
            return $this->integrationApiService->profileChangePassword($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileUpdateNotification(Request $request)
    {
        try {
            return $this->integrationApiService->profileUpdateNotification($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function salesByFilter(Request $request)
    {
        try {
            return $this->integrationApiService->salesByFilter($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSaleDetails(Request $request)
    {
        try {
            return $this->integrationApiService->getSaleDetails($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function notificationGetUnread(Request $request)
    {
        try {
            return $this->integrationApiService->notificationGetUnread($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserProjects(Request $request)
    {
        try {
            return $this->integrationApiService->getUserProjects($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        try {
            return $this->integrationApiService->sendMessage($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            return $this->integrationApiService->logout($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutDevice(Request $request)
    {
        try {
            return $this->integrationApiService->logoutDevice($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotification(Request $request) {
        try {
            return $this->integrationApiService->sendNotification($request);

        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPushNotifications(Request $request) {
        try {
            return $this->integrationApiService->getPushNotifications($request);

        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                'message' => __('definitions.message.search.error'),
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDeviceData(Request $request)
    {
        try {
            return $this->integrationApiService->getDeviceData($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNotificationPermission(Request $request)
    {
        try {
            return $this->integrationApiService->updateNotificationPermission($request);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => __('definitions.message.search.error'),
                                    ], 400);
        }
    }
}
