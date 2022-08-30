<?php

namespace Modules\Core\Services\Api\V1;

class SubsellersApiService
{
    private const BALANCE = '0';
    private const VERIFIED_EMAIL = 1;
    private const VERIFIED_CELLPHONE = 1;
    private const DEFAULT_LEVEL = 1;

    public function prepareRequestData()
    {
        $this->mergeDataApi();

        $requestData = request()->toArray();

        $requestData['account_owner_id'] = null;
        $requestData['subseller_owner_id'] = $requestData['user_id'];
        $requestData['balance'] = self::BALANCE;
        $requestData['email_verified'] = self::VERIFIED_EMAIL;
        $requestData['document'] = $requestData['document'];
        $requestData['cellphone_verified'] = self::VERIFIED_CELLPHONE;
        $requestData['release_count'] = 0;
        $requestData['password'] = bcrypt($requestData['password']);
        $requestData['level'] = self::DEFAULT_LEVEL;

        $penaltyValues = [
            'contestation_penalty_level_1' => '2000',
            'contestation_penalty_level_2' => '3000',
            'contestation_penalty_level_3' => '5000',
        ];

        $requestData['contestation_penalties_taxes'] = json_encode($penaltyValues);

        return $requestData;
    }

    public function mergeDataApi()
    {
        request()->merge([
            'user_id' => request()->user_id
        ]);
    }
}
