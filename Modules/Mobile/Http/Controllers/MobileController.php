<?php

namespace Modules\Mobile\Http\Controllers;

use App\Exceptions\Apis\Integrations\IntegrationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use stringEncode\Exception;

class MobileController extends Controller
{
    const lastVersion = 'v1';
    private $version = 'v10';
    /**
     * @var IntegrationService
     */
    private $mobileApiService;


    public function __construct(Request $request)
    {
        $this->version = $request->route('version');

//        if ($this->version) {
//            $this->getMobileApiService($this->version);
//        } else {
//            $this->getMobileApiService(self::lastVersion);
//        }
    }


    public function getMobileApiService($version)
    {
        if (!$this->mobileApiService) {

            switch ($version) {
                case 'v1':
                    //$this->mobileApiService = app()->make('App\Http\Controllers\Apis\Integrations\v10\IntegrationService');
                    break;
                default:
                    throw new IntegrationException('Versão invalida.');
                    break;
            }
        }

        return $this->integrationService;
    }


    public function login(Request $request) {

        try {
            $loginClassPath = "Modules\Mobile\Http\Controllers\AuthApiController"; //"Modules\Mobile\Http\Controllers\Apis\\". $this->version ."\AuthApiController";
            $authController = app()->make($loginClassPath);

        } catch (Exception $ex) {
            throw new Exception('Versão inválida.');
        }


        return $authController->login($request);
    }

}
