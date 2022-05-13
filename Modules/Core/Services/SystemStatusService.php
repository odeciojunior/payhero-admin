<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleUnderAttack;
use Modules\Core\Entities\UnderAttack;

class SystemStatusService
{
    private const SALES_ANALYZED_AMOUNT = 30;
    private const BANK_SLIP_ERROR_LIMIT = 28;
    private const CREDIT_CARD_ERROR_LIMIT = 28;
    private const SUCCESS_MESSAGE = 'Sistema funcionando normalmente';
    private const WARNING_MESSAGE = 'Problemas com o sistema';
    private const UNDER_ATTACK_MESSAGE = 'Possível ataque ao sistema';
    private const BANK_SLIP_ERROR_MESSAGE = 'Possível erro com os boletos bancários';
    private const CREDIT_CARD_ERROR_MESSAGE = 'Possível erro com os pagamentos no cartão de crédito';

    public function checkSystems(): array
    {
        return [
            'checkout' => $this->getCheckoutStatus(),
            'app' => $this->getAppStatus(),
            'sac' => $this->getSacStatus()
        ];
    }

    private function checkAttacks(): array
    {
        $sales = Sale::with(['project.domains', 'customer'])
            ->where('attempts', '>=', UnderAttack::MAX_ATTEMPT)
            ->where('updated_at', '>=', now()->subHours(1))
            ->get();

        $domains = [];
        $result = [];
        foreach ($sales as $sale) {
            $domain = $sale->project->domains->where('status', 3)->first();
            $domainName = '';
            if ($domain) {
                $domains[$domain->id] = $domain;
                $domainName = $domain->name;
            }

            $result[] = [
                'sale_code' => hashids_encode($sale->id, 'sale_id'),
                'sale_id' => $sale->id,
                'project' => $sale->project->name,
                'domain' => $domainName,
                'customer' => $sale->customer->name,
                'attempts' => $sale->attempts,
            ];
        }

        $cloudFlareService = new CloudFlareService();
        foreach ($domains as $domain) {
            $underAttack = UnderAttack::where('domain_id', $domain->id)
                ->where('type', 'DOMAIN')
                ->whereNull('removed_at')
                ->first();

            if (empty($underAttack->id)) {
                if ($cloudFlareService->setSecurityLevel($domain->cloudflare_domain_id, 'under_attack')) {
                    $underAttack = UnderAttack::create(['domain_id' => $domain->id]);
                }
            }

            $salesDomain = $sales->where('project_id', $domain->project_id);
            foreach ($salesDomain as $sale) {
                SaleUnderAttack::firstOrCreate([
                    'sale_id' => $sale->id,
                    'under_attack_id' => $underAttack->id,
                ]);
            }
        }

        return $result;
    }

    private function getCheckoutStatus(): array
    {
        $system = ['status' => '', 'messages' => [], 'attacks' => []];

        $response = $this->call('https://checkout.cloudfox.net/');

        if ($response['status_code'] != 200) {
            $system['status'] = 'warning';
            $system['messages'][] = self::WARNING_MESSAGE;
        }

        $bankSlipSales = Sale::where('payment_method', Sale::BOLETO_PAYMENT)
            ->orderByDesc('id')
            ->limit(self::SALES_ANALYZED_AMOUNT)
            ->get();

        $bankSlipErrors = $bankSlipSales->where('status', Sale::STATUS_SYSTEM_ERROR)->count();

        if ($bankSlipErrors >= self::BANK_SLIP_ERROR_LIMIT) {
            $system['status'] = 'danger';
            $system['messages'][] = self::BANK_SLIP_ERROR_MESSAGE;
        }

        $creditCardSales = Sale::where('payment_method', Sale::CREDIT_CARD_PAYMENT)
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        $creditCardErrors = $creditCardSales->where('status', Sale::STATUS_SYSTEM_ERROR)->count();

        if ($creditCardErrors >= self::CREDIT_CARD_ERROR_LIMIT) {
            $system['status'] = 'danger';
            $system['messages'][] = self::CREDIT_CARD_ERROR_MESSAGE;
        }

        $attacks = $this->checkAttacks();
        if (count($attacks)) {
            $system['status'] = 'danger';
            $system['messages'][] = self::UNDER_ATTACK_MESSAGE;
            $system['attacks'] = $attacks;
        }

        $attacks_card_refused = $this->checkCardAttacks();

        if (count($attacks_card_refused)) {
            $system['attacks_card_refused'] = $attacks_card_refused;
        }

        if (empty($system['status'])) {
            $system['status'] = 'success';
            $system['messages'][] = self::SUCCESS_MESSAGE;
        }

        return $system;
    }

    private function getAppStatus(): array
    {
        $system = ['status' => '', 'messages' => [], 'attacks' => []];

        $response = $this->call('https://sirius.cloudfox.net');

        if (!in_array($response['status_code'], [200, 302])) {
            $system['status'] = 'warning';
            $system['messages'][] = self::WARNING_MESSAGE;
        }

        if (empty($system['status'])) {
            $system['status'] = 'success';
            $system['messages'][] = self::SUCCESS_MESSAGE;
        }

        return $system;
    }

    private function getSacStatus(): array
    {
        $data = [
            'status' => 'success',
            'messages' => [],
            'attacks' => []
        ];
        $data['messages'][] = self::SUCCESS_MESSAGE;
        return $data;

        $system = ['status' => '', 'messages' => [], 'attacks' => []];

        $response = $this->call('https://sac.cloudfox.net/api/project');

        if ($response['status_code'] != 200) {
            $system['status'] = 'warning';
            $system['messages'][] = self::WARNING_MESSAGE;
        }

        if (empty($system['status'])) {
            $system['status'] = 'success';
            $system['messages'][] = self::SUCCESS_MESSAGE;
        }

        return $system;
    }

    private function call($url, $data = null, $method = 'GET', $headers = null): array
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        $method = strtoupper($method);
        switch ($method) {
            case 'GET':
                if ($data) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
        }

        if ($headers) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);

        $body = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

        curl_close($curl);

        return [
            'status_code' => $statusCode,
            'content_type' => $contentType,
            'body' => $body,
        ];
    }

    private function checkCardAttacks()
    {
        return UnderAttack::select('users.name',
            'under_attacks.percentage_card_refused',
            'under_attacks.start_date_card_refused',
            'under_attacks.end_date_card_refused',
            'under_attacks.total_refused',
        )->join('users', 'users.id', '=', 'user_id')
            ->where('type', 'CARD_DECLINED')
            ->get();
    }
}