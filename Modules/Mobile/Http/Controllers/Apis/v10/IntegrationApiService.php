<?php

namespace Modules\Mobile\Http\Controllers\Apis\v10;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Lcobucci\JWT\Parser;
use stringEncode\Exception;

/**
 * Class IntegrationApiService
 * @package Modules\Mobile\Http\Controllers\Apis\v10
 */
class IntegrationApiService {

    const version = 'v10';
    private $dashboardApiService;
    private $authApiService;
    private $financeApiService;

    /**
     * IntegrationApiService constructor.
     */
    public function __construct() { }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function login(Request $request)
    {
        try {
            if (!$this->authApiService) {
                $this->getIntegrationApiService('auth');
            }

            return $this->authApiService->login($request);

        } catch (Exception $ex) {
            return response()->json(['status' => 'error',
                'message' => 'Erro ao fazer login'], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function logout(Request $request)
    {
        try {
            if (!$this->authApiService) {
                $this->getIntegrationApiService('auth');
            }

            return $this->authApiService->logout($request);

        } catch (Exception $ex) {
            return response()->json(['status' => 'error',
                'message' => 'Erro ao fazer logout'], 400);
        }
    }

    /**
     * @param $class
     * @throws Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getIntegrationApiService($class)
    {
        switch ($class) {
            case 'dashboard':
                $this->dashboardApiService = app()->make("Modules\Mobile\Http\Controllers\Apis\\". self::version ."\DashboardApiService");
                break;
            case 'auth':
                $this->authApiService = app()->make("Modules\Mobile\Http\Controllers\Apis\\". self::version ."\AuthApiService");
                break;
            case 'finance':
                $this->financeApiService = app()->make("Modules\Mobile\Http\Controllers\Apis\\". self::version ."\AuthApiService");
                break;
            default:
                throw new Exception('Classe inválida.');
                break;
        }
    }

    /**
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function dashboardGetData(Request $request) {
        try {

            if (!$this->dashboardApiService) {
                $this->getIntegrationApiService('dashboard');
            }

            return $this->dashboardApiService->getDashboardValues($request);

        } catch (Exception $ex) {
            return response()->json(['status' => 'error',
                'message' => 'Erro ao carregar dados do dashboard'], 400);
        }
    }

    public function financeGetData() {
        try {

            if (!$this->financeApiService) {
                $this->getIntegrationApiService('finance');
            }

            return $this->financeApiService->getFinanceData();

        } catch (Exception $ex) {
            return response()->json(['status' => 'error',
                'message' => 'Erro ao carregar dados de finanças'], 400);
        }
    }
}
