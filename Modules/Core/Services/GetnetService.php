<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Http;

class GetnetService
{

    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->clientId = getenv('Client_ID');
        $this->clientSecret = getenv('Client_Secret');
        $this->token();
    }

    public function token()
    {
        $credentialsBase64 = base64_encode($this->clientSecret . ':' . $this->clientSecret);

    }

}