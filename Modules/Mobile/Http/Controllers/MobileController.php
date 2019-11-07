<?php

namespace Modules\Mobile\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use stringEncode\Exception;

class MobileController extends Controller
{
    const lastVersion = 'v10';
    /**
     * @var IntegrationService
     */
    private $mobileApiService;


    public function __construct(Request $request)
    {
        $version = $request->route('version');

        if ($version) {
            $this->getMobileApiService($version);
        } else {
            $this->getMobileApiService(self::lastVersion);
        }
    }


    public function getMobileApiService($version)
    {
        if (!$this->mobileApiService) {

            switch ($version) {
                case 'v10':
                    $this->mobileApiService = app()->make("Modules\Mobile\Http\Controllers\Apis\\". $version ."\MobileApiService");
                    break;
                default:
                    throw new Exception('Versão inválida.');
                    break;
            }
        }

        return $this->mobileApiService;
    }


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

            return $this->mobileApiService->login($request);

        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                'message' => __('definitions.message.search.error'),
            ], 400);
        }
    }

    public function dashboardGetValues() {

        try {

            return $this->mobileApiService->dashboardGetValues();

        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                'message' => __('definitions.message.search.error'),
            ], 400);
        }
    }

    public function dashboardGetTopProducts(Request $request) {

        try {

            return $this->mobileApiService->dashboardGetTopProducts($request);

        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                'message' => __('definitions.message.search.error'),
            ], 400);
        }
    }

}
