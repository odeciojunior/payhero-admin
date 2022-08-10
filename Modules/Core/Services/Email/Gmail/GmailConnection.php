<?php

namespace Modules\Core\Services\Email\Gmail;

use Exception;

class GmailConnection
{
    public function __construct()
    {
        $this->credentials = "credentials.json";
        $this->client = $this->create_client();
    }

    public function get_client()
    {
        return $this->client;
    }

    public function get_credentials()
    {
        return $this->credentials;
    }

    public function is_connected()
    {
        return $this->is_connected;
    }

    /**
     * @throws \Google\Exception
     * @throws Exception
     */
    public function create_client()
    {
        $client = new \Google_Client();
        $client->setApplicationName("Gmail API PHP Quickstart");
        $client->setScopes(\Google_Service_Gmail::GMAIL_READONLY);
        $client->setAuthConfig("credentials.json");
        $client->setAccessType("offline");
        $client->setPrompt("select_account consent");

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.

        //        $accessToken = config('token_gmail');
        //        $tokenPath = storage_path('app/token_gmail.json');
        //        if (!empty($accessToken)) {
        //            $client->setAccessToken($accessToken);
        //        }else{
        //            $accessToken = json_decode(file_get_contents($tokenPath), true);
        //            $client->setAccessToken($accessToken);
        //        }

        $tokenPath = storage_path("app/token_gmail.json");

        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        //        $accessToken = config('token_gmail');
        //        if ($accessToken) {
        //            $client->setAccessToken($accessToken);
        //        } else if (file_exists($tokenPath)) {
        //            $accessToken = json_decode(file_get_contents($tokenPath), true);
        //            $client->setAccessToken($accessToken);
        //        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                /// Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print "Enter verification code: ";
                $authCode = trim(fgets(STDIN));

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);
                // Check to see if there was an error.
                if (array_key_exists("error", $accessToken)) {
                    $this->is_connected = false;
                    throw new Exception(join(", ", $accessToken));
                }
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }

        $this->is_connected = true;
        return $client;
    }
}
