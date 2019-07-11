<?php

namespace Modules\Core\Services;

use App\Entities\DomainRecord;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Adapter\Guzzle;
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
    public function integrationWebsite(int $domainModelId, string $domain, string $ipAddress)
    {
        $this->deleteZone($domain);
        $this->getDomainRecordModel()->where('domain_id', $domainModelId)->delete();

        //criar o dominio
        $newZone = $this->addZone($domain);

        if ($newZone) {
            //dominio criado

            $this->setZone($newZone->name);
            $this->addRecord("A", $newZone->name, $ipAddress);
            $this->getDomainRecordModel()->create([
                                                      'domain_id'   => $domainModelId,
                                                      'type'        => 'A',
                                                      'name'        => $newZone->name,
                                                      'content'     => $ipAddress,
                                                      'system_flag' => 1,
                                                  ]);

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
                                     'base_uri' => $url,
                                     //'headers'  => $headers,
                                     'Accept'   => 'application/json',
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
}
