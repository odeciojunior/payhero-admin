<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\CheckoutGateway;
use Symfony\Component\VarDumper\VarDumper;

class AsaasHelper extends Command
{
    protected $signature = "asaas-helper";

    protected $description = "Consultas na api do Asaas pelo checkout";

    private $api = null;

    public function __construct()
    {
        parent::__construct();
        $this->api = new CheckoutGateway(
            FoxUtils::isProduction() ? Gateway::ASAAS_PRODUCTION_ID : Gateway::ASAAS_SANDBOX_ID
        );
    }

    public function handle()
    {
        try {
            $this->listOptions();

            while (true) {
                $option = $this->ask("Digite uma opção");
                $this->menu($option);
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    public function listOptions()
    {
        if (FoxUtils::isProduction()) {
            $this->question("===== ASAAS (Production) =====");
        } else {
            $this->error("===== ASAAS (Sandbox) =====");
        }
        $this->comment("[1] Listar opções");
        $this->comment("[2] Company Transfers ");
        $this->comment("[3] Company Transfer");
        $this->comment("[4] Company Balance");
        $this->comment("[5] Azcend Balance");
        $this->comment("[6] Company Anticipation");
        $this->comment("[7] Company Anticipations");
        $this->comment("[8] Company Receivables Reserves");
        $this->comment("[9] Update Company Balance Extract");
        $this->comment("[0] Sair");
        $this->comment("===========================");
        $this->comment("[2964,3442] - João, Dani");
        $this->comment("===========================");
    }

    public function menu($option)
    {
        switch ($option) {
            case 1:
                $this->listOptions();
                break;
            case 2:
                $this->getTransfers();
                break;
            case 3:
                $this->getTransfer();
                break;
            case 4:
                $this->getCompanyBalance();
                break;
            case 5:
                $this->getCloudfoxBalance();
                break;
            case 6:
                $this->getCompanyAntipation();
                break;
            case 7:
                $this->getCompanyAntipations();
                break;
            case 8:
                $this->getReceivablesReserves();
                break;
            case 9:
                $this->updateAsaasBalance();
                break;
            case 0:
                $this->info("Bye!");
                exit();
                break;
            default:
                $this->comment("Opção inválida");
                break;
        }
    }

    public function getTransfers()
    {
        $companyId = $this->anticipate("Informe CompanyId", ["2964", "3442"]);
        VarDumper::dump($this->api->getTransfersAsaas($companyId) ?? []);
    }

    public function getTransfer()
    {
        list($companyId, $transferId) = explode(",", $this->ask("Informe CompanyId,transferId"));

        VarDumper::dump($this->api->getTransferAsaas($companyId, $transferId) ?? []);
    }

    public function getCompanyBalance()
    {
        $companyId = $this->anticipate("Informe CompanyId", ["2964", "3442"]);
        VarDumper::dump($this->api->getCurrentBalance($companyId) ?? []);
    }

    public function getCloudfoxBalance()
    {
        VarDumper::dump($this->api->getCurrentBalance() ?? []);
    }

    public function getCompanyAntipation()
    {
        list($companyId, $anticipationId) = explode(",", $this->ask("Informe CompanyId,anticipationId"));

        VarDumper::dump($this->api->getAnticipationAsaas($companyId, $anticipationId) ?? []);
    }

    public function getCompanyAntipations()
    {
        $companyId = $this->anticipate("Informe CompanyId", ["2964", "3442"]);

        VarDumper::dump($this->api->getAnticipationsAsaas($companyId) ?? []);
    }

    public function getReceivablesReserves()
    {
        $companyId = $this->anticipate("Informe CompanyId", ["2964", "3442"]);
        $filters = [
            "startDate" => "2021-11-01",
            "finishDate" => now(),
        ];
        $response = $this->api->getReceivablesReserves($companyId, $filters);

        VarDumper::dump($response->total);
    }

    public function updateAsaasBalance()
    {
        $companyId = $this->anticipate("Informe CompanyId", ["2964", "3442"]);
        $company = Company::find($companyId);
        $gatewayService = new AsaasService();
        $gatewayService->setCompany($company);

        $filters = [
            "date_type" => "transfer_date",
            "date_range" => "01/01/2018 - " . Date("d/m/Y"),
            "reason" => "",
            "transaction" => "",
            "type" => "",
            "value" => "",
        ];
        $balance = $gatewayService->getPeriodBalance($filters) ?? 0;
        VarDumper::dump([
            "fantasy_name" => $company->fantasy_name,
            "real_balance" => $balance * 100,
            "asaas_balance" => $company->asaas_balance,
        ]);

        $atualiza = $this->ask("Deseja corrigir [y/n]");
        if ($atualiza == "y") {
            $company->update([
                "asaas_balance" => $balance * 100,
            ]);
        }
    }
}
