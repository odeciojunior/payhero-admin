<?php

namespace Modules\Core\Services;

use Illuminate\View\View;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;
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
     * @var Array
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
     * @param $domain
     * @return mixed
     */
    public function addZone($domain, $autoSet = false)
    {
        $requestBody = json_decode('{
                     "automatic_security": false,
                     "custom_spf" : true,
                     "default" : false,
                     "domain" : "' . $domain . '"
                 }');

        $response = $this->sendgrid->client->whitelabel()->domains()->post($requestBody);

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
    public function deleteZone($domain)
    {
        $zone = $this->getZone($domain);
        if (!empty($zone)) {

            $response = $this->sendgrid->client->whitelabel()->domains()->_($zone->id)->delete();
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
    public function getZones()
    {
        $response = $this->sendgrid->client->whitelabel()->domains()->get();

        return json_decode($response->body());
    }

    /**
     * @param $domain
     * @return array
     */
    public function getZone($domain)
    {
        $response = $this->sendgrid->client->whitelabel()->domains()->get();

        $sendgridDomains = json_decode($response->body());
        foreach ($sendgridDomains as $sendgridDomain) {

            if ($sendgridDomain->domain == $domain) {
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
        $request_body = json_decode('{
                 "default": false,
                 "domain": "' . $domain . '",
                 "subdomain": "mail"
             }');

        $query_params = json_decode('{"limit": 1, "offset": 1}');

        $response = $this->sendgrid->client->whitelabel()->links()->post($request_body, $query_params);

        return json_decode($response->body());
    }

    /**
     * @param $domain
     * @return array
     */
    public function getLinkBrand($domain)
    {
        $response = $this->sendgrid->client->whitelabel()->links()->get(null, null);

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

            $response = $this->sendgrid->client->whitelabel()->links()->_($link->id)->delete();
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
            $response = $this->sendgrid->client->whitelabel()->domains()->_($domainId)->validate()->post();
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
            $response = $this->sendgrid->client->whitelabel()->links()->_($linkId)->validate()->post();
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

    public function sendEmail($subject, $fromEmail, $fromName, $toEmail, $toName, $templateId, $data)
    {
        try {
            $email = new \SendGrid\Mail\Mail();
            $email->setFrom($fromEmail, $fromName);
            $email->addTo($toEmail, $toName);
            $email->addDynamicTemplateDatas($data);
            $email->setTemplateId($templateId);
            //            $email->addContent(
            //                "text/html", $view->render()
            //            );
            //            $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
            try {
                $response = $this->sendgrid->send($email);
                dd($response);
            } catch (Exception $e) {
                return false;
                echo 'Caught exception: ' . $e->getMessage() . "\n";
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
