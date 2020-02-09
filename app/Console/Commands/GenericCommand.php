<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\ProjectNotificationService;
use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;


/**
 * Class GenericCommand
 * @package App\Console\Commands
 */
class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generic:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @throws PresenterException
     */
    public function handle()
    {

        $transactionModel = new Transaction();
        $transferModel    = new Transfer();

        $transactions = $transactionModel->where([
            ['release_date', '>=', Carbon::now()->subDays('5')->format('Y-m-d')],
            ['status', 'transfered']
        ])
        ->whereHas('transfers', null, '>', 1);

        $totalValue = 0;
        $realValue = 0;
        $wrongValue = 0;

        foreach($transactions->cursor() as $key => $transaction){

            $value = 0;
            foreach($transaction->transfers as $key => $transfer){
                $totalValue += $transfer->value;

                if($key > 0){
                    $value += $transfer->value;
                }
                else{
                    $realValue += $transfer->value;
                }
            }

            $wrongValue += $value;

            $company = $transaction->company;

            $company->update([
                'balance' => intval($company->balance) - intval($value),
            ]);

            $transfer = $transferModel->create([
                'user_id'        => $company->user_id,
                'company_id'     => $company->id,
                'type_enum'      => $transferModel->present()->getTypeEnum('out'),
                'value'          => $value,
                'type'           => 'out',
                'reason'         => 'Múltiplas transferências da transação #' . Hashids::connection('sale_id')->encode($transaction->sale_id)
            ]);
        }

        dd(
            number_format(intval($totalValue) / 100, 2, ',', '.'),
            number_format(intval($realValue) / 100, 2, ',', '.'),
            number_format(intval($wrongValue) / 100, 2, ',', '.')
           );
    }

}
