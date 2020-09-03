<?php


namespace Modules\Core\Services;

use Aws\Credentials\Credentials;
use Aws\Sns\SnsClient;
use Exception;

class AwsSns
{
    private $snsClient;

    public function __construct()
    {
        $this->snsClient = new SnsClient([
            'version' => '2010-03-31',
            'credentials' => new Credentials(
                getenv('AWS_ACCESS_KEY_ID_SMS'),
                getenv('AWS_SECRET_ACCESS_KEY_SMS')
            ),
            'region' => getenv('AWS_DEFAULT_REGION_SMS'),
        ]);
    }

    public function sendMessage($phone, $message)
    {
        try {
            $result = $this->snsClient->publish([
                'Message' => $message,
                'PhoneNumber' => $phone
            ]);

            dd($result);
        } catch (Exception $e) {
            report($e);
        }
    }


}