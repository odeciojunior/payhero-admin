<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Withdrawal;

class ReleaseWithdrawalsWithGetFasterBenefit extends Command
{
    protected $signature = 'withdrawals:release-get-faster';

    protected $description = "Libera o saque dos usuário que possuem o benefício 'Receba + rápido' habilitado";
    private $gatewayIds = [

        Gateway::GETNET_PRODUCTION_ID,
        Gateway::ASAAS_PRODUCTION_ID,
        Gateway::GERENCIANET_PRODUCTION_ID,
        Gateway::SAFE2PAY_PRODUCTION_ID

    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        try {

            $withdrawals = Withdrawal::selectRaw('withdrawals.id')
                ->join('companies as c', 'c.id', '=', 'withdrawals.company_id')
                ->join('users as u', 'u.id', '=', 'c.user_id')
                ->whereIn('withdrawals.status', [Withdrawal::STATUS_PENDING, Withdrawal::STATUS_IN_REVIEW])
                ->where('u.get_faster', 1)
                ->whereIn('withdrawals.gateway_id',$this->gatewayIds)
                ->whereNull('c.deleted_at')
                ->whereNull('u.deleted_at')
                ->orderBy('withdrawals.id')
                ->get()->toArray();

            $managerUrl = env('MANAGER_URL', 'http://dev.manager.com.br') . '/api/withdrawals/release/withdrawalsgetfaster';

            foreach ($withdrawals as $withdrawal) {
                try {
                    $this->runCurl($managerUrl, 'POST', ['withdrawal_id' => hashids_encode($withdrawal['id'])]);
                } catch (Exception $e) {
                    report($e);
                }
            }

        } catch (Exception $e) {
            report($e);
        }

    }

    /**
     * @throws Exception
     */
    private function runCurl($url, $method = 'GET', $data = null): void
    {
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            if ($method == "POST") {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, foxutils()->getHeadersInternalAPI());
            $result = curl_exec($ch);
            json_decode($result);
            return;
        } catch (Exception $ex) {
            report($ex);
            throw $ex;
        }
    }
}
