<?php

namespace App\Console\Commands\Sac;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\Transaction;
use Modules\Core\Events\Sac\NotifyTicketOpenEvent;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\Gateways\Safe2PayService;

class AutomaticTicketCreation extends Command
{
    protected $signature = "create:ticket";

    protected $description = "Cria chamados automaticamente para vendas sem rastreamento";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $daysWithoutTracking = 15;

        $sales = Sale::select([
            "sales.id",
            "sales.customer_id",
            DB::raw("sum(transactions.value) as transaction_value"),
            "transactions.company_id",
            "companies.safe2pay_balance",
        ])
            ->leftJoin("trackings", "trackings.sale_id", "=", "sales.id")
            ->leftJoin("tickets", "tickets.sale_id", "=", "sales.id")
            ->join("users", "users.id", "=", "sales.owner_id")
            ->join("transactions", function ($join) {
                $join
                    ->on("transactions.sale_id", "=", "sales.id")
                    ->where("transactions.type", Transaction::TYPE_PRODUCER);
            })
            ->join("companies", "companies.id", "=", "transactions.company_id")
            ->where("sales.status", Sale::STATUS_APPROVED)
            ->where("sales.start_date", "<", now()->subDays($daysWithoutTracking))
            ->where("sales.start_date", ">", "2022-05-19 15:00:00")
            ->where("users.block_attendance_balance", true)
            ->whereNull("trackings.id")
            ->whereNull("tickets.id")
            ->whereNotNull("sales.delivery_id")
            ->groupBy("sales.id", "sales.customer_id", "transactions.company_id", "companies.safe2pay_balance");

        $total = $sales->count();
        $bar = $this->getOutput()->createProgressBar($total);
        $bar->start();

        $sales->chunk(500, function ($sales) use ($daysWithoutTracking, $bar) {
            foreach ($sales as $sale) {
                try {
                    $ticket = Ticket::create([
                        "sale_id" => $sale->id,
                        "customer_id" => $sale->customer_id,
                        "subject" => "Código de rastreio não informado",
                        "subject_enum" => Ticket::SUBJECT_TRACKING_CODE_NOT_RECEIVED,
                        "description" => "Chamado criado automáticamente para venda a mais de {$daysWithoutTracking} dias sem código de rastreio",
                        "ticket_category_enum" => Ticket::CATEGORY_COMPLAINT,
                        "ticket_status_enum" => Ticket::STATUS_OPEN,
                        "mediation_notified" => 0,
                    ]);
                    event(new NotifyTicketOpenEvent($ticket->id));

                    $allowBlock = $this->allowBlockBalance($sale);

                    $blockStatus = $allowBlock
                        ? BlockReasonSale::STATUS_BLOCKED
                        : BlockReasonSale::STATUS_PENDING_BLOCK;
                    BlockReasonSale::create([
                        "sale_id" => $ticket->sale_id,
                        "blocked_reason_id" => BlockReasonSale::BLOCK_REASON_ID_TICKET,
                        "status" => $blockStatus,
                        "observation" => "Chamado aberto",
                    ]);
                } catch (\Exception $e) {
                    report($e);
                }

                $bar->advance();
            }
        });

        $bar->finish();
    }

    private function allowBlockBalance(object $sale): bool
    {
        $safe2payService = new Safe2PayService();

        $company = new Company([
            "id" => $sale->company_id,
            "safe2pay_balance" => $sale->safe2pay_balance,
        ]);

        $safe2payService->setCompany($company);
        $availableBalance = $safe2payService->getAvailableBalance();
        $pendingBalance = $safe2payService->getPendingBalance();
        (new CompanyService())->applyBlockedBalance($safe2payService, $availableBalance, $pendingBalance);

        return $availableBalance + $pendingBalance >= $sale->transaction_value;
    }
}
