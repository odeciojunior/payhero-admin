<?php

namespace Modules\Reports\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Modules\Core\Entities\Sale;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Entities\BlockReason;
use Modules\Core\Services\SaleService;
use Spatie\Activitylog\Models\Activity;
use Modules\Reports\Transformers\ReportCouponResource;
use Modules\Reports\Transformers\PendingBalanceResource;
use Modules\Reports\Transformers\TransactionBlockedResource;

class ReportsApiController extends Controller
{

    public function getDiscountCoupons(Request $request)
    {
        try {
            $projectId = hashids_decode($request->input('project'));
            $dateRange = FoxUtils::validateDateRange($request->input("date_range"));
            if (empty($request->input('status'))) {
                $status = [1, 2, 4, 6, 7, 8, 12, 20, 22];
            } else {
                $status = $request->input("status") == 7 ? [7, 22] : [$request->input("status")];
            }

            $cacheName = 'coupons-' . $projectId . '-' . json_encode($dateRange) . '-' . json_encode($status);

            $coupons = cache()->remember($cacheName , 60, function() use($request, $dateRange, $status) {
    
                return Sale::select([
                    DB::raw('COUNT(sales.id) as amount'),
                    'project_id',
                    'cupom_code',
                    'projects.name as project_name'
                ])->join('projects', 'sales.project_id', '=', 'projects.id')
                    ->where('project_id', hashids_decode($request->input('project')))
                    ->whereIn('sales.status', $status)
                    ->where('cupom_code', '!=', '')
                    ->whereBetween('start_date', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59'])
                    ->groupBy('cupom_code', 'project_id')
                    ->orderByRaw('amount DESC')
                    ->paginate(10);
            });  

            return ReportCouponResource::collection($coupons);
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao carregar descontos'], 400);
        }
    }

    public function pendingBalance(Request $request)
    {
        try {
            $saleService = new SaleService();

            $data = $request->all();

            $sales = $saleService->getPendingBalance($data);

            return PendingBalanceResource::collection($sales);
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao carregar vendas'], 400);
        }
    }

    public function resumePendingBalance(Request $request)
    {
        try {
            $saleService = new SaleService();

            $data = $request->all();

            $resume = $saleService->getResumePending($data);

            return response()->json($resume);
        } catch (Exception $e) {
            report($e);
            return response()->json(['error' => 'Erro ao exibir resumo das vendas'], 400);
        }
    }

    public function blockedBalance(Request $request)
    {
        try {
            activity()->tap(function (Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela saldo bloqueado');

            $saleService = new SaleService();

            $data = $request->all();

            $sales = $saleService->getPaginetedBlocked($data);

            return TransactionBlockedResource::collection($sales);
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao carregar vendas bloqueadas'], 400);
        }
    }

    public function resumeBlockedBalance(Request $request)
    {
        try {
            $saleService = new SaleService();

            $data = $request->all();

            $resume = $saleService->getResumeBlocked($data);

            return response()->json($resume);
        } catch (Exception $e) {
            report($e);
            return response()->json(['error' => 'Erro ao exibir resumo do saldo bloqueado'], 400);
        }
    }

    public function getBlockReasons()
    {
        try {
            $data = cache()->remember('blocked-reasons', 600, function() {
                $all = BlockReason::select(['id', 'reason'])->get();
                $otherReason = $all->where('id', 7)->first();
                return $all->where('id', '!=', 7)
                    ->push($otherReason)
                    ->values();
            });
            return response()->json($data);

        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter lista de motivos de bloqueio'], 400);
        }
    }
}
