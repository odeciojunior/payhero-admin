<?php

namespace Modules\Transfers\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\FoxUtils;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Transfers\Transformers\TransfersResource;

/**
 * Class TransfersController
 * @package Modules\Transfers\Http\Controllers
 */
class TransfersController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {

            $transfersModel = new Transfer();

            activity()->on($transfersModel)->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todos os extratos');

            $data = $request->all();

            //parâmetros obrigatórios
            $companyId = current(Hashids::decode($data['company']));
            $dateRange = FoxUtils::validateDateRange($data["date_range"]);
            if ($data['date_type'] == 'transaction_date') {
                $dateType = 'transaction.created_at';
            } else if ($data['date_type'] == 'transfer_date') {
                $dateType = 'transfers.created_at';
            } else {
                $dateType = 'sales.start_date';
            }

            $transfers = $transfersModel->leftJoin('transactions as transaction', 'transaction.id', 'transfers.transaction_id')
                ->leftJoin('sales', 'sales.id', '=', 'transaction.sale_id')
                ->where(function ($query) use ($companyId) {
                    $query->where('transfers.company_id', $companyId)
                        ->orWhere('transaction.company_id', $companyId);
                })
                ->whereBetween($dateType, [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);

            $saleId = str_replace('#', '', $data['transaction']);
            $saleId = current(Hashids::connection('sale_id')->decode($saleId));
            if ($saleId) {
                $transfers->where('transaction.sale_id', $saleId);
            }

            if(!empty($data['type'])){
                $transfers->where('transfers.type_enum', $transfersModel->present()->getTypeEnum($data['type']));
            }

            if(!empty($data['reason'])){
                $transfers->where('transfers.reason', 'like', '%' . $data['reason'] . '%');
            }

            if(!empty($data['value'])){
                $value = intval(preg_replace('/[^0-9]/', '', $data['value']));
                $transfers->where('transfers.value', $value);
            }

            $balanceInPeriod = $transfers->selectRaw("sum(CASE WHEN transfers.type_enum = 2 THEN (transfers.value * -1) ELSE transfers.value END) as balanceInPeriod")
                ->first();

            if(!empty($balanceInPeriod)){
                $balanceInPeriod = $balanceInPeriod->balanceInPeriod / 100;
                $balanceInPeriod = number_format($balanceInPeriod, 2, ',', '.');
            }

            $transfers = $transfers->select(
                'transfers.*',
                'transaction.sale_id',
                'transaction.company_id',
                'transaction.currency',
                'transaction.status',
                'transaction.type as transaction_type',
                'transaction.antecipable_value'
            )->orderBy('id', 'DESC')
            ->paginate(10);

            $return = TransfersResource::collection($transfers);

            $return->additional(['meta' => [
                'balance_in_period' => $balanceInPeriod,
            ]]);

            return $return;

        } catch (Exception $e) {
            Log::warning('Erro ao buscar lista de transferencias (TransfersController - index)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                    ], 400);
        }
    }
}


