<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Services\SaleService;

class CheckUsersChargebacks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $startDate;

    public function handle()
    {
        $this->startDate = now()
            ->startOfDay()
            ->subDays(150);

        $users = User::where('contestation_rate', '>', '1.5')->orderBy('contestation_rate', 'desc')->get();

        foreach ($users as $user) {

            $contestationsCount = $this->getContestationsCount($user);
            $chargebacksCount = $this->getChargebacksCount($user);
            $approvedsCount = $this->getApprovedsCount($user);

            $this->line($user->name);
            $this->line('Vendas aprovadas: ' . $approvedsCount);
            $this->line('Contestações: ' . $contestationsCount . '/' . $approvedsCount . ' - ' . $user->contestation_rate . '%');
            $this->line('Chargebacks: ' . $chargebacksCount . '/' . $approvedsCount . ' - ' . $user->chargeback_rate . '%');
            $this->line('---------------------------------------------------');
        }

    }

    public function getContestationsCount(User $user)
    {
        return Sale::whereIn("status", [
            Sale::STATUS_APPROVED,
            Sale::STATUS_CHARGEBACK,
            Sale::STATUS_REFUNDED,
            Sale::STATUS_IN_DISPUTE,
        ])
            ->where("start_date", ">", $this->startDate->format("Y-m-d") . " 00:00:00")
            ->where(function ($query) use ($user) {
                $query->where("owner_id", $user->id)->orWhere("affiliate_id", $user->id);
            })
            ->whereHas("contestations")
            ->count();
    }

    public function getChargebacksCount(User $user)
    {
        return Sale::where("status", Sale::STATUS_CHARGEBACK)
            ->where(function ($query) use ($user) {
                $query->where("owner_id", $user->id)->orWhere("affiliate_id", $user->id);
            })
            ->where("created_at", ">=", $this->startDate->format("Y-m-d") . " 00:00:00")
            ->count();
    }

    public function getApprovedsCount(User $user)
    {
        $endDate = now()
        ->endOfDay()
        ->subDays(20);

        return (new SaleService())->getCreditCardApprovedSalesInPeriod($user, $this->startDate, $endDate);
    }
}
