<?php

namespace Modules\Core\Services;

use Aws\Credentials\Credentials;
use Aws\Sns\SnsClient;
use Exception;
use Illuminate\Support\Facades\Log;

class AwsSns
{
    public static function sendMessage($phone, $message): void
    {
        $snsClient = new SnsClient([
            "version" => "2010-03-31",
            "credentials" => new Credentials(getenv("AWS_ACCESS_KEY_ID_SMS"), getenv("AWS_SECRET_ACCESS_KEY_SMS")),
            "region" => getenv("AWS_DEFAULT_REGION_SMS"),
        ]);

        try {
            $snsClient->publish([
                "Message" => $message,
                "PhoneNumber" => $phone,
            ]);
        } catch (Exception $e) {
            Log::warning("Erro ao enviar sms SNS-AWS - SendMessage");
            report($e);
        }
    }
}
