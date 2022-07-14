<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\SentEmail;
use SendGrid;

/**
 * Class SendgridService
 * @package Modules\Core\Services
 */
class SendgridService
{
    /**
     * @var SendGrid
     */
    private $sendgrid;
    /**
     * @var array
     */
    private $zone;

    /**
     * SendgridService constructor.
     */
    public function __construct()
    {
        $this->sendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));
    }


    /**
     * @param bool $impersonateSubuser
     * @return SendGrid
     * @see: https://sendgrid.com/docs/API_Reference/api_v3.html
     */
    public function sendgrid($impersonateSubuser = false)
    {
        //faz a requisição como sub-usuário
        if (!empty($impersonateSubuser)) {
            return new SendGrid(getenv('SENDGRID_API_KEY'));
        } else {
            return $this->sendgrid;
        }
    }

    /**
     * @param $domain
     * @param bool $autoSet
     * @return mixed
     */
    public function addZone($domain, $autoSet = false)
    {
        $requestBody = json_decode(
            '{
                     "automatic_security": false,
                     "custom_spf" : true,
                     "default" : false,
                     "domain" : "' . $domain . '"
                 }'
        );

        $response = $this->sendgrid(true)->client->whitelabel()->domains()->post($requestBody);

        $data = json_decode($response->body());
        if ($autoSet) {
            $this->zone = $data;
        }

        return $data;
    }

    /**
     * @param $domain
     * @return bool
     */
    public function deleteZone($domain, $retryOnLimit = false)
    {
        $zone = $this->getZone($domain);
        if (!empty($zone)) {
            $response = $this->sendgrid()->client->whitelabel()->domains()->_($zone->id)->delete(null, null, null, $retryOnLimit);
            if ($response->statusCode() == 204) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getZones($limit = 50, $offset = 0, $retryOnLimit = false)
    {
        $queryParams = [
            'limit' => $limit,
            'offset' => $offset
        ];
        $response = $this->sendgrid()->client->whitelabel()->domains()->get(null, $queryParams, null, $retryOnLimit);

        return json_decode($response->body());
    }

    /**
     * @param $domain
     * @return array
     */
    public function getZone($domain)
    {
        //exclude_subusers: false para trazer os domínios cadastrados por sub-usuários
        $queryParameters = json_decode('{"domain": "' . $domain . '", "exclude_subusers": false}');

        $response = $this->sendgrid->client->whitelabel()->domains()->get(null, $queryParameters);

        $sendgridDomains = json_decode($response->body());
        foreach ($sendgridDomains as $sendgridDomain) {
            if (!empty($sendgridDomain->domain) && $sendgridDomain->domain == $domain) {
                return $sendgridDomain;
            }
        }

        return [];
    }

    /**
     * @param $domain
     * @return bool
     */
    public function setZone($domain)
    {
        $zone = $this->getZone($domain);
        if (!empty($zone)) {
            $this->zone = $zone;

            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array
     */
    public function getRecords()
    {
        if (isset($this->zone->dns)) {
            return $this->zone->dns;
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    public function getMxServer()
    {
        if (isset($this->zone->dns->mail_server)) {
            return $this->zone->dns->mail_server;
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    public function getCnameServer()
    {
        if (isset($this->zone->dns->mail_cname)) {
            return $this->zone->dns->mail_cname;
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    public function getSpfServer()
    {
        if (isset($this->zone->dns->subdomain_spf)) {
            return $this->zone->dns->subdomain_spf;
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    public function getDkimServer()
    {
        if (isset($this->zone->dns->dkim)) {
            return $this->zone->dns->dkim;
        } else {
            return [];
        }
    }

    /**
     * @param $domain
     * @return mixed
     */
    public function createLinkBrand($domain)
    {
        $this->deleteLinkBrand($domain);

        $request_body = json_decode(
            '{
                 "default": false,
                 "domain": "' . $domain . '",
                 "subdomain": "mail"
             }'
        );

        $query_params = json_decode('{"limit": 1, "offset": 1}');

        $response = $this->sendgrid(true)->client->whitelabel()->links()->post($request_body, $query_params);

        return json_decode($response->body());
    }

    /**
     * @param $domain
     * @return array
     */
    public function getLinkBrand($domain)
    {
        $queryParameters = json_decode('{"domain": "' . $domain . '"}');

        $response = $this->sendgrid()->client->whitelabel()->links()->get(null, $queryParameters);

        $links = json_decode($response->body());

        foreach ($links as $link) {
            if ($link->domain == $domain) {
                return $link;
            }
        }

        return [];
    }

    /**
     * @param $domain
     * @return bool
     */
    public function deleteLinkBrand($domain)
    {
        $link = $this->getLinkBrand($domain);
        if (!empty($link)) {
            $response = $this->sendgrid()->client->whitelabel()->links()->_($link->id)->delete();
            if ($response->statusCode() == 204) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param $domainId
     * @return bool
     */
    public function validateDomain($domainId)
    {
        if (!empty($domainId)) {
            $response = $this->sendgrid()->client->whitelabel()->domains()->_($domainId)->validate()->post();
            $response = json_decode($response->body());

            if ($response->valid == true) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param $linkId
     * @return bool
     */
    public function validateBrandLink($linkId)
    {
        if (!empty($linkId)) {
            $response = $this->sendgrid()->client->whitelabel()->links()->_($linkId)->validate()->post();
            $response = json_decode($response->body());

            if ($response->valid == true) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function sendEmail(string $fromEmail, string $fromName, string $toEmail, string $toName, string $templateId, array $data): bool
    {
        try {
            if (env('APP_ENV') == 'production') {
                if (!foxutils()->validateEmail($toEmail)) {
                    return false;
                }
            } else {
                $toEmail = env('APP_EMAIL_TEST');
                if (empty($toEmail)) {
                    return false;
                }
            }


            $email = new \SendGrid\Mail\Mail();
            $email->setFrom($fromEmail, $fromName);
            $email->addTo($toEmail, $toName);
            $email->addDynamicTemplateDatas($data);
            $email->setTemplateId($templateId);

            $response = $this->sendgrid()->send($email);
            $statusCode = $response->statusCode();
            $body = $response->body();

            if (in_array($statusCode, [200, 201, 202])) {
                $status = "success";
            } else {
                $status = "error";
            }

            SentEmail::create(
                [
                    'from_email' => $fromEmail,
                    'from_name' => $fromName == '' ? ' vazio ' : $fromName,
                    'to_email' => $toEmail,
                    'to_name' => $toName,
                    'template_id' => $templateId,
                    'template_data' => json_encode($data),
                    'status_code' => $statusCode,
                    'status' => $status,
                    'log_error' => $body,
                ]
            );

            return true;

        } catch (Exception $e) {

            report($e);
            SentEmail::create(
                [
                    'from_email' => $fromEmail,
                    'from_name' => $fromName == '' ? ' vazio ' : $fromName,
                    'to_email' => $toEmail,
                    'to_name' => $toName,
                    'template_id' => $templateId,
                    'template_data' => json_encode($data),
                    'status_code' => 400,
                    'status' => "error",
                    'log_error' => $e->getMessage(),
                ]
            );

            return false;
        }
    }
}
