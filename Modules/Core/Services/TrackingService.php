<?php

namespace Modules\Core\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\Transaction;
use Vinkla\Hashids\Facades\Hashids;

class TrackingService
{
    /**
     * @var string
     *
     * Data em que migrou da aftership/perfectlog para a trackingmore
     */
    private $migrationDate = '2020-02-03 10:16:00';

    /**
     * @param $trackingCode
     * @return mixed
     */
    public function sendTrackingToApi($trackingCode)
    {
        if (!empty($trackingCode)) {
            $trackingmoreService = new TrackingmoreService();
            return $trackingmoreService->createTracking($trackingCode);
        } else {
            return null;
        }
    }

    /**
     * @param  Tracking  $tracking
     * @return mixed|null
     * @throws PresenterException
     */
    public function findTrackingApi(Tracking $tracking)
    {
        //if de 10k. Busca o código de rastreio na trackingmore caso a data de criação
        //seja posterior a data da migração. Caso contrário busca as informações na
        //perfectlog/aftership
        if ($tracking->created_at->gte($this->migrationDate)) {
            $trackingmoreService = new TrackingmoreService();

            $apiTracking = $trackingmoreService->find($tracking->tracking_code);

            if (isset($apiTracking->status)) {
                $status = $this->parseStatusApi($apiTracking->status);
                if ($tracking->tracking_status_enum != $status) {
                    $tracking->tracking_status_enum = $status;
                    $tracking->save();
                }
            }

            return $apiTracking;
        } else {
            $perfectLogService = new PerfectLogService();

            $apiTracking = $perfectLogService->find($tracking->tracking_code);

            if (isset($apiTracking->tracking_status)) {
                $status = $this->parseStatusApi($apiTracking->tracking_status, true);
                if ($tracking->tracking_status_enum != $status) {
                    $tracking->tracking_status_enum = $status;
                    $tracking->save();
                }
            }

            return $apiTracking;
        }
    }

    /**
     * @param $apiTracking
     * @return mixed
     */
    public function deleteTrackingApi($apiTracking)
    {
        $trackingmoreService = new TrackingmoreService();

        $carrierCode = $apiTracking->carrier_code;

        $trackingNumber = $apiTracking->tracking_number;

        return $trackingmoreService->delete($carrierCode, $trackingNumber);
    }

    /**
     * @param $status
     * @param  bool  $beforeMigration
     * @return int|mixed
     * @throws PresenterException
     */
    public function parseStatusApi($status, $beforeMigration = false)
    {
        //if de 10k. Converte o status da trackingmore caso a data de criação
        //seja posterior a data da migração. Caso contrário converte o status
        //da perfectlog/aftership
        if (!$beforeMigration) {
            $trackingmoreService = new TrackingmoreService();

            return $trackingmoreService->parseStatus($status);
        } else {
            $perfectLogService = new PerfectLogService();

            return $perfectLogService->parseStatus($status);
        }
    }

