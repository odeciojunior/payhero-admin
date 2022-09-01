<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\Core\Entities\User;
use Modules\Core\Services\Pipefy\PipefyService;

class PipefyTopSale extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pipefy:top-sale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pipefy top sale';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $pipefyService = new PipefyService();
        $pipefyService->createCardUser(User::find(19));
        dd("qqq");
        $labelArray = [PipefyService::LABEL_TOP_SALE];
        $start  = Carbon::now()->subDays(120);
        $end    = Carbon::now();

        $userFirstSale = User::selectRaw("users.id, sum(sales.original_total_paid_value) as total_sale, users.pipefy_card")
            ->join("sales", "sales.owner_id", "users.id")
            ->whereRaw("users.account_is_approved = ".User::STATUS_ACTIVE." and users.created_at < sales.created_at")
            ->whereRaw("users.pipefy_card is not null") //verificar se vamos fazer essa busca apenas para quem esta com card criado
            ->havingRaw("total_sale >= 10000000")
            ->whereBetween("sales.created_at", [$start, $end]) // verificar se vai ser dentro de um periodo
            ->groupBy("sales.owner_id")
            ->get();

        foreach ($userFirstSale as $user){
            if (empty($user->pipefy_card)) { //verificar se vamos fazer essa busca apenas para quem esta com ard criado
                $userCreateCard = $pipefyService->createCardUser(User::find($user->id));
                $pipefyService->updateCardLabel($userCreateCard, $labelArray);
            }else{
                $pipefyService->updateCardLabel(User::find($user->id), $labelArray);
            }
        }
        return true;
    }
}
