<?php

namespace Modules\Core\Services;

use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\Crypto;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\EndpointException;
use Cloudflare\API\Endpoints\SSL;
use Cloudflare\API\Endpoints\TLS;
use Cloudflare\API\Endpoints\User;
use Cloudflare\API\Endpoints\Zones;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Foundation\Application;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\DomainRecord;
use PHPHtmlParser\Dom;
use stdClass;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use Illuminate\Support\Facades\Log;

/**
 * Class CloudFlareService
 * @package Modules\Core\Services
 */
class CloudFlareService
{
    const shopifyIp = "23.227.38.65";
    const checkoutIp = "checkout.azcend.com.br";
    const sacIp = "sac.azcend.com.br";
    const affiliateIp = "affiliate.checkout.azcend.com.br";
    const adminIp = "admin.azcend.com.br";

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
     * @var SSL
     */
    private $ssl;
    /**
     * @var TLS
     */
    private $tls;
    /**
     * @var Crypto
     */
    private $crypto;
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
     * @var DomainRecord
     */
    private $domainRecordModel;

    /**
     * @return Application|mixed|SendgridService
     */
    public function getSendgridService()
    {
        if (!$this->sendgridService) {
            $this->sendgridService = app(SendgridService::class);
        }

        return $this->sendgridService;
    }

    /**
     * @return DomainRecord|Application|mixed
     */
    private function getDomainRecordModel()
    {
        if (!$this->domainRecordModel) {
            $this->domainRecordModel = app(DomainRecord::class);
        }

        return $this->domainRecordModel;
    }