    /**
     * @param  Tracking  $tracking
     * @param $apiTracking
     * @return Collection
     * @throws PresenterException
     */
    public function getCheckpointsApi(Tracking $tracking, $apiTracking)
    {
        $checkpoints = collect();

        //if de 10k. Trata os checkpoints da trackingmore caso a data de criação
        //seja posterior a data da migração. Caso contrário trata os checkpoints
        //da perfectlog/aftership
        if ($tracking->created_at->gte($this->migrationDate)) {
            $apiCheckpoints = array_reverse($apiTracking->origin_info->trackinfo ?? []);
            $apiCheckpoints += array_reverse($apiTracking->destination_info->trackinfo ?? []);

            if (!empty($apiCheckpoints)) {
                foreach ($apiCheckpoints as $log) {
                    $event = $log->Details ? $log->StatusDescription.' - '.$log->Details : $log->StatusDescription;

                    if (!empty($event)) {
                        $status_enum = $this->parseStatusApi($log->checkpoint_status ?? 'notfound');
                        $status = $status_enum ? __('definitions.enum.tracking.tracking_status_enum.'.$tracking->present()->getTrackingStatusEnum($status_enum)) : 'Não informado';

                        //remove caracteres chineses e informações indesejadas
                        $blacklistWords = [
                            'asendia',
                            'beijing',
                            'chaozhou',
                            'china',
                            'dianhua',
                            'dongguan',
                            'fuyang',
                            'guangdongshengguangzhoushi',
                            'hang kong',
                            'hong kong',
                            'hongkong',
                            'jangxi',
                            'jinhua',
                            'jiangxi',
                            'jingwaijinkou',
                            'kulitiba',
                            'nanchang',
                            'shanghai',
                            'shantou',
                            'shanzhao',
                            'sheng',
                            'shenzhen',
                            'singapore',
                            'sunyou',
                            'xinyu',
                            'yanwen',
                            'yingshangxian',
                            'yiwu',
                            'zhongxin',
                        ];

                        if (Str::contains(strtolower($event),
                                $blacklistWords) || preg_match('/[^\p{Common}\p{Latin}]+/u', $event)) {
                            $event = 'Encomenda em movimentação no exterior';
                        }

                        $checkpoints->add([
                            'tracking_status_enum' => $status_enum,
                            'tracking_status' => $status,
                            'created_at' => Carbon::parse($log->Date)->format('d/m/Y'),
                            'event' => $event,
                        ]);
                    }
                }
            }
        } else {
            if (!empty($apiTracking->trail)) {
                foreach ($apiTracking->trail as $log) {
                    $status_enum = $this->parseStatusApi($log->tracking_status, true);
                    $status = $status_enum ? __('definitions.enum.tracking.tracking_status_enum.'.$tracking->present()->getTrackingStatusEnum($status_enum)) : 'Não informado';

                    $event = $log->event;

                    //remove caracteres chineses e informações indesejadas
                    preg_match('/[^\p{Common}\p{Latin}]+/u', $event, $nonLatinChars);
                    $event = str_replace([
                        'Clique aquiMinhas Importações - ',
                        'CHINA/',
                        'CHINA /',
                        'Paísem'
                    ], '', $event);
                    $event = str_replace([
                        'de País em',
                    ], 'do exterior', $event);

                    $checkpoints->add([
                        'tracking_status_enum' => $status_enum,
                        'tracking_status' => $status,
                        'created_at' => Carbon::parse($log->updated_at)->format('d/m/Y'),
                        'event' => $nonLatinChars ? 'Encomenda em movimentação no exterior' : $event,
                    ]);
                }
            }
        }

        return $checkpoints;
    }

