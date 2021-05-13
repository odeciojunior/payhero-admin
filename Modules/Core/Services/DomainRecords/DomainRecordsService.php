<?php

namespace Modules\Core\Services\DomainRecords;

use Modules\Core\Entities\Domain;
use Modules\Core\Entities\DomainRecord;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\DomainService;

class DomainRecordsService
{
    private Domain $domain;
    private DomainRecord $domainRecordModel;
    private CloudFlareService $cloudflareService;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
        $this->cloudflareService = new CloudFlareService();
        $this->domainRecordModel = new DomainRecord();
    }

    public function verifyRecordDelete(DomainRecord $record)
    {
        try {
            $domainExist = $this->verifyDomainExistCloudFlare();

            if (!$domainExist) {
                return (new DomainService())->deleteDomain($this->domain);
            }

            if (!$this->cloudflareService->deleteRecord($record->cloudflare_record_id)) {
                return [
                    'message' => 'Ocorreu um erro ao tentar deletar a entrada!',
                    'success' => false,
                ];
            }

            return $this->deleteRecord($record);
        } catch (\Exception $e) {
            report($e);

            return [
                'message' => 'Ocorreu um erro!',
                'success' => false
            ];
        }
    }

    public function verifyDomainExistCloudFlare(): bool
    {
        try {
            if (empty($this->cloudflareService->getZones($this->domain->name))) {
                return false;
            }

            $zoneId = $this->cloudflareService->setZone($this->domain->name);
            if (empty($zoneId)) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            report($e);

            return false;
        }
    }

    public function deleteRecord(DomainRecord $record)
    {
        try {
            $recordsFind = $this->domainRecordModel->where('id', $record->id)->first();
            $recordsDeleted = $recordsFind->delete();

            if ($recordsDeleted) {
                return [
                    'message' => 'DNS removido com sucesso!',
                    'success' => true,
                ];
            }

            return [
                'message' => 'Ocorreu um erro ao tentar remover entrada!',
                'success' => false,
            ];
        } catch (\Exception $e) {
            report($e);
            return [
                'message' => 'Ocorreu um erro ao tentar remover entrada!',
                'success' => false,
            ];
        }
    }
}