    /**
     * CloudFlareService constructor.
     */
    public function __construct()
    {
        try {
            $this->key = new APIKey(getenv("CLOUDFLARE_EMAIL"), getenv("CLOUDFLARE_TOKEN"));
            $this->adapter = new Guzzle($this->key);
            $this->dns = new DNS($this->adapter);
            $this->ssl = new SSL($this->adapter);
            $this->tls = new TLS($this->adapter);
            $this->crypto = new Crypto($this->adapter);
            $this->zones = new Zones($this->adapter);
            $this->user = new User($this->adapter);
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * @param  string  $domain
     * @return $this
     * @throws EndpointException
     */
    public function zone(string $domain)
    {
        $this->zoneID = $this->zones->getZoneID($domain);

        return $this;
    }

    public function setZone(string $domain)
    {
        $this->zoneID = $this->zones->getZoneID($domain);

        return $this->zoneID;
    }

    /**
     * @param  string  $name
     * @return mixed
     */
    public function getZones(string $name = "")
    {
        $zones = $this->zones->listZones($name, "", 1, 1000);

        return $zones->result;
    }

    /**
     * @param  string  $zone
     * @return bool|stdClass
     */
    public function addZone(string $zone)
    {
        try {
            return $this->zones->addZone($zone);
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @param  string  $domain
     * @return bool
     */
    public function deleteZone(string $domain)
    {
        try {
            $zoneID = $this->zones->getZoneID($domain);
            $user = $this->adapter->delete("zones/".$zoneID);

            $body = json_decode($user->getBody());

            if (isset($body->result->id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param  string  $zoneId
     * @return bool
     */
    public function deleteZoneById(string $zoneId)
    {
        try {
            $user = $this->adapter->delete("zones/".$zoneId);

            $body = json_decode($user->getBody());

            if (isset($body->result->id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param  string|null  $domain
     * @return mixed
     * @throws EndpointException
     */
    public function getRecords(string $domain = null)
    {
        if ($domain) {
            $this->setZone($domain);
        }

        return $this->dns->listRecords($this->zoneID)->result;
    }

    /**
     * @param  string  $type  | A, CNAME, AAAA
     * @param  string  $name  | dominio, subdominio
     * @param  string  $content  | IP, dominio
     * @param  int  $ttl
     * @param  bool  $proxied
     * @param  string  $priority
     * @return array
     */
    public function addRecord(
        string $type,
        string $name,
        string $content,
        int $ttl = 0,
        bool $proxied = true,
        $priority = "0",
    ) {
        $options = [
            "type" => $type,
            "name" => $name,
            "content" => $content,
            "proxied" => $proxied,
        ];

        if ($ttl > 0) {
            $options["ttl"] = $ttl;
        }

        if (!empty($priority)) {
            $options["priority"] = (int) $priority;
        } else {
            if ($priority == 0) {
                $options["priority"] = 0;
            } else {
                return [];
            }
        }

        if (!empty($data)) {
            $options["data"] = $data;
        }

        $user = $this->adapter->post("zones/".$this->zoneID."/dns_records", $options);

        $this->body = json_decode($user->getBody());

        if (isset($this->body->result->id)) {
            return $this->body->result->id;
        }

        return [];
    }

    /**
     * @param  string  $zoneID
     * @param  string  $recordID
     * @param  array  $details
     * @return mixed|object
     */
    public function updateRecordDetails(string $zoneID, string $recordID, array $details)
    {
        try {
            if (empty($zoneID) || empty($recordID) || empty($details)) {
                return false;
            }

            $response = $this->adapter->put("zones/".$zoneID."/dns_records/".$recordID, $details);

            return json_decode($response->getBody())->success;
        } catch (Exception $e) {
            if ($e->getMessage() != "This record type cannot be proxied.") {
                report($e);
            }

            return false;
        }
    }

    /**
     * @param  string  $recordId
     * @return bool
     */
    public function deleteRecord(string $recordId)
    {
        try {
            $this->adapter->delete("zones/".$this->zoneID."/dns_records/".$recordId);
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * @param  string  $domain
     * @return bool
     */
    public function activationCheck(string $domain)
    {
        try {
            $zoneID = $this->zones->getZoneID($domain);

            return $this->zones->activationCheck($zoneID);
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function integrationWebsite(int $domainModelId, string $domain, $ipAddress)
    {
        $domainModel = Domain::find($domainModelId);

        $this->removeDomain($domainModel);
        $this->getDomainRecordModel()
            ->where("domain_id", $domainModelId)
            ->delete();

        //criar o dominio
        $newZone = $this->addZone($domain);

        if ($newZone) {
            //dominio criado

            $domainModel->update([
                "cloudflare_domain_id" => $newZone->id,
            ]);

            $this->setZone($newZone->name);

            if (!empty($ipAddress)) {
                $recordId = $this->addRecord("A", $newZone->name, $ipAddress, 0, false);
                $this->getDomainRecordModel()->create([
                    "domain_id" => $domainModelId,
                    "cloudflare_record_id" => $recordId,
                    "type" => "A",
                    "name" => $newZone->name,
                    "content" => $ipAddress,
                    "system_flag" => 1,
                    "proxy" => 0,
                ]);
            }

            $recordId = $this->addRecord("CNAME", "www", $newZone->name);
            $this->getDomainRecordModel()->create([
                "domain_id" => $domainModelId,
                "cloudflare_record_id" => $recordId,
                "type" => "CNAME",
                "name" => "www",
                "content" => $newZone->name,
                "system_flag" => 1,
            ]);

            $recordId = $this->addRecord("CNAME", "checkout", self::checkoutIp);
            $this->getDomainRecordModel()->create([
                "domain_id" => $domainModelId,
                "cloudflare_record_id" => $recordId,
                "type" => "CNAME",
                "name" => "checkout",
                "content" => self::checkoutIp,
                "system_flag" => 1,
            ]);

            $recordId = $this->addRecord("CNAME", "sac", self::sacIp);
            $this->getDomainRecordModel()->create([
                "domain_id" => $domainModelId,
                "cloudflare_record_id" => $recordId,
                "type" => "CNAME",
                "name" => "sac",
                "content" => self::sacIp,
                "system_flag" => 1,
            ]);

            $recordId = $this->addRecord("CNAME", "affiliate", self::affiliateIp);
            $this->getDomainRecordModel()->create([
                "domain_id" => $domainModelId,
                "cloudflare_record_id" => $recordId,
                "type" => "CNAME",
                "name" => "affiliate",
                "content" => self::affiliateIp,
                "system_flag" => 1,
            ]);

            $recordId = $this->addRecord("CNAME", "tracking", self::adminIp);
            $this->getDomainRecordModel()->create([
                "domain_id" => $domainModelId,
                "cloudflare_record_id" => $recordId,
                "type" => "CNAME",
                "name" => "tracking",
                "content" => self::adminIp,
                "system_flag" => 1,
            ]);

            $this->getSendgridService()->deleteZone($newZone->name);
            $sendgridResponse = $this->getSendgridService()->addZone($newZone->name);

            foreach ($sendgridResponse->dns as $responseDns) {
                if ($responseDns->type == "mx") {
                    $recordId = $this->addRecord("MX", $responseDns->host, $responseDns->data, 0, false, "1");
                    $this->getDomainRecordModel()->create([
                        "domain_id" => $domainModelId,
                        "cloudflare_record_id" => $recordId,
                        "type" => "MX",
                        "name" => $responseDns->host,
                        "content" => $responseDns->data,
                        "system_flag" => 1,
                        "proxy" => 0,
                    ]);
                } else {
                    $recordId = $this->addRecord(
                        strtoupper($responseDns->type),
                        $responseDns->host,
                        $responseDns->data,
                        0,
                        false,
                    );
                    $this->getDomainRecordModel()->create([
                        "domain_id" => $domainModelId,
                        "cloudflare_record_id" => $recordId,
                        "type" => strtoupper($responseDns->type),
                        "name" => $responseDns->host,
                        "content" => $responseDns->data,
                        "system_flag" => 1,
                        "proxy" => 0,
                    ]);
                }
            }

            $this->getSendgridService()->deleteLinkBrand($newZone->name);
            $linkBrandResponse = $this->getSendgridService()->createLinkBrand($newZone->name);

            foreach ($linkBrandResponse->dns as $responseDns) {
                $recordId = $this->addRecord(
                    strtoupper($responseDns->type),
                    $responseDns->host,
                    $responseDns->data,
                    0,
                    false,
                );
                $this->getDomainRecordModel()->create([
                    "domain_id" => $domainModelId,
                    "cloudflare_record_id" => $recordId,
                    "type" => strtoupper($responseDns->type),
                    "name" => $responseDns->host,
                    "content" => $responseDns->data,
                    "system_flag" => 1,
                ]);
            }

            return true;
        } else {
            return false;
        }
    }

    public function integrationShopify(int $domainModelId, string $domain)
    {
        $domainModel = Domain::find($domainModelId);

        $this->removeDomain($domainModel);
        $this->getDomainRecordModel()
            ->where("domain_id", $domainModelId)
            ->delete();

        //criar o dominio
        $newZone = $this->addZone($domain);

        if ($newZone) {
            //dominio criado

            $domainModel->update([
                "cloudflare_domain_id" => $newZone->id,
            ]);

            $this->setZone($newZone->name);

            $recordId = $this->addRecord("A", $newZone->name, self::shopifyIp, 0, false);
            $this->getDomainRecordModel()->create([
                "domain_id" => $domainModelId,
                "cloudflare_record_id" => $recordId,
                "type" => "A",
                "name" => $newZone->name,
                "content" => self::shopifyIp,
                "system_flag" => 1,
                "proxy" => 0,
            ]);

            $recordId = $this->addRecord("CNAME", "www", "shops.myshopify.com", 0, false);
            $this->getDomainRecordModel()->create([
                "domain_id" => $domainModelId,
                "cloudflare_record_id" => $recordId,
                "type" => "CNAME",
                "name" => "www",
                "content" => "shops.myshopify.com",
                "system_flag" => 1,
                "proxy" => 0,
            ]);

            $recordId = $this->addRecord("CNAME", "checkout", self::checkoutIp);
            $this->getDomainRecordModel()->create([
                "domain_id" => $domainModelId,
                "cloudflare_record_id" => $recordId,
                "type" => "CNAME",
                "name" => "checkout",
                "content" => self::checkoutIp,
                "system_flag" => 1,
            ]);

            $recordId = $this->addRecord("CNAME", "sac", self::sacIp);
            $this->getDomainRecordModel()->create([
                "domain_id" => $domainModelId,
                "cloudflare_record_id" => $recordId,
                "type" => "CNAME",
                "name" => "sac",
                "content" => self::sacIp,
                "system_flag" => 1,
            ]);

            $recordId = $this->addRecord("CNAME", "affiliate", self::affiliateIp);
            $this->getDomainRecordModel()->create([
                "domain_id" => $domainModelId,
                "cloudflare_record_id" => $recordId,
                "type" => "CNAME",
                "name" => "affiliate",
                "content" => self::affiliateIp,
                "system_flag" => 1,
            ]);

            $recordId = $this->addRecord("CNAME", "tracking", self::adminIp);
            $this->getDomainRecordModel()->create([
                "domain_id" => $domainModelId,
                "cloudflare_record_id" => $recordId,
                "type" => "CNAME",
                "name" => "tracking",
                "content" => self::adminIp,
                "system_flag" => 1,
            ]);

            $this->getSendgridService()->deleteZone($newZone->name);
            $sendgridResponse = $this->getSendgridService()->addZone($newZone->name);

            foreach ($sendgridResponse->dns as $responseDns) {
                if ($responseDns->type == "mx") {
                    $recordId = $this->addRecord("MX", $responseDns->host, $responseDns->data, 0, false, "1");
                    $this->getDomainRecordModel()->create([
                        "domain_id" => $domainModelId,
                        "cloudflare_record_id" => $recordId,
                        "type" => "MX",
                        "name" => $responseDns->host,
                        "content" => $responseDns->data,
                        "system_flag" => 1,
                        "proxy" => 0,
                    ]);
                } else {
                    $recordId = $this->addRecord(
                        strtoupper($responseDns->type),
                        $responseDns->host,
                        $responseDns->data,
                        0,
                        false,
                    );
                    $this->getDomainRecordModel()->create([
                        "domain_id" => $domainModelId,
                        "cloudflare_record_id" => $recordId,
                        "type" => strtoupper($responseDns->type),
                        "name" => $responseDns->host,
                        "content" => $responseDns->data,
                        "system_flag" => 1,
                        "proxy" => 0,
                    ]);
                }
            }

            $this->getSendgridService()->deleteLinkBrand($newZone->name);
            $linkBrandResponse = $this->getSendgridService()->createLinkBrand($newZone->name);

            if (!empty($linkBrandResponse)) {
                foreach ($linkBrandResponse->dns as $responseDns) {
                    $recordId = $this->addRecord(
                        strtoupper($responseDns->type),
                        $responseDns->host,
                        $responseDns->data,
                        0,
                        false,
                    );
                    $this->getDomainRecordModel()->create([
                        "domain_id" => $domainModelId,
                        "cloudflare_record_id" => $recordId,
                        "type" => strtoupper($responseDns->type),
                        "name" => $responseDns->host,
                        "content" => $responseDns->data,
                        "system_flag" => 1,
                        "proxy" => 0,
                    ]);
                }
            } else {
                return false;
            }

            return true;
        } else {
            return false;
        }
    }

    public function checkHtmlMetadata($url, $metaName, $metaContent): bool
    {
        try {
            $client = new Client([
                'base_uri' => $url,
                'timeout' => 10,
                'connect_timeout' => 10,
                'Accept' => 'application/json',
            ]);

            $response = $client->request("get", "/");

            if ($response->getStatusCode() !== ResponseAlias::HTTP_OK) {
                return false;
            }

            $data = $response->getBody()->getContents();
            $dom = new Dom();
            $dom->load($data);
            $metas = $dom->find("meta");

            foreach ($metas as $meta) {
                if ($meta->getAttribute("name") === $metaName &&
                    $meta->getAttribute("content") === $metaContent
                ) {
                    return true;
                }
            }

            return false;
        } catch (Throwable $e) {
            if ($e instanceof ConnectException || $e instanceof RequestException) {
                return false;
            }

            report($e);
            return false;
        }
    }

    /**
     * @param  string  $domain
     * @return array|false|string
     */
    public function getSSLSetting(string $domain)
    {
        try {
            if ($domain) {
                $zoneID = $this->zones->getZoneID($domain);
            } else {
                $zoneID = $this->zoneID;
            }

            if (empty($zoneID)) {
                return [];
            } else {
                return $this->ssl->getSSLSetting($zoneID);
            }
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }

    /**
     * @param  string  $value
     * @param  string|null  $domain
     * @return array|bool
     */
    public function setSSLSetting(string $value, string $domain = null)
    {
        try {
            if ($domain) {
                $zoneID = $this->zones->getZoneID($domain);
            } else {
                $zoneID = $this->zoneID;
            }

            if (!empty($zoneID)) {
                return $this->ssl->updateSSLSetting($zoneID, $value);
            } else {
                return [];
            }
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }

    /**
     * @param  string  $value
     * @param  string|null  $domain
     * @return array|bool
     */
    public function setHTTPSRedirectSetting(string $value, string $domain = null)
    {
        try {
            if ($domain) {
                $zoneID = $this->zones->getZoneID($domain);
            } else {
                $zoneID = $this->zoneID;
            }

            if (!empty($zoneID)) {
                return $this->ssl->updateHTTPSRedirectSetting($zoneID, $value);
            } else {
                return [];
            }
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }

    /**
     * @param  string  $value
     * @param  string|null  $domain
     * @return array|bool
     */
    public function setHTTPSRewritesSetting(string $value, string $domain = null)
    {
        try {
            if ($domain) {
                $zoneID = $this->zones->getZoneID($domain);
            } else {
                $zoneID = $this->zoneID;
            }

            if (!empty($zoneID)) {
                return $this->ssl->updateHTTPSRewritesSetting($zoneID, $value);
            } else {
                return [];
            }
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }

    /**
     * @param  string|null  $domain
     * @return array|bool
     */
    public function enableTLS13(string $domain = null)
    {
        try {
            if ($domain) {
                $zoneID = $this->zones->getZoneID($domain);
            } else {
                $zoneID = $this->zoneID;
            }

            if (!empty($zoneID)) {
                return $this->tls->enableTLS13($zoneID);
            } else {
                return [];
            }
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }

    /**
     * @param  string  $value
     * @param  string|null  $domain
     * @return array|bool
     */
    public function setOpportunisticEncryptionSetting(string $value, string $domain = null)
    {
        try {
            if ($domain) {
                $zoneID = $this->zones->getZoneID($domain);
            } else {
                $zoneID = $this->zoneID;
            }

            if (!empty($zoneID)) {
                return $this->crypto->updateOpportunisticEncryptionSetting($zoneID, $value);
            } else {
                return [];
            }
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }

    /**
     * @param  string  $value
     * @param  string|null  $domain
     * @return array|bool
     */
    public function setOnionRoutingSetting(string $value, string $domain = null)
    {
        try {
            if ($domain) {
                $zoneID = $this->zones->getZoneID($domain);
            } else {
                $zoneID = $this->zoneID;
            }

            if (!empty($zoneID)) {
                return $this->crypto->updateOnionRoutingSetting($zoneID, $value);
            } else {
                return [];
            }
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }

    /**
     * @param  string|null  $domain
     * @param  array  $options
     * @return bool
     */
    public function setCloudFlareConfig(string $domain = null, array $options = [])
    {
        /*
        Configuracoes Default
        Always Use HTTPS  - ON
        Authenticated Origin Pulls - OFF
        Opportunistic Encryption  - ON
        Onion Routing - ON
        TLS 1.3 - Enabled
        Automatic HTTPS Rewrites - ON
        */

        try {
            if ($domain) {
                $zoneID = $this->zones->getZoneID($domain);
            } else {
                $zoneID = $this->zoneID;
            }

            if (empty($options)) {
                $options = [
                    "ssl" => "flexible",
                    "always_use_https" => "on",
                    "origin_pulls" => "off",
                    "opportunistic_encryption" => "on",
                    "onion_routing" => "on",
                    "tls13" => "on",
                    "automatic_https_rewrites" => "on",
                ];
            }

            if (!empty($options["ssl"])) {
                $this->ssl->updateSSLSetting($zoneID, $options["ssl"]);
            }

            if (!empty($options["always_use_https"])) {
                $this->ssl->updateHTTPSRedirectSetting($zoneID, $options["always_use_https"]);
            }

            if (!empty($options["origin_pulls"])) {
                $this->tls->updateTLSClientAuth($zoneID, $options["origin_pulls"]);
            }

            if (!empty($options["opportunistic_encryption"])) {
                $this->crypto->updateOpportunisticEncryptionSetting($zoneID, $options["opportunistic_encryption"]);
            }

            if (!empty($options["onion_routing"])) {
                $this->crypto->updateOnionRoutingSetting($zoneID, $options["onion_routing"]);
            }

            if (!empty($options["tls13"])) {
                $this->tls->enableTLS13($zoneID);
            }

            if (!empty($options["automatic_https_rewrites"])) {
                $this->ssl->updateHTTPSRewritesSetting($zoneID, $options["automatic_https_rewrites"]);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function removeDomain(Domain $domain)
    {
        try {
            $this->setZone($domain->name);
        } catch (Exception $e) {
            return;
        }

        $getRecords = $this->getRecords($domain->name);

        foreach ($getRecords as $getRecord) {
            $this->deleteRecord($getRecord->id);
        }

        if (!foxutils()->isEmpty($domain->cloudflare_domain_id)) {
            $this->deleteZoneById($domain->cloudflare_domain_id);
        } else {
            $this->deleteZone($domain->name);
        }

        try {
            $this->getSendgridService()->deleteLinkBrand($domain->name);
            $this->getSendgridService()->deleteZone($domain->name);
        } catch (Exception $e) {
        }
    }

    public function setSecurityLevel(string $zoneId, string $level): bool
    {
        if (!in_array($level, ["off", "essentially_off", "low", "medium", "high", "under_attack"])) {
            return false;
        }
        $return = $this->adapter->patch("zones/".$zoneId."/settings/security_level", [
            "value" => $level,
        ]);

        $body = json_decode($return->getBody());

        if ($body->success) {
            return true;
        }

        return false;
    }

    /**
     * Sync DNS records for all zones with new constant values and update database records
     * 
     * @param bool $isDryRun If true, only logs what would change without making actual updates
     * @return array Array containing results of the update operation
     */
    public function updateAllZonesDNSRecords(bool $isDryRun = false): array
    {
        $results = [];
        $updateMap = [
            'checkout' => self::checkoutIp,
            'sac' => self::sacIp,
            'affiliate' => self::affiliateIp,
            'tracking' => self::adminIp
        ];

        Log::channel('cloudflare')->info('Starting DNS record update process', [
            'dry_run' => $isDryRun,
            'update_map' => $updateMap
        ]);

        try {
            // Get all zones
            $zones = $this->getZones();
            Log::channel('cloudflare')->info('Retrieved zones from CloudFlare', [
                'zone_count' => count($zones)
            ]);
            
            foreach ($zones as $zone) {
                $zoneResult = [
                    'zone' => $zone->name,
                    'updated_records' => [],
                    'errors' => []
                ];

                Log::channel('cloudflare')->info('Processing zone', [
                    'zone_name' => $zone->name,
                    'zone_id' => $zone->id
                ]);

                try {
                    // Find corresponding domain in database
                    $domain = Domain::where('name', $zone->name)->first();
                    if (!$domain) {
                        Log::channel('cloudflare')->warning('Domain not found in database', [
                            'zone_name' => $zone->name
                        ]);
                        continue;
                    }

                    // Get all DNS records for the zone
                    $records = $this->getRecords($zone->name);
                    Log::channel('cloudflare')->info('Retrieved DNS records', [
                        'zone_name' => $zone->name,
                        'record_count' => count($records)
                    ]);
                    
                    foreach ($records as $record) {
                        // Check if the record is one we want to update
                        if (isset($updateMap[$record->name]) && $record->type === 'CNAME') {
                            $newContent = $updateMap[$record->name];
                            
                            // Only update if content is different
                            if ($record->content !== $newContent) {
                                Log::channel('cloudflare')->info('Found record to update', [
                                    'zone_name' => $zone->name,
                                    'record_name' => $record->name,
                                    'old_content' => $record->content,
                                    'new_content' => $newContent
                                ]);

                                if (!$isDryRun) {
                                    $updateDetails = [
                                        'type' => $record->type,
                                        'name' => $record->name,
                                        'content' => $newContent,
                                        'proxied' => $record->proxied
                                    ];

                                    // Attempt to update the record in Cloudflare
                                    $updateSuccess = $this->updateRecordDetails(
                                        $zone->id,
                                        $record->id,
                                        $updateDetails
                                    );

                                    if ($updateSuccess) {
                                        // Update corresponding record in database
                                        $domainRecord = DomainRecord::where([
                                            'domain_id' => $domain->id,
                                            'cloudflare_record_id' => $record->id
                                        ])->first();

                                        if ($domainRecord) {
                                            $domainRecord->update([
                                                'content' => $newContent
                                            ]);
                                        }

                                        $zoneResult['updated_records'][] = [
                                            'name' => $record->name,
                                            'old_content' => $record->content,
                                            'new_content' => $newContent
                                        ];
                                        
                                        Log::channel('cloudflare')->info('Successfully updated record', [
                                            'zone_name' => $zone->name,
                                            'record_name' => $record->name,
                                            'new_content' => $newContent
                                        ]);
                                    } else {
                                        $errorMessage = "Failed to update {$record->name} record";
                                        $zoneResult['errors'][] = $errorMessage;
                                        
                                        Log::channel('cloudflare')->error('Failed to update record', [
                                            'zone_name' => $zone->name,
                                            'record_name' => $record->name,
                                            'error' => $errorMessage
                                        ]);
                                    }
                                }
                            } else {
                                Log::channel('cloudflare')->info('Record content already up to date', [
                                    'zone_name' => $zone->name,
                                    'record_name' => $record->name,
                                    'content' => $record->content
                                ]);
                            }
                        }
                    }
                } catch (Exception $e) {
                    $errorMessage = "Error processing records: " . $e->getMessage();
                    $zoneResult['errors'][] = $errorMessage;
                    
                    Log::channel('cloudflare')->error('Error processing zone records', [
                        'zone_name' => $zone->name,
                        'error' => $errorMessage,
                        'trace' => $e->getTraceAsString()
                    ]);
                }

                $results[] = $zoneResult;
            }

            Log::channel('cloudflare')->info('Completed DNS update process', [
                'total_zones' => count($zones),
                'results' => $results
            ]);

            return [
                'success' => true,
                'results' => $results
            ];
        } catch (Exception $e) {
            $error = "Failed to fetch zones: " . $e->getMessage();
            
            Log::channel('cloudflare')->error('Fatal error during DNS update process', [
                'error' => $error,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $error
            ];
        }
    }
}
