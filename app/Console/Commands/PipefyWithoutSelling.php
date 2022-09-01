<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\Core\Entities\User;
use Modules\Core\Services\Pipefy\PipefyService;

class PipefyWithoutSelling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pipefy:without-selling';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pipefy without selling';

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
        $labelArray = [PipefyService::LABEL_TOP_SALE];
        $start  = Carbon::now()->subDays(30);
        $end    = Carbon::now();

        $userFirstSale = User::selectRaw("users.id, sum(sales.original_total_paid_value) as total_sale, users.pipefy_card_id")
            ->join("sales", "sales.owner_id", "users.id")
            ->whereRaw("users.account_is_approved = ".User::STATUS_ACTIVE." and users.created_at < sales.created_at")
            ->whereRaw("users.pipefy_card_id is not null") //verificar se vamos fazer essa busca apenas para quem esta com ard criado
            ->havingRaw("total_sale >= 10000000")
            ->whereBetween("sales.created_at", [$start, $end]) // verificar se vai ser dentro de um periodo
            ->groupBy("sales.owner_id");

        //montar um arry com os dados do card para colocar no campo pipefy_card
        $json = ["teste" => "xxxx", "key" => ["teste","test1","teste3"],"bla" => "bla2"];
        $teste = json_encode($json);

        dd(json_decode($teste));

        dd($userFirstSale->toSql());

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