    /**
     * @param  string  $trackingCode
     * @param  ProductPlanSale  $productPlanSale
     * @param  bool  $logging
     * @param  bool  $forceUpdate
     * @return mixed
     */
    public function createOrUpdateTracking(string $trackingCode, ProductPlanSale $productPlanSale, $logging = false, $forceUpdate = false)
    {
        try {
            $trackingService = new TrackingService();
            $trackingModel = new Tracking();

            $logging ? activity()->enableLogging() : activity()->disableLogging();

            $systemStatusEnum = $trackingModel->present()->getSystemStatusEnum('valid');

            $trackingCode = preg_replace('/[^a-zA-Z0-9]/', '', $trackingCode);

            //verifica se já tem uma venda nessa conta com o mesmo código de rastreio
            $sale = $productPlanSale->sale;
            $exists = $trackingModel->where('trackings.tracking_code', $trackingCode)
                ->where('sale_id', '!=', $sale->id)
                ->whereHas('sale', function ($query) use ($sale) {
                    $query->where('upsell_id', '!=', $sale->id)
                        ->where('upsell_id', '!=', $sale->upsell_id);
                })->exists();

            if ($exists) {
                $systemStatusEnum = $trackingModel->present()->getSystemStatusEnum('duplicated');
            }

            $apiResult = $trackingService->sendTrackingToApi($trackingCode);

            if (!empty($apiResult)) {
                //verifica se a data de postagem na transportadora é menor que a data da venda
                if (!empty($apiResult->origin_info)) {
                    $postDate = Carbon::parse($apiResult->origin_info->ItemReceived);
                    if ($postDate->lt($productPlanSale->created_at)) {
                        $systemStatusEnum = $trackingModel->present()->getSystemStatusEnum('posted_before_sale');
                    }
                } else {
                    $systemStatusEnum = $trackingModel->present()->getSystemStatusEnum('no_tracking_info');
                }
                $statusEnum = $trackingService->parseStatusApi($apiResult->status) ?? $trackingModel->present()->getTrackingStatusEnum('posted');
            } else {
                $systemStatusEnum = $trackingModel->present()->getSystemStatusEnum('unknown_carrier');
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum('posted');
            }

            $tracking = $productPlanSale->tracking;

            if (!empty($tracking)) {
                if (($tracking->tracking_code != $trackingCode) || $forceUpdate) {
                    $tracking->update([
                        'tracking_code' => $trackingCode,
                        'tracking_status_enum' => $statusEnum,
                        'system_status_enum' => $systemStatusEnum,
                    ]);
                }
            } else { //senao cria o tracking
                $tracking = $trackingModel->firstOrNew([
                    'sale_id' => $productPlanSale->sale_id,
                    'product_id' => $productPlanSale->product_id,
                    'product_plan_sale_id' => $productPlanSale->id,
                    'amount' => $productPlanSale->amount,
                    'delivery_id' => $productPlanSale->sale->delivery->id,
                    'tracking_code' => $trackingCode,
                    'tracking_status_enum' => $statusEnum,
                    'system_status_enum' => $systemStatusEnum,
                ]);
                $tracking->save();
            }

            return $tracking;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    /**
     * @param $filters
     * @param  int  $userId
     * @return Builder
     * @throws PresenterException
     */
    public function getTrackingsQueryBuilder($filters, $userId = 0)
    {
        $trackingModel = new Tracking();
        $productPlanSaleModel = new ProductPlanSale();
        $salePresenter = (new Sale())->present();

        if (!$userId) {
            $userId = auth()->user()->account_owner_id;
        }

        $saleStatus = [
            $salePresenter->getStatus('approved'),
        ];

        $productPlanSales = $productPlanSaleModel->with(['tracking', 'sale.delivery', 'sale.customer', 'product',]);

        $productPlanSales->whereHas('sale', function ($query) use ($filters, $saleStatus, $userId, $productPlanSales) {
            //tipo da data e periodo obrigatorio
            $dateRange = FoxUtils::validateDateRange($filters["date_updated"]);
            $query->whereBetween('end_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                ->whereIn('status', $saleStatus)
                ->where('owner_id', $userId);

            if (isset($filters['sale'])) {
                $saleId = current(Hashids::connection('sale_id')->decode($filters['sale']));
                $query->where('id', $saleId);
            }

            //filtro transactions
            if (isset($filters['transaction_status'])) {
                $query->whereHas('transactions', function ($queryTransaction) use ($filters) {
                    $transactionPresenter = (new Transaction())->present();
                    if ($filters['transaction_status'] != 'blocked') {
                        $statusEnum = $transactionPresenter->getStatusEnum($filters['transaction_status']);
                        $queryTransaction->where('status_enum', $statusEnum);
                    } else {
                        $queryTransaction->where('transactions.release_date', '>', '2020-05-25') //data que começou a bloquear
                            ->where('transactions.release_date', '<=', Carbon::now()->format('Y-m-d'));
                    }
                    $queryTransaction->where('type', $transactionPresenter->getType('producer'))
                        ->whereNull('invitation_id');
                });
                if ($filters['transaction_status'] == 'blocked') {
                    $productPlanSales->where(function ($query) {
                        $query->whereHas('tracking', function ($trackingsQuery) {
                            $trackingPresenter = (new Tracking)->present();
                            $status = [
                                $trackingPresenter->getSystemStatusEnum('unknown_carrier'),
                                $trackingPresenter->getSystemStatusEnum('no_tracking_info'), //não está bloqueado, está pendente, não transferiu pq aguarda a atualização do código
                                $trackingPresenter->getSystemStatusEnum('posted_before_sale'),
                                $trackingPresenter->getSystemStatusEnum('duplicated'),
                            ];
                            $trackingsQuery->whereIn('system_status_enum', $status);
                        })->orDoesntHave('tracking');
                    });
                }
            }
        });

        if (isset($filters['status'])) {
            if ($filters['status'] === 'unknown') {
                $productPlanSales->doesntHave('tracking');
            } else {
                $productPlanSales->whereHas(
                    'tracking',
                    function ($query) use ($trackingModel, $filters) {
                        $query->where(
                            'tracking_status_enum',
                            $trackingModel->present()->getTrackingStatusEnum($filters['status'])
                        );
                    }
                );
            }
        }

        if (isset($filters['problem'])) {
            if ($filters['problem'] == 1) {
                $productPlanSales->whereHas(
                    'tracking',
                    function ($query) use ($trackingModel, $filters) {
                        $query->whereIn('system_status_enum', [
                                $trackingModel->present()->getSystemStatusEnum('unknown_carrier'),
                                $trackingModel->present()->getSystemStatusEnum('no_tracking_info'), //não está bloqueado, está pendente, não transferiu pq aguarda a atualização do código
                                $trackingModel->present()->getSystemStatusEnum('posted_before_sale'),
                                $trackingModel->present()->getSystemStatusEnum('duplicated'),
                            ]
                        );
                    }
                );
            }
        }

        if (isset($filters['tracking_code'])) {
            $productPlanSales->whereHas(
                'tracking',
                function ($query) use ($filters) {
                    $query->where('tracking_code', 'like', '%'.$filters['tracking_code'].'%');
                }
            );
        }

        if (isset($filters['project'])) {
            $productPlanSales->whereHas(
                'product',
                function ($query) use ($filters) {
                    $query->where('project_id', current(Hashids::decode($filters['project'])));
                }
            );
        }

        return $productPlanSales;
    }

    /**
     * @param $filters
     * @return LengthAwarePaginator
     * @throws PresenterException
     */
    public function getPaginatedTrackings($filters)
    {
        $productPlanSales = $this->getTrackingsQueryBuilder($filters);

        return $productPlanSales->orderBy('id', 'desc')->paginate(10);
    }

    /**
     * @param $filters
     * @return array
     * @throws PresenterException
     */
    public function getResume($filters)
    {
        $trackingPresenter = (new Tracking())->present();

        $status = [
            $trackingPresenter->getTrackingStatusEnum('posted'),
            $trackingPresenter->getTrackingStatusEnum('dispatched'),
            $trackingPresenter->getTrackingStatusEnum('delivered'),
            $trackingPresenter->getTrackingStatusEnum('out_for_delivery'),
            $trackingPresenter->getTrackingStatusEnum('exception'),
        ];

        $productPlanSales = $this->getTrackingsQueryBuilder($filters)
            ->without([
                'tracking',
                'sale',
                'product',
            ])
            ->leftJoin('trackings', 'products_plans_sales.id', '=', 'trackings.product_plan_sale_id')
            ->selectRaw("COUNT(*) as total,
                                   SUM(CASE WHEN trackings.tracking_status_enum = ? THEN 1 ELSE 0 END) as posted,
                                   SUM(CASE WHEN trackings.tracking_status_enum = ? THEN 1 ELSE 0 END) as dispatched,
                                   SUM(CASE WHEN trackings.tracking_status_enum = ? THEN 1 ELSE 0 END) as delivered,
                                   SUM(CASE WHEN trackings.tracking_status_enum = ? THEN 1 ELSE 0 END) as out_for_delivery,
                                   SUM(CASE WHEN trackings.tracking_status_enum = ? THEN 1 ELSE 0 END) as exception,
                                   SUM(CASE WHEN trackings.tracking_status_enum is null THEN 1 ELSE 0 END) as unknown",
                $status)
            ->first();

        return $productPlanSales->toArray();
    }
}
