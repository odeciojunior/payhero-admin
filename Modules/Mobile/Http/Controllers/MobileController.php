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
                    $this->integrationApiService = app()->make("Modules\Mobile\Http\Controllers\Apis\\". $version ."\IntegrationApiService");
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
    public function login(Request $request) {
        try {
            $dataRequest = $request->json()->all();
            $validator = Validator::make($dataRequest, [
                'email'             => 'required|string|email',
                'password'          => 'required|string',
                //'mobile_push_token' => 'sometimes|string',
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
    public function dashboardGetData(Request $request) {
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
    public function financeGetData(Request $request) {
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
    public function financeWithdraw(Request $request) {
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
    public function profileGetData(Request $request) {
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
    public function profileChangePassword(Request $request) {
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
    public function profileUpdateNotification(Request $request) {
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
    public function salesByFilter(Request $request) {
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
    public function saleById(Request $request) {
        try {
            return $this->integrationApiService->salesById($request);

        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                'message' => __('definitions.message.search.error'),
            ], 400);
        }
    }
}
