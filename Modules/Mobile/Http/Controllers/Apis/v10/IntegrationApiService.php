<?php

namespace Modules\Mobile\Http\Controllers\Apis\v10;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    private $profileApiService;
    private $salesApiService;
    private $notificationApiService;
    private $projectApiService;

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
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao fazer login',
                                    ], 400);
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
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao fazer logout',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function logoutDevice(Request $request)
    {
        try {
            if (!$this->authApiService) {
                $this->getIntegrationApiService('auth');
            }

            return $this->authApiService->logoutDevice($request);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao fazer logout',
                                    ], 400);
        }
    }

    /**
     * @param $class
     * @throws Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getIntegrationApiService($service)
    {
        switch ($service) {
            case 'dashboard':
                $this->dashboardApiService = app()->make("Modules\Mobile\Http\Controllers\Apis\\" . self::version . "\DashboardApiService");
                break;
            case 'auth':
                $this->authApiService = app()->make("Modules\Mobile\Http\Controllers\Apis\\" . self::version . "\AuthApiService");
                break;
            case 'finance':
                $this->financeApiService = app()->make("Modules\Mobile\Http\Controllers\Apis\\" . self::version . "\FinanceApiService");
                break;
            case 'profile':
                $this->profileApiService = app()->make("Modules\Mobile\Http\Controllers\Apis\\" . self::version . "\ProfileApiService");
                break;
            case 'sales':
                $this->salesApiService = app()->make("Modules\Mobile\Http\Controllers\Apis\\" . self::version . "\SalesApiService");
                break;
            case 'notification':
                $this->notificationApiService = app()->make("Modules\Mobile\Http\Controllers\Apis\\" . self::version . "\NotificationApiService");
                break;
            case 'project':
                $this->projectApiService = app()->make("Modules\Mobile\Http\Controllers\Apis\\" . self::version . "\ProjectApiService");
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
    public function dashboardGetData(Request $request)
    {
        try {

            if (!$this->dashboardApiService) {
                $this->getIntegrationApiService('dashboard');
            }

            return $this->dashboardApiService->getDashboardValues($request);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao carregar dados do dashboard',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function financeGetData(Request $request)
    {
        try {

            if (!$this->financeApiService) {
                $this->getIntegrationApiService('finance');
            }

            return $this->financeApiService->financeGetData($request);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao carregar dados de finanças',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function financeWithdraw(Request $request)
    {
        try {

            if (!$this->financeApiService) {
                $this->getIntegrationApiService('finance');
            }

            return $this->financeApiService->store($request);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao carregar dados de finanças',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function financeAccountInformation(Request $request)
    {
        try {

            if (!$this->financeApiService) {
                $this->getIntegrationApiService('finance');
            }

            return $this->financeApiService->getAccountInformation($request);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao carregar dados de finanças',
                                    ], 400);
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function profileGetData(Request $request)
    {
        try {

            if (!$this->profileApiService) {
                $this->getIntegrationApiService('profile');
            }

            return $this->profileApiService->getProfileData($request);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao carregar dados de profile',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function profileChangePassword(Request $request)
    {
        try {

            if (!$this->profileApiService) {
                $this->getIntegrationApiService('profile');
            }

            return $this->profileApiService->changePassword($request);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao alterar senha',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function profileUpdateNotification(Request $request)
    {
        try {

            if (!$this->profileApiService) {
                $this->getIntegrationApiService('profile');
            }

            return $this->profileApiService->updateUserNotification($request);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao atualizar a configuração de notificações',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function salesByFilter(Request $request)
    {
        try {

            if (!$this->salesApiService) {
                $this->getIntegrationApiService('sales');
            }

            return $this->salesApiService->salesByFilter($request);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao recuperar vendas',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getSaleDetails(Request $request)
    {
        try {

            if (!$this->salesApiService) {
                $this->getIntegrationApiService('sales');
            }

            return $this->salesApiService->getSaleDetails($request);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao recuperar detalhes da venda',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function notificationGetUnread(Request $request)
    {
        try {

            if (!$this->notificationApiService) {
                $this->getIntegrationApiService('notification');
            }

            return $this->notificationApiService->getUnreadNotifications($request);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao recuperar as notificações',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getUserProjects(Request $request)
    {
        try {

            if (!$this->projectApiService) {
                $this->getIntegrationApiService('project');
            }

            return $this->projectApiService->getProjects($request);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao recuperar os projetos',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function sendMessage(Request $request)
    {
        try {

            if (!$this->notificationApiService) {
                $this->getIntegrationApiService('notification');
            }

            return $this->notificationApiService->sendMessage($request);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao recuperar as notificações',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function sendNotification(Request $request) {
        try {

            if (!$this->notificationApiService) {
                $this->getIntegrationApiService('notification');
            }

            return $this->notificationApiService->processPostback($request);

        } catch (Exception $ex) {
            return response()->json([
                                        'status' => 'error',
                                        'message' => 'Erro ao recuperar os projetos'
                                    ], 400);
        }
    }
}
