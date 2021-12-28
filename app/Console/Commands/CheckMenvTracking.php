<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\MelhorenvioIntegration;
use Modules\Core\Entities\Tracking;
use Modules\Core\Events\CheckSaleHasValidTrackingEvent;
use Modules\Core\Services\MelhorenvioService;

class CheckMenvTracking extends Command
{
    protected $signature = 'check:menv-tracking';

    protected $description = 'Check if sales using Melhor Envio already have a tracking code';

    public function handle()
    {
        $deliveriesQuery = DB::table('deliveries as d')
            ->join('sales as s', 'd.id', '=', 's.delivery_id')
            ->join('products_plans_sales as pps', 's.id', '=', 'pps.sale_id')
            ->join('shippings as sh', 's.shipping_id', '=', 'sh.id')
            ->join('melhorenvio_integrations as mi', 'sh.melhorenvio_integration_id', '=', 'mi.id')
            ->where('d.updated_at', '>=', now()->subDays(21))
            ->whereNotNull('d.melhorenvio_order_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('trackings as t')
                    ->where('t.product_plan_sale_id', DB::raw('pps.id'));
            })->select([
                's.id as sale_id',
                'd.id as delivery_id',
                'pps.id as pps_id',
                'pps.product_id',
                'pps.amount as amount',
                'd.melhorenvio_order_id',
                'mi.access_token',
                'mi.refresh_token',
                'mi.expiration as token_expiration'
            ])->orderByDesc('d.id');

        $total = $deliveriesQuery->count();

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $deliveriesQuery->chunk(250, function ($deliveries) use ($bar) {
            foreach ($deliveries as $delivery) {
                try {
                    $integration = new MelhorenvioIntegration([
                        'access_token' => $delivery->access_token,
                        'refresh_token' => $delivery->refresh_token,
                        'expiration' => $delivery->token_expiration,
                    ]);
                    $service = new MelhorenvioService($integration);

                    $orders = json_decode($delivery->melhorenvio_order_id);

                    foreach ($orders as $orderId) {
                        $order = $service->getOrder($orderId);

                        if (!empty($order) && !empty($order->status) && in_array($order->status, ['posted', 'delivered'])) {

                            Tracking::updateOrCreate([
                                'sale_id' => $delivery->sale_id,
                                'product_id' => $delivery->product_id,
                                'product_plan_sale_id' => $delivery->pps_id,
                                'amount' => $delivery->amount,
                                'delivery_id' => $delivery->delivery_id,
                                'tracking_code' => $order->tracking,
                                'tracking_status_enum' => $order->status === 'delivered' ? Tracking::STATUS_DELIVERED : Tracking::STATUS_POSTED,
                                'system_status_enum' => Tracking::SYSTEM_STATUS_VALID,
                            ]);

                            event(new CheckSaleHasValidTrackingEvent($delivery->sale_id));
                        }
                    }

                } catch (\Exception $e) {
                    $this->error('ERROR: ' . $e->getMessage());
                    report($e);
                }
                sleep(1);
                $bar->advance();
            }
        });

        $bar->finish();
    }
}
