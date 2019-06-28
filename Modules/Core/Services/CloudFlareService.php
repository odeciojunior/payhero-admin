<?php

namespace Modules\Core\Services;

use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\User;
use Cloudflare\API\Endpoints\Zones;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Class CloudFlareService
 * @package Modules\Core\Services
 */
class CloudFlareService
{
    const shopifyIp   = '23.227.38.32';
    const checkoutIp  = '104.248.122.89';
    const sacIp       = '104.248.122.89';
    const affiliateIp = '104.248.122.89';
    /**
     * @var APIKey
     */
    private $key;
    /**
     * @var Guzzle
     */
    private $adapter;
    /**
     * @var DNS
     */
    private $dns;
    /**
     * @var Zones
     */
    private $zones;
    /**
     * @var string
     */
    private $zoneID;
    /**
     * @var User
     */
    private $user;
    /**
     * @var SendgridService
     */
    private $sendgridService;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed|SendgridService
     */
    private function getSendgridService()
    {
        if (!$this->sendgridService) {
            $this->sendgridService = app(SendgridService::class);
        }

        return $this->sendgridService;
    }

    /**
     * CloudFlareService constructor.
     */
    public function __construct()
    {
        $this->key     = new APIKey(env('CLOUDFLARE_EMAIL'), env('CLOUDFLARE_TOKEN'));
        $this->adapter = new Guzzle($this->key);
        $this->dns     = new DNS($this->adapter);
        $this->zones   = new Zones($this->adapter);
        $this->user    = new User($this->adapter);
    }

    /**
     * @param string $domain
     * @return $this
     * @throws \Cloudflare\API\Endpoints\EndpointException
     */
    public function zone(string $domain)
    {
        $this->zoneID = $this->zones->getZoneID($domain);

        return $this;
    }

    /**
     * @param string $domain
     * @return string
     * @throws \Cloudflare\API\Endpoints\EndpointException
     */
    public function setZone(string $domain)
    {
        $this->zoneID = $this->zones->getZoneID($domain);

        return $this->zoneID;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getZones(string $name = '')
    {
        $zones = $this->zones->listZones($name);

        return $zones->result;
    }

    /**
     * @param string $zone
     * @return \stdClass
     */
    public function addZone(string $zone)
    {
        return $this->zones->addZone($zone);
    }

    /**
     * @param string $domain
     * @return bool
     */
    public function deleteZone(string $domain)
    {
        try {
            $zoneID = $this->zones->getZoneID($domain);
            $user   = $this->adapter->delete('zones/' . $zoneID);

            $body = json_decode($user->getBody());

            if (isset($body->result->id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::warning('Erro ao remover dominio (dominio inexistente)');
            report($e);

            return false;
        }
    }

    /**
     * @param string|null $domain
     * @return mixed
     */
    public function getRecords(string $domain = null)
    {
        if ($domain) {
            $this->setZone($domain);
        }

        return $this->dns->listRecords($this->zoneID)->result;
    }

    /**
     * @param string $type    | A, CNAME, AAAA
     * @param string $name    | dominio, subdominio
     * @param string $content | IP, dominio
     * @param int $ttl
     * @param bool $proxied
     * @return bool
     */
    public function addRecord(string $type, string $name, string $content, int $ttl = 0, bool $proxied = true)
    {
        if ($this->dns->addRecord($this->zoneID, $type, $name, $content, $ttl, $proxied) === true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $domain
     * @return bool
     */
    public function deleteRecord(string $domain)
    {
        try {
            if ($this->zoneID) {
                $records = $this->dns->listRecords($this->zoneID)->result;
                foreach ($records as $record) {

                    if ($record->name == $domain) {
                        $user = $this->adapter->delete('zones/' . $this->zoneID . '/dns_records/' . $record->id);

                        $body = json_decode($user->getBody());

                        if (isset($body->result->id)) {
                            return true;
                        }
                    }
                }
            } else {
                //nenhuma zona selecionada
                return false;
            }

            return false;
        } catch (Exception $e) {
            Log::warning('Erro ao remover record (record inexistente)');
            report($e);

            return false;
        }
    }

    /**
     * @param string $domain
     * @return bool
     * @throws \Cloudflare\API\Endpoints\EndpointException
     */
    public function activationCheck(string $domain)
    {
        try {
            $zoneID = $this->zones->getZoneID($domain);

            return $this->zones->activationCheck($zoneID);
        } catch (Exception $e) {
            Log::warning('Erro ao checar dominio');
            report($e);

            return false;
        }
    }

    /**
     * @param string $domain
     * @param string $ipAddress
     * @return bool
     * @throws \Cloudflare\API\Endpoints\EndpointException
     */
    public function integrationWebsite(string $domain, string $ipAddress)
    {
        $this->deleteZone($domain);

        //criar o dominio
        $newZone = $this->addZone($domain);

        if ($newZone) {
            //dominio criado

            $this->setZone($newZone->name);

            $this->addRecord("A", $newZone->name, $ipAddress);

            $this->addRecord("CNAME", 'www', $newZone->name);

            $this->addRecord("A", 'checkout', self::checkoutIp);

            $this->addRecord("A", 'sac', self::sacIp);

            $this->addRecord("A", 'affiliate', self::affiliateIp);

            $sendgridResponse = $this->getSendgridService()->addZone($newZone->name);

            foreach ($sendgridResponse->dns as $responseDns) {
                if ($responseDns->type == 'mx') {
                    $this->addRecord('MX', $responseDns->host, $responseDns->data, 0, false, '1');
                } else {
                    $this->addRecord(strtoupper($responseDns->type), $responseDns->host, $responseDns->data, 0, false);
                }
            }

            $this->getSendgridService()->deleteLinkBrand($newZone->name);
            $linkBrandResponse = $this->getSendgridService()->createLinkBrand($newZone->name);

            foreach ($linkBrandResponse->dns as $responseDns) {
                $this->addRecord(strtoupper($responseDns->type), $responseDns->host, $responseDns->data, 0, false);
            }

            sleep(5);
            $responseValidateDomain = $this->getSendgridService()->validateDomain($sendgridResponse->id);
            $responseValidateLink   = $this->getSendgridService()->validateBrandLink($linkBrandResponse->id);

            if ($responseValidateDomain && $responseValidateLink) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param $domain
     * @return bool
     * @throws \Cloudflare\API\Endpoints\EndpointException
     */
    public function integrationShopify($domain)
    {
        $this->deleteZone($domain);

        //criar o dominio
        $newZone = $this->addZone($domain);

        if ($newZone) {
            //dominio criado

            $this->setZone($newZone->name);

            $this->addRecord("A", $newZone->name, self::shopifyIp);

            $this->addRecord("CNAME", 'www', 'shops.myshopify.com');

            $this->addRecord("A", 'checkout', self::checkoutIp);

            $this->addRecord("A", 'sac', self::sacIp);

            $this->addRecord("A", 'affiliate', self::affiliateIp);

            return true;
        } else {
            return false;
        }
    }
}
