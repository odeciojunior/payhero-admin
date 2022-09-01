<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Services\Pipefy\PipefyService;

class PipefyFirstSale extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pipefy:first-sale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pipefy first sale';

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
        $labelArray = [PipefyService::LABEL_SOLD];

        $userFirstSale = User::selectRaw("users.id, count(sales.id) as total_sale, users.pipefy_card_id")
            ->join("sales", "sales.owner_id", "users.id")
            ->whereRaw("users.pipefy_card_id is not null") //verificar se vamos fazer essa busca apenas para quem esta com card criado
            ->whereRaw("sales.status = 1")
            ->havingRaw("total_sale = 1")
            ->groupBy("sales.owner_id");
//            ->get();


        foreach ($userFirstSale as $user){
            if (empty($user->pipefy_card_id)) { //verificar se vamos fazer essa busca apenas para quem esta com ard criado
                $userCreateCard = $pipefyService->createCardUser(User::find($user->id));
                $pipefyService->updateCardLabel($userCreateCard, $labelArray);
            }else{
                $pipefyService->updateCardLabel(User::find($user->id), $labelArray);
            }
        }
        return true;
    }
}
