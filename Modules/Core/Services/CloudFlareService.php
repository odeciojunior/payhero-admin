<?php

namespace Modules\Core\Services;

use Cloudflare\API\Endpoints\EndpointException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Modules\Core\Entities\Domain;
use PHPHtmlParser\Dom;
use Illuminate\Http\Response;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\SSL;
use Cloudflare\API\Endpoints\TLS;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\User;
use Cloudflare\API\Endpoints\Zones;
use Illuminate\Support\Facades\Log;
use Cloudflare\API\Endpoints\Crypto;
use Modules\Core\Entities\DomainRecord;
use stdClass;

/**
 * Class CloudFlareService
 * @package Modules\Core\Services
 */
class CloudFlareService
{
    const shopifyIp   = '23.227.38.32';
    const checkoutIp  = '104.248.234.121';
    const sacIp       = '104.248.122.89';
    const affiliateIp = '104.248.122.89';
    const adminIp     = '165.22.13.237';
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
    private function getSendgridService()
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
            $this->key     = new APIKey(getenv('CLOUDFLARE_EMAIL'), getenv('CLOUDFLARE_TOKEN'));
            $this->adapter = new Guzzle($this->key);
            $this->dns     = new DNS($this->adapter);
            $this->ssl     = new SSL($this->adapter);
            $this->tls     = new TLS($this->adapter);
            $this->crypto  = new Crypto($this->adapter);
            $this->zones   = new Zones($this->adapter);
            $this->user    = new User($this->adapter);
        } catch (Exception $e) {
            Log::warning('__construct - Erro ao criar servico do cloudflare');
            report($e);
        }
    }

    /**
     * @param string $domain
     * @return $this
     * @throws EndpointException
     */
    public function zone(string $domain)
    {
        $this->zoneID = $this->zones->getZoneID($domain);

        return $this;
    }

    /**
     * @param string $domain
     * @return string
     * @throws EndpointException
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
        $zones = $this->zones->listZones($name, '', 1, 1000);

        return $zones->result;
    }

    /**
     * @param string $zone
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
            // Log::warning('Erro ao remover dominio (dominio inexistente)');
            // report($e);

            return false;
        }
    }

    /**
     * @param string $zoneId
     * @return bool
     */
    public function deleteZoneById(string $zoneId)
    {
        try {
            $user = $this->adapter->delete('zones/' . $zoneId);

            $body = json_decode($user->getBody());

            if (isset($body->result->id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            // Log::warning('Erro ao remover dominio (dominio inexistente)');
            // report($e);

            return false;
        }
    }

    /**
     * @param string|null $domain
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
     * @param string $type    | A, CNAME, AAAA
     * @param string $name    | dominio, subdominio
     * @param string $content | IP, dominio
     * @param int $ttl
     * @param bool $proxied
     * @return bool
     */
    public function addRecord(string $type, string $name, string $content, int $ttl = 0, bool $proxied = true, $priority = '0')
    {
        //        if ($this->dns->addRecord($this->zoneID, $type, $name, $content, $ttl, $proxied, $priority) === true) {
        //            return true;
        //        } else {
        //            return false;
        //        }

        $options = [
            'type'    => $type,
            'name'    => $name,
            'content' => $content,
            'proxied' => $proxied,
        ];

        if ($ttl > 0) {
            $options['ttl'] = $ttl;
        }

        if (!empty($priority)) {
            $options['priority'] = (int) $priority;
        }

        if (!empty($data)) {
            $options['data'] = $data;
        }

        $user = $this->adapter->post('zones/' . $this->zoneID . '/dns_records', $options);

        $this->body = json_decode($user->getBody());

        if (isset($this->body->result->id)) {
            return $this->body->result->id;
        }

        return [];
    }

    /**
     * @param string $zoneID
     * @param string $recordID
     * @param array $details
     * @return array
     */
    public function updateRecordDetails(string $zoneID, string $recordID, array $details)
    {
        try {

            if (!empty($zoneID) && !empty($recordID) && !empty($details)) {
                $response = $this->adapter->put('zones/' . $zoneID . '/dns_records/' . $recordID, $details);
                $body     = json_decode($response->getBody());

                return $body;
            } else {
                return false;
            }
        } catch (Exception $e) {
            Log::warning('Erro ao remover record (record inexistente)');
            report($e);

            return false;
        }
    }

    /**
     * @param string $recordId
     * @return bool
     */
    public function deleteRecord(string $recordId)
    {
        try {

            $user = $this->adapter->delete('zones/' . $this->zoneID . '/dns_records/' . $recordId);

            $body = json_decode($user->getBody());

            if (isset($body->result->id)) {
                return true;
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
     * @param int $domainModelId
     * @param string $domain
     * @param $ipAddress
     * @return bool
     * @throws EndpointException
     */
    public function integrationWebsite(int $domainModelId, string $domain, $ipAddress)
    {
        $domainModel = new Domain();

        $this->deleteZone($domain);
        $this->getDomainRecordModel()->where('domain_id', $domainModelId)->delete();

        //criar o dominio
        $newZone = $this->addZone($domain);

        if ($newZone) {
            //dominio criado

            $domainModel->where('id', $domainModelId)
                        ->update([
                                     'cloudflare_domain_id' => $newZone->id,
                                 ]);

            $this->setZone($newZone->name);

            if (!empty($ipAddress)) {
                $recordId = $this->addRecord("A", $newZone->name, $ipAddress);
                $this->getDomainRecordModel()->create([
                                                          'domain_id'            => $domainModelId,
                                                          'cloudflare_record_id' => $recordId,
                                                          'type'                 => 'A',
                                                          'name'                 => $newZone->name,
                                                          'content'              => $ipAddress,
                                                          'system_flag'          => 1,
                                                      ]);
            }

            $recordId = $this->addRecord("CNAME", 'www', $newZone->name);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'            => $domainModelId,
                                                      'cloudflare_record_id' => $recordId,
                                                      'type'                 => 'CNAME',
                                                      'name'                 => 'www',
                                                      'content'              => $newZone->name,
                                                      'system_flag'          => 1,
                                                  ]);

            $recordId = $this->addRecord("A", 'checkout', self::checkoutIp);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'            => $domainModelId,
                                                      'cloudflare_record_id' => $recordId,
                                                      'type'                 => 'A',
                                                      'name'                 => 'checkout',
                                                      'content'              => self::checkoutIp,
                                                      'system_flag'          => 1,
                                                  ]);

            $recordId = $this->addRecord("A", 'sac', self::sacIp);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'            => $domainModelId,
                                                      'cloudflare_record_id' => $recordId,
                                                      'type'                 => 'A',
                                                      'name'                 => 'sac',
                                                      'content'              => self::sacIp,
                                                      'system_flag'          => 1,
                                                  ]);

            $recordId = $this->addRecord("A", 'affiliate', self::affiliateIp);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'            => $domainModelId,
                                                      'cloudflare_record_id' => $recordId,
                                                      'type'                 => 'A',
                                                      'name'                 => 'affiliate',
                                                      'content'              => self::affiliateIp,
                                                      'system_flag'          => 1,
                                                  ]);

            $recordId = $this->addRecord("A", 'tracking', self::adminIp);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'            => $domainModelId,
                                                      'cloudflare_record_id' => $recordId,
                                                      'type'                 => 'A',
                                                      'name'                 => 'tracking',
                                                      'content'              => self::adminIp,
                                                      'system_flag'          => 1,
                                                  ]);

            $this->getSendgridService()->deleteZone($newZone->name);
            $sendgridResponse = $this->getSendgridService()->addZone($newZone->name);

            foreach ($sendgridResponse->dns as $responseDns) {
                if ($responseDns->type == 'mx') {
                    $recordId = $this->addRecord('MX', $responseDns->host, $responseDns->data, 0, false, '1');
                    $this->getDomainRecordModel()->create([
                                                              'domain_id'            => $domainModelId,
                                                              'cloudflare_record_id' => $recordId,
                                                              'type'                 => 'MX',
                                                              'name'                 => $responseDns->host,
                                                              'content'              => $responseDns->data,
                                                              'system_flag'          => 1,
                                                          ]);
                } else {
                    $recordId = $this->addRecord(strtoupper($responseDns->type), $responseDns->host, $responseDns->data, 0, false);
                    $this->getDomainRecordModel()->create([
                                                              'domain_id'            => $domainModelId,
                                                              'cloudflare_record_id' => $recordId,
                                                              'type'                 => strtoupper($responseDns->type),
                                                              'name'                 => $responseDns->host,
                                                              'content'              => $responseDns->data,
                                                              'system_flag'          => 1,
                                                          ]);
                }
            }

            $this->getSendgridService()->deleteLinkBrand($newZone->name);
            $linkBrandResponse = $this->getSendgridService()->createLinkBrand($newZone->name);

            foreach ($linkBrandResponse->dns as $responseDns) {
                $recordId = $this->addRecord(strtoupper($responseDns->type), $responseDns->host, $responseDns->data, 0, false);
                $this->getDomainRecordModel()->create([
                                                          'domain_id'            => $domainModelId,
                                                          'cloudflare_record_id' => $recordId,
                                                          'type'                 => strtoupper($responseDns->type),
                                                          'name'                 => $responseDns->host,
                                                          'content'              => $responseDns->data,
                                                          'system_flag'          => 1,
                                                      ]);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $domainModelId
     * @param string $domain
     * @return bool
     * @throws EndpointException
     */
    public function integrationShopify(int $domainModelId, string $domain)
    {
        $domainModel = new Domain();

        $this->deleteZone($domain);
        $this->getDomainRecordModel()->where('domain_id', $domainModelId)->delete();

        //criar o dominio
        $newZone = $this->addZone($domain);

        if ($newZone) {
            //dominio criado

            $domainModel->where('id', $domainModelId)
                        ->update([
                                     'cloudflare_domain_id' => $newZone->id,
                                 ]);

            $this->setZone($newZone->name);

            $recordId = $this->addRecord("A", $newZone->name, self::shopifyIp, 0, false);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'            => $domainModelId,
                                                      'cloudflare_record_id' => $recordId,
                                                      'type'                 => 'A',
                                                      'name'                 => $newZone->name,
                                                      'content'              => self::shopifyIp,
                                                      'system_flag'          => 1,
                                                  ]);

            $recordId = $this->addRecord("CNAME", 'www', 'shops.myshopify.com');
            $this->getDomainRecordModel()->create([
                                                      'domain_id'            => $domainModelId,
                                                      'cloudflare_record_id' => $recordId,
                                                      'type'                 => 'CNAME',
                                                      'name'                 => 'www',
                                                      'content'              => 'shops.myshopify.com',
                                                      'system_flag'          => 1,
                                                  ]);

            $recordId = $this->addRecord("A", 'checkout', self::checkoutIp);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'            => $domainModelId,
                                                      'cloudflare_record_id' => $recordId,
                                                      'type'                 => 'A',
                                                      'name'                 => 'checkout',
                                                      'content'              => self::checkoutIp,
                                                      'system_flag'          => 1,
                                                  ]);

            $recordId = $this->addRecord("A", 'sac', self::sacIp);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'            => $domainModelId,
                                                      'cloudflare_record_id' => $recordId,
                                                      'type'                 => 'A',
                                                      'name'                 => 'sac',
                                                      'content'              => self::sacIp,
                                                      'system_flag'          => 1,
                                                  ]);

            $recordId = $this->addRecord("A", 'affiliate', self::affiliateIp);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'            => $domainModelId,
                                                      'cloudflare_record_id' => $recordId,
                                                      'type'                 => 'A',
                                                      'name'                 => 'affiliate',
                                                      'content'              => self::affiliateIp,
                                                      'system_flag'          => 1,
                                                  ]);

            $recordId = $this->addRecord("A", 'tracking', self::adminIp);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'            => $domainModelId,
                                                      'cloudflare_record_id' => $recordId,
                                                      'type'                 => 'A',
                                                      'name'                 => 'tracking',
                                                      'content'              => self::adminIp,
                                                      'system_flag'          => 1,
                                                  ]);

            $this->getSendgridService()->deleteZone($newZone->name);
            $sendgridResponse = $this->getSendgridService()->addZone($newZone->name);

            foreach ($sendgridResponse->dns as $responseDns) {
                if ($responseDns->type == 'mx') {
                    $recordId = $this->addRecord('MX', $responseDns->host, $responseDns->data, 0, false, '1');
                    $this->getDomainRecordModel()->create([
                                                              'domain_id'            => $domainModelId,
                                                              'cloudflare_record_id' => $recordId,
                                                              'type'                 => 'MX',
                                                              'name'                 => $responseDns->host,
                                                              'content'              => $responseDns->data,
                                                              'system_flag'          => 1,
                                                          ]);
                } else {
                    $recordId = $this->addRecord(strtoupper($responseDns->type), $responseDns->host, $responseDns->data, 0, false);
                    $this->getDomainRecordModel()->create([
                                                              'domain_id'            => $domainModelId,
                                                              'cloudflare_record_id' => $recordId,
                                                              'type'                 => strtoupper($responseDns->type),
                                                              'name'                 => $responseDns->host,
                                                              'content'              => $responseDns->data,
                                                              'system_flag'          => 1,
                                                          ]);
                }
            }

            $this->getSendgridService()->deleteLinkBrand($newZone->name);
            $linkBrandResponse = $this->getSendgridService()->createLinkBrand($newZone->name);

            if (!empty($linkBrandResponse)) {
                foreach ($linkBrandResponse->dns as $responseDns) {
                    $recordId = $this->addRecord(strtoupper($responseDns->type), $responseDns->host, $responseDns->data, 0, false);
                    $this->getDomainRecordModel()->create([
                                                              'domain_id'            => $domainModelId,
                                                              'cloudflare_record_id' => $recordId,
                                                              'type'                 => strtoupper($responseDns->type),
                                                              'name'                 => $responseDns->host,
                                                              'content'              => $responseDns->data,
                                                              'system_flag'          => 1,
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

    /**
     * @param $url
     * @param $metaName
     * @param $metaContent
     * @return bool
     * @throws GuzzleException
     */
    public function checkHtmlMetadata($url, $metaName, $metaContent)
    {
        try {
            $client = new Client([
                                     'base_uri'        => $url,
                                     'timeout'         => 0,
                                     'connect_timeout' => 0,
                                     //'headers'  => $headers,
                                     'Accept'          => 'application/json',
                                 ]);

            $response = $client->request('get', '/');

            if ($response->getStatusCode() == Response::HTTP_OK) {
                $data = $response->getBody()->getContents();
                $dom  = new Dom;
                $dom->load($data);
                $metas = $dom->find('meta');

                foreach ($metas as $meta) {
                    if (($meta->getAttribute('name') == $metaName) &&
                        ($meta->getAttribute('content') == $metaContent)) {
                        return true;
                    }
                }

                return false;
            } else {
                return false;
            }
        } catch (Exception $e) {

            return false;
        }
    }

    /**
     * @param string $domain
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
            Log::warning('Erro ao ver configuração de SSL');
            report($e);

            return [];
        }
    }

    /**
     * @param string $value
     * @param null $domain
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
            Log::warning('Erro ao ver configuração de SSL');
            report($e);

            return [];
        }
    }

    /**
     * @param string $value
     * @param null $domain
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
            Log::warning('Erro ao configurar HTTPSRedirectS');
            report($e);

            return [];
        }
    }

    /**
     * @param string $value
     * @param string|null $domain
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
            Log::warning('Erro ao configurar HTTPSRewrites');
            report($e);

            return [];
        }
    }

    /**
     * @param string|null $domain
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
            Log::warning('Erro ao configurar TLS 1.3');
            report($e);

            return [];
        }
    }

    /**
     * @param string $value
     * @param string|null $domain
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
            Log::warning('Erro ao configurar OpportunisticEncryption');
            report($e);

            return [];
        }
    }

    /**
     * @param string $value
     * @param string|null $domain
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
            Log::warning('Erro ao configurar OnionRouting');
            report($e);

            return [];
        }
    }

    /**
     * @param string|null $domain
     * @param array $options
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
                    'ssl'                      => 'flexible',
                    'always_use_https'         => 'on',
                    'origin_pulls'             => 'off',
                    'opportunistic_encryption' => 'on',
                    'onion_routing'            => 'on',
                    'tls13'                    => 'on',
                    'automatic_https_rewrites' => 'on',
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
            Log::warning('Erro ao fazer confiuracao default do cloudflare');
            report($e);

            return false;
        }
    }
}
