<?php

namespace Modules\UserTerms\Http\Controllers;

use Exception;
use Modules\Core\Entities\UserTerms;
use Modules\Core\Services\IpService;
use Jenssegers\Agent\Facades\Agent;
use Illuminate\Http\Request;

class UserTermsApiController
{
    public function store(Request $request)
    {
        try {
            $userTermsModel = new UserTerms();

            $userLogged = auth()->user();

            $userTerm = $userTermsModel->where([
                                                   ['user_id', $userLogged->account_owner_id],
                                                   ['term_version', 'v1'],
                                                   ['accepted_at', true],
                                               ])->first();

            if (!empty($userTerm)) {
                return response()->json([
                                            'message' => 'Salvo com sucesso!',
                                        ], 200);
            }

            $geoIp = null;
            try {
                $geoIp = geoip()->getLocation(IpService::getRealIpAddr());
            } catch (Exception $e) {
                //
            }

            $operationalSystem = Agent::platform();
            $browser           = Agent::browser();

            $deviceData = [
                'operational_system'       => Agent::platform(),
                'operation_system_version' => Agent::version($operationalSystem),
                'browser'                  => Agent::browser(),
                'browser_version'          => Agent::version($browser),
                'is_mobile'                => Agent::isMobile(),
                'ip'                       => @$geoIp['ip'],
                'country'                  => @$geoIp['country'],
                'city'                     => @$geoIp['city'],
                'state'                    => @$geoIp['state'],
                'state_name'               => @$geoIp['state_name'],
                'zip_code'                 => @$geoIp['postal_code'],
                'currency'                 => @$geoIp['currency'],
                'lat'                      => @$geoIp['lat'],
                'lon'                      => @$geoIp['lon'],
            ];

            $userTermsCreated = $userTermsModel->create([
                                                            'user_id'      => $userLogged->account_owner_id,
                                                            'term_version' => 'v1',
                                                            'device_data'  => json_encode($deviceData, true),
                                                            'accepted_at'  => true,
                                                        ]);

            if ($userTermsCreated) {
                return response()->json([
                                            'message' => 'Salvo com sucesso!',
                                        ], 200);
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro, tente novamente!',
                                        ], 400);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro',
                                    ], 400);
        }
    }
}
