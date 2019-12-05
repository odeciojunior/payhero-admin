<?php

namespace App\Listeners\Modules\Core\Listeners;

use App\Events\Modules\Core\Events\SaleApprovedEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\HotzappIntegration;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Services\HotZappService;
use Exception;
use Illuminate\Support\Facades\Log;

class HotZappCardApprovedSaleListener implements ShouldQueue
{
    use Queueable;

    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param SaleApprovedEvent $event
     */
    public function handle(SaleApprovedEvent $event)
    {
        try {
            $hotZappIntegrationModel = new HotZappIntegration();
            $planSaleModel           = new PlanSale();
            $planModel               = new Plan();

            if ($event->sale->payment_method == 1 || $event->sale->payment_method == 3) {
                $hotzappIntegration = $hotZappIntegrationModel->where('project_id', $event->plan->project)
                                                              ->where('credit_card_paid', 1)->first();
            } else {
                $hotzappIntegration = $hotZappIntegrationModel->where('project_id', $event->plan->project)
                                                              ->where('boleto_paid', 1)->first();
            }

            if (!empty($hotzappIntegration)) {

                $hotZappService = new HotZappService($hotzappIntegration->link);

                $plansSale = $planSaleModel->where('sale_id', $event->sale->id)->get();

                $plans = [];
                foreach ($plansSale as $planSale) {

                    $plan = $planModel->find($planSale->plan_id);

                    $plans[] = [
                        "price"        => $plan->price,
                        "quantity"     => $planSale->amount,
                        "product_name" => $plan->name,
                    ];
                }

                if ($event->sale->payment_method == 1 || $event->sale->payment_method == 3) {
                    $hotZappService->creditCardPaid($event->sale, $plans);
                } else {
                    $hotZappService->boletoPaid($event->sale, $plans);
                }
            }
        } catch (Exception $e) {
            Log::warning('erro ao enviar notificação pro HotZapp na venda ' . $event->sale->id);
            report($e);
        }
    }
}



