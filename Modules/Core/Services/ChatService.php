<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Company;

class ChatService
{
    public static function getData()
    {
        $user = auth()->user();
        preg_match('/([\w]+)\s(.*)/', $user->name, $nameArray);
        return (object)[
            'token' => "1d0b79d1-3275-4984-9ce1-9c8da217d609",
            'host' => "https://wchat.freshchat.com",
            'externalId' => hashids_encode($user->id),
            'firstName' => $nameArray[1]??'',
            'lastName' => $nameArray[2]??'',
            'email' => $user->email,
            'phone' => preg_replace('/\+?55/', '', $user->cellphone),
            'phoneCountryCode' => "+55"
        ];
    }

    public static function getExtraData()
    {
        $company = Company::where('user_id', auth()->user()->account_owner_id)->first();
        return (object)[
            'company' => $company->fantasy_name??'',
        ];
    }
}
