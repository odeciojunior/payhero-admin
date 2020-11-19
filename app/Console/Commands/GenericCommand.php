<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Services\CheckoutService;

class GenericCommand extends Command
{
    protected $signature = 'generic {user?}';

    protected $description = 'Command description';

    public function handle()
    {

        $usersInformation = DB::select('select * from user_informations where mother_name is not null or sex is not null');

        foreach ($usersInformation as $userInformation) {
            $user = User::find($userInformation->user_id);

            if (!empty($userInformation->sex)) {
                $user->update(['sex' => $userInformation->sex]);
            }

            if (!empty($userInformation->mother_name)) {
                $user->update(['mother_name' => $userInformation->mother_name]);
            }
        }
//        if (env('APP_ENV') == 'local') {
//            print('INICIO - Documentos Usuario: ' . PHP_EOL);
//            foreach(UserDocument::orderBy('id','DESC')->take(10)->get() as $key => $user){
//                $this->line('Documentos Usuario: ' . $key);
//                $user->update([
//                    'status' => 3
//                ]);
//            }
//
//            foreach (User::orderBy('id','DESC')->take(10)->get() as $key => $user) {
//                $user->update([
//                    'address_document_status' => 3,
//                    'personal_document_status' => 3
//                ]);
//            }
//            print('FIM - Documentos Usuario: ' . PHP_EOL . PHP_EOL);
//
//            print('INICIO - Documentos Empresa: ' . PHP_EOL);
//            foreach (Company::orderBy('id','DESC')->take(10)->get() as $key => $user) {
//                $user->update([
//                    'contract_document_status' => 3,
//                    'bank_document_status' => 3,
//                    'address_document_status' => 3,
//                    'capture_transaction_enabled' => 1
//                ]);
//            }
//
//            foreach(CompanyDocument::orderBy('id','DESC')->take(10)->get() as $key => $user){
//                $this->line('Documentos Empresa: ' . $key);
//                $user->update([
//                    'status' => 3
//                ]);
//            }
//            print('FIM -Documentos Empresa: ' . PHP_EOL);
//        } else {
//            print('Este comando só pode rodar no amibiente local ' . PHP_EOL);
//        }

//        try {
//            $salesModel = new Sale();
//            $trackingPresenter = (new Tracking())->present();
//            $productPresenter = (new Product())->present();
//            $checkoutService = new CheckoutService();
//
//            $salesQuery = $salesModel::with([
//                'productsPlansSale.tracking',
//                'productsPlansSale.product',
//            ])->whereIn('status', [1, 4])
//                ->where('has_valid_tracking', false)
//                ->whereHas('productsPlansSale', function ($query) use ($productPresenter) {
//                    $query->whereHas('product', function ($query) use ($productPresenter) {
//                        $query->where('type_enum', $productPresenter->getType('physical'));
//                    });
//                })
//                ->where('start_date', '>=', '2020-10-16 00:00:00')
//                ->orderByDesc('id');
//
//            $total = $salesQuery->count();
//            $count = 1;
//
//            $salesQuery->chunk(60,
//                function ($sales) use ($total, &$count, $checkoutService, $trackingPresenter, $productPresenter) {
//                    foreach ($sales as $sale) {
//                        $this->line("Verificando venda {$count} de {$total}: {$sale->id}...");
//                        try {
//                            foreach ($sale->productsPlansSale as $pps) {
//                                if ($pps->product->type_enum == $productPresenter->getType('physical')) {
//                                    $hasInvalidOrNotInformedTracking = is_null($pps->tracking) || !in_array($pps->tracking->system_status_enum,
//                                            [
//                                                $trackingPresenter->getSystemStatusEnum('valid'),
//                                                $trackingPresenter->getSystemStatusEnum('checked_manually'),
//                                            ]);
//                                    if ($hasInvalidOrNotInformedTracking) {
//                                        break;
//                                    }
//                                }
//                            }
//
//                            if (!$hasInvalidOrNotInformedTracking) {
//                                $sale->has_valid_tracking = true;
//                                $sale->save();
//                                $checkoutService->releasePaymentGetnet($sale->id);
//
//                                $this->info("Venda liberada!");
//                            } else {
//                                $this->line("Venda ainda não liberada!");
//                            }
//                        } catch (Exception $e) {
//                            $this->error('ERROR:'.$e->getMessage());
//                        }
//                        $count++;
//                    }
//                    $this->warn('Aguardando 60 segundos...');
//                    sleep(60);
//                });
//        } catch (Exception $e) {
//            report($e);
//        }
    }
}


