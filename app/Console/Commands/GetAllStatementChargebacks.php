<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\GetnetChargeback;
use Modules\Core\Entities\GetnetChargebackDetail;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\GetnetBackOfficeService;

class GetAllStatementChargebacks extends Command
{
    protected $signature = 'getnet:get-all-statement-chargebacks';

    protected $description = 'Salva todos os chargeback diarios';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $getnetBackOfficeService = new GetnetBackOfficeService();
            $latest_chargeback = GetnetChargeback::orderByDesc('id')->first();
            $start_day = Carbon::parse($latest_chargeback->created_at);

            //filtros que retorna tudo
            $startDateFilter = $start_day->addDay()->format('Y-m-d') . ' 00:00:00';
            $endDateFilter = $start_day->addDays(20)->format('Y-m-d') . ' 23:59:59';

            $filters = [
                'schedule_date_init' => $startDateFilter,
                'schedule_date_end' => $endDateFilter,
            ];

            //pega todos os statements da getnet
            $statements = json_decode($getnetBackOfficeService->getStatementWithoutSaveRequest($filters));

            if (isset($statements->chargeback)) {
                $chargebacks = $statements->chargeback;
                $sale_gateway_order_ids_saved = GetnetChargeback::join('sales', 'sales.id',
                    'getnet_chargebacks.sale_id')
                    ->pluck('sales.gateway_order_id')
                    ->toArray();
                $sale_gateway_order_ids_news = [];

                foreach ($chargebacks as $chargeback) {
                    $explode1 = explode("|", $chargeback->adjustment_reason);
                    $explode2 = explode(" - ", $explode1[2]);
                    $gateway_order_id = $explode2[0];

                    try {
                        //compara com vendas antigas e vendas novas
                        if (!in_array($gateway_order_id, $sale_gateway_order_ids_saved)) {
                            $sale = Sale::where("gateway_order_id", '=', $gateway_order_id)->first();

                            if (empty($sale)) {
                                report(new Exception('Não foi possivel efetuar o chargeback para a venda com esse gateway_order_id ' . $gateway_order_id));
                                continue;
                            }

                            if (!in_array($gateway_order_id, $sale_gateway_order_ids_news)) {
                                $sale_gateway_order_ids_news[] = $gateway_order_id;

                                $userProject = UserProject::with('company')
                                    ->where('type_enum', UserProject::TYPE_PRODUCER_ENUM)
                                    ->where('project_id', $sale->project_id)
                                    ->first();

                                $chargeback_obj = new GetnetChargeback();
                                $chargeback_obj->sale_id = $sale->id;
                                $chargeback_obj->company_id = $userProject->company_id;
                                $chargeback_obj->project_id = $sale->project_id;
                                $chargeback_obj->user_id = $sale->owner_id;
                                $chargeback_obj->transaction_date = $chargeback->transaction_date;
                                $chargeback_obj->installment_date = $chargeback->installment_date;
                                $chargeback_obj->adjustment_date = $chargeback->adjustment_date;
                                $chargeback_obj->amount = intval($sale->original_total_paid_value);
                                $chargeback_obj->body = json_encode($chargeback);
                                $chargeback_obj->save();

                                event(new NewChargebackEvent($sale));
                            }

                            $getnet_chargeback = GetnetChargeback::where('sale_id', $sale->id)->first();

                            $getnetChargebackDetail = new GetnetChargebackDetail();
                            $getnetChargebackDetail->body = json_encode($chargeback);
                            $getnetChargebackDetail->filters = json_encode($filters);
                            $getnetChargebackDetail->getnet_chargeback_id = $getnet_chargeback->id;
                            $getnetChargebackDetail->save();
                        }
                    } catch (Exception $e) {
                        report($e);
                    }
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    public function antigoImport()
    {
        try {
            $getnetBackOfficeService = new GetnetBackOfficeService();

            //filtros que retorna tudo
            $startDate = '2020-07-01';
            $endDate = today()->addDays(40)->format('Y-m-d');
            $startDate .= ' 00:00:00';
            $endDate .= ' 23:59:59';

            $filters = [
                'schedule_date_init' => $startDate,
                'schedule_date_end' => $endDate,
            ];

            //pega o último
            $latestGetnetChargebackDetail = GetnetChargebackDetail::orderBy('id', 'desc')->first();

            //pega todos os statements da getnet
            $statements = json_decode($getnetBackOfficeService->getStatementWithoutSaveRequest($filters));

            //cria um novo apenas com chargeback
            $getnetChargebackDetail = new GetnetChargebackDetail();
            $getnetChargebackDetail->body = json_encode($statements->chargeback);
            $getnetChargebackDetail->filters = json_encode($filters);
            $getnetChargebackDetail->save();

            $countLatestGetnetChargebackDetailArrayBody = isset($latestGetnetChargebackDetail->body) ? count(json_decode($latestGetnetChargebackDetail->body)) : 0;
            $countGetnetChargebackDetailArrayBody = count(json_decode($getnetChargebackDetail->body));

            //verifica se tem diferença pro último
            if ($countLatestGetnetChargebackDetailArrayBody != $countGetnetChargebackDetailArrayBody) {
                $chargebacks = json_decode($getnetChargebackDetail->body);

                //pega todas as vendas salvas antes para comparar antes de salvar novamente
                $sale_gateway_order_ids_saved = GetnetChargeback::join('sales', 'sales.id',
                    'getnet_chargebacks.sale_id')->pluck('sales.gateway_order_id')->toArray();
                $sale_gateway_order_ids_news = [];

                foreach ($chargebacks as $chargeback) {
                    $explode1 = explode("|", $chargeback->adjustment_reason);
                    $explode2 = explode(" - ", $explode1[2]);
                    $gateway_order_id = $explode2[0];

                    try {
                        //compara com vendas antigas e vendas novas
                        if (
                            !in_array($gateway_order_id, $sale_gateway_order_ids_news) &&
                            !in_array($gateway_order_id, $sale_gateway_order_ids_saved)
                        ) {
                            $sale_gateway_order_ids_news[] = $gateway_order_id;

                            $sale = Sale::where("gateway_order_id", '=', $gateway_order_id)->first();

                            $userProject = UserProject::with('company')
                                ->where('type_enum', UserProject::TYPE_PRODUCER_ENUM)
                                ->where('project_id', $sale->project_id)
                                ->first();

                            $chargeback_obj = new GetnetChargeback();
                            $chargeback_obj->getnet_chargeback_detail_id = $getnetChargebackDetail->id;
                            $chargeback_obj->sale_id = $sale->id;
                            $chargeback_obj->company_id = $userProject->company_id;
                            $chargeback_obj->project_id = $sale->project_id;
                            $chargeback_obj->user_id = $sale->owner_id;
                            $chargeback_obj->transaction_date = $chargeback->transaction_date;
                            $chargeback_obj->installment_date = $chargeback->installment_date;
                            $chargeback_obj->adjustment_date = $chargeback->adjustment_date;
                            $chargeback_obj->chargeback_amount = $sale->total_paid_value;
                            $chargeback_obj->body = json_encode($chargeback);
                            $chargeback_obj->save();
                        }
                    } catch (Exception $e) {
                        report($e);
                    }
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
