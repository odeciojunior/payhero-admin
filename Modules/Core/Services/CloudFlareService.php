<?php

namespace Modules\Core\Services;

use App\Entities\DomainRecord;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\Crypto;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\SSL;
use Cloudflare\API\Endpoints\TLS;
use Cloudflare\API\Endpoints\User;
use Cloudflare\API\Endpoints\Zones;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Exception;
use GuzzleHttp\Client;
use PHPHtmlParser\Dom;

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
     * @return DomainRecord|\Illuminate\Contracts\Foundation\Application|mixed
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
            // Log::warning('Erro ao remover dominio (dominio inexistente)');
            // report($e);

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
    public function addRecord(string $type, string $name, string $content, int $ttl = 0, bool $proxied = true, $priority = '0')
    {
        if ($this->dns->addRecord($this->zoneID, $type, $name, $content, $ttl, $proxied, $priority) === true) {
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
     * @param string|null $ipAddress
     * @return bool
     * @throws \Cloudflare\API\Endpoints\EndpointException
     */
    public function integrationWebsite(int $domainModelId, string $domain, $ipAddress)
    {
        $this->deleteZone($domain);
        $this->getDomainRecordModel()->where('domain_id', $domainModelId)->delete();

        //criar o dominio
        $newZone = $this->addZone($domain);

        if ($newZone) {
            //dominio criado

            $this->setZone($newZone->name);

            if (!empty($ipAddress)) {
                $this->addRecord("A", $newZone->name, $ipAddress);
                $this->getDomainRecordModel()->create([
                                                          'domain_id'   => $domainModelId,
                                                          'type'        => 'A',
                                                          'name'        => $newZone->name,
                                                          'content'     => $ipAddress,
                                                          'system_flag' => 1,
                                                      ]);
            }

            $this->addRecord("CNAME", 'www', $newZone->name);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'   => $domainModelId,
                                                      'type'        => 'CNAME',
                                                      'name'        => 'www',
                                                      'content'     => $newZone->name,
                                                      'system_flag' => 1,
                                                  ]);

            $this->addRecord("A", 'checkout', self::checkoutIp);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'   => $domainModelId,
                                                      'type'        => 'A',
                                                      'name'        => 'checkout',
                                                      'content'     => self::checkoutIp,
                                                      'system_flag' => 1,
                                                  ]);

            $this->addRecord("A", 'sac', self::sacIp);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'   => $domainModelId,
                                                      'type'        => 'A',
                                                      'name'        => 'sac',
                                                      'content'     => self::sacIp,
                                                      'system_flag' => 1,
                                                  ]);

            $this->addRecord("A", 'affiliate', self::affiliateIp);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'   => $domainModelId,
                                                      'type'        => 'A',
                                                      'name'        => 'affiliate',
                                                      'content'     => self::affiliateIp,
                                                      'system_flag' => 1,
                                                  ]);

            $this->getSendgridService()->deleteZone($newZone->name);
            $sendgridResponse = $this->getSendgridService()->addZone($newZone->name);

            foreach ($sendgridResponse->dns as $responseDns) {
                if ($responseDns->type == 'mx') {
                    $this->addRecord('MX', $responseDns->host, $responseDns->data, 0, false, '1');
                    $this->getDomainRecordModel()->create([
                                                              'domain_id'   => $domainModelId,
                                                              'type'        => 'MX',
                                                              'name'        => $responseDns->host,
                                                              'content'     => $responseDns->data,
                                                              'system_flag' => 1,
                                                          ]);
                } else {
                    $this->addRecord(strtoupper($responseDns->type), $responseDns->host, $responseDns->data, 0, false);
                    $this->getDomainRecordModel()->create([
                                                              'domain_id'   => $domainModelId,
                                                              'type'        => strtoupper($responseDns->type),
                                                              'name'        => $responseDns->host,
                                                              'content'     => $responseDns->data,
                                                              'system_flag' => 1,
                                                          ]);
                }
            }

            $this->getSendgridService()->deleteLinkBrand($newZone->name);
            $linkBrandResponse = $this->getSendgridService()->createLinkBrand($newZone->name);

            foreach ($linkBrandResponse->dns as $responseDns) {
                $this->addRecord(strtoupper($responseDns->type), $responseDns->host, $responseDns->data, 0, false);
                $this->getDomainRecordModel()->create([
                                                          'domain_id'   => $domainModelId,
                                                          'type'        => strtoupper($responseDns->type),
                                                          'name'        => $responseDns->host,
                                                          'content'     => $responseDns->data,
                                                          'system_flag' => 1,
                                                      ]);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $domain
     * @return bool
     * @throws \Cloudflare\API\Endpoints\EndpointException
     */
    public function integrationShopify(int $domainModelId, $domain)
    {
        $this->deleteZone($domain);
        $this->getDomainRecordModel()->where('domain_id', $domainModelId)->delete();

        //criar o dominio
        $newZone = $this->addZone($domain);

        if ($newZone) {
            //dominio criado

            $this->setZone($newZone->name);

            $this->addRecord("A", $newZone->name, self::shopifyIp, 0, false);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'   => $domainModelId,
                                                      'type'        => 'A',
                                                      'name'        => $newZone->name,
                                                      'content'     => self::shopifyIp,
                                                      'system_flag' => 1,
                                                  ]);

            $this->addRecord("CNAME", 'www', 'shops.myshopify.com');
            $this->getDomainRecordModel()->create([
                                                      'domain_id'   => $domainModelId,
                                                      'type'        => 'CNAME',
                                                      'name'        => 'www',
                                                      'content'     => 'shops.myshopify.com',
                                                      'system_flag' => 1,
                                                  ]);

            $this->addRecord("A", 'checkout', self::checkoutIp);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'   => $domainModelId,
                                                      'type'        => 'A',
                                                      'name'        => 'checkout',
                                                      'content'     => self::checkoutIp,
                                                      'system_flag' => 1,
                                                  ]);

            $this->addRecord("A", 'sac', self::sacIp);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'   => $domainModelId,
                                                      'type'        => 'A',
                                                      'name'        => 'sac',
                                                      'content'     => self::sacIp,
                                                      'system_flag' => 1,
                                                  ]);

            $this->addRecord("A", 'affiliate', self::affiliateIp);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'   => $domainModelId,
                                                      'type'        => 'A',
                                                      'name'        => 'affiliate',
                                                      'content'     => self::affiliateIp,
                                                      'system_flag' => 1,
                                                  ]);

            $this->getSendgridService()->deleteZone($newZone->name);
            $sendgridResponse = $this->getSendgridService()->addZone($newZone->name);

            foreach ($sendgridResponse->dns as $responseDns) {
                if ($responseDns->type == 'mx') {
                    $this->addRecord('MX', $responseDns->host, $responseDns->data, 0, false, '1');
                    $this->getDomainRecordModel()->create([
                                                              'domain_id'   => $domainModelId,
                                                              'type'        => 'MX',
                                                              'name'        => $responseDns->host,
                                                              'content'     => $responseDns->data,
                                                              'system_flag' => 1,
                                                          ]);
                } else {
                    $this->addRecord(strtoupper($responseDns->type), $responseDns->host, $responseDns->data, 0, false);
                    $this->getDomainRecordModel()->create([
                                                              'domain_id'   => $domainModelId,
                                                              'type'        => strtoupper($responseDns->type),
                                                              'name'        => $responseDns->host,
                                                              'content'     => $responseDns->data,
                                                              'system_flag' => 1,
                                                          ]);
                }
            }

            $this->getSendgridService()->deleteLinkBrand($newZone->name);
            $linkBrandResponse = $this->getSendgridService()->createLinkBrand($newZone->name);

            foreach ($linkBrandResponse->dns as $responseDns) {
                $this->addRecord(strtoupper($responseDns->type), $responseDns->host, $responseDns->data, 0, false);
                $this->getDomainRecordModel()->create([
                                                          'domain_id'   => $domainModelId,
                                                          'type'        => strtoupper($responseDns->type),
                                                          'name'        => $responseDns->host,
                                                          'content'     => $responseDns->data,
                                                          'system_flag' => 1,
                                                      ]);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $url
     * @param $meta
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkHtmlMetadata($url, $metaName, $metaContent)
    {
        try {
            $client = new Client([
                                     'base_uri'        => $url,
                                     'timeout'         => 10,
                                     'connect_timeout' => 10,
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
            Log::warning('Erro ao checar dominio');
            report($e);

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
     * @param null $domain
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
     * @param null $domain
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
     * @param null $domain
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
     * @param null $domain
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
     * @param array $options
     * @param string|null $domain
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
