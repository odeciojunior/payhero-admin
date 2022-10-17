<?php

namespace Modules\SalesRecovery\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Checkouts\Transformers\CheckoutIndexResource;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\CheckoutService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\PagarmeService;
use Modules\Core\Services\ProjectService;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\SalesRecoveryService;
use Modules\Plans\Transformers\PlansSelectResource;
use Modules\Sales\Exports\Reports\AbandonedCartReportExport;
use Modules\Sales\Exports\Reports\BilletExpiredReportExport;
use Modules\Sales\Exports\Reports\CardRefusedReportExport;
use Modules\Sales\Exports\Reports\PixExpiredReportExport;
use Modules\SalesRecovery\Transformers\SalesRecoveryCardRefusedResource;
use Modules\SalesRecovery\Transformers\SalesRecoveryCartAbandonedDetailsResourceTransformer;
use Modules\SalesRecovery\Transformers\SalesRecoverydetailsResourceTransformer;
use Modules\SalesRecovery\Transformers\SalesRecoveryIndexResourceTransformer;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class SalesRecoveryApiController
 * @package Modules\SalesRecovery\Http\Controllers
 */
class SalesRecoveryApiController extends Controller
{
    /**
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index()
    {
        try {
            $projectService = new ProjectService();
            $projectModel = new Project();

            $projectStatus = [
                $projectModel->present()->getStatus("active"),
                $projectModel->present()->getStatus("disabled"),
            ];

            $projects = $projectService->getUserProjects(true, $projectStatus);
            if (!empty($projects)) {
                return SalesRecoveryIndexResourceTransformer::collection($projects);
            } else {
                return response()->json(
                    [
                        "message" => "Erro ao listar projetos, tente novamente mais tarde",
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            Log::warning("Erro ao listar projetos, tente novamente mais tarde");
            report($e);

            return response()->json(
                [
                    "message" => "Erro ao listar projetos, tente novamente mais tarde",
                ],
                400
            );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection|string
     */
    public function getRecoveryData(Request $request)
    {
        try {
            $salesRecoveryService = new SalesRecoveryService();

            $requestValidate = Validator::make($request->all(), [
                "project" => "required|string",
                "type" => "required|string",
                "start_date" => "nullable",
                "end_date" => "nullable",
                "client_name" => "nullable|string",
            ]);

            if ($requestValidate->fails()) {
                return response()->json(
                    [
                        "message" => "Erro ao listar projetos, tente novamente mais tarde",
                    ],
                    400
                );
            } else {
                $projectId = null;
                if ($request->has("project") && !empty($request->input("project"))) {
                    $projectId = current(Hashids::decode($request->input("project")));
                }

                $client = null;
                if ($request->has("client_name") && !empty($request->input("client_name"))) {
                    $client = $request->input("client_name");
                }

                $endDate = null;
                if ($request->has("end_date") && !empty($request->input("end_date"))) {
                    $endDate = date("Y-m-d", strtotime($request->input("end_date") . " + 1 day"));
                }

                $startDate = null;
                if ($request->has("start_date") && !empty($request->input("start_date"))) {
                    $startDate = date("Y-m-d", strtotime($request->input("start_date")));
                }

                return $salesRecoveryService->verifyType(
                    $request->input("type"),
                    $projectId,
                    $startDate,
                    $endDate,
                    $client
                );
            }
        } catch (Exception $e) {
            Log::warning("Erro ao buscar dados de recuperação de vendas");
            report($e);

            return response()->json(
                [
                    "message" => "Erro ao listar projetos, tente novamente mais tarde",
                ],
                400
            );
        }
    }

    public function getAbandonedCart(Request $request)
    {
        try {
            $request->validate([
                "project" => "nullable|string",
                "recovery_type" => "required",
                "date_range" => "required",
                "client" => "nullable|string",
                "client_document" => "nullable|string",
                "plan" => "nullable|string",
            ]);

            $checkouts = (new CheckoutService())->getAbandonedCart();

            return CheckoutIndexResource::collection($checkouts);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro, tente novamente mais tarde",
                ],
                400
            );
        }
    }

    public function getCartRefused(Request $request)
    {
        try {
            $data = $request->all();
            $salesRecoveryService = new SalesRecoveryService();

            $projectIds = ["all"];
            if ($data["project"] != "all") {
                $projectIds = [];
                $projects = explode(",", $data["project"]);

                foreach ($projects as $project) {
                    array_push($projectIds, current(Hashids::decode($project)));
                }
            }

            $client = null;
            if (!empty($data["client"])) {
                $client = $data["client"];
            }

            $clientDocument = null;
            if (!empty($data["client_document"])) {
                $clientDocument = $data["client_document"];
            }

            $plans = null;
            if ($data["plan"] != "all") {
                $plans = [];
                $parsePlans = explode(",", $data["plan"]);

                foreach ($parsePlans as $plan) {
                    array_push($plans, $plan);
                }
            }

            $dateStart = null;
            $dateEnd = null;

            $dateRange = FoxUtils::validateDateRange($data["date_range"]);
            if (!empty($data["date_type"]) && $dateRange) {
                $dateStart = $dateRange[0] . " 00:00:00";
                $dateEnd = $dateRange[1] . " 23:59:59";
            }

            $paymentMethod = (new Sale())->present()->getPaymentType('credit_card');
            $status        = [3];

            $company_id = hashids_decode($data["company"]);
            $sales = $salesRecoveryService->getSaleExpiredOrRefused($paymentMethod, $status, $projectIds, $dateStart, $dateEnd, $client, $clientDocument, $plans, $company_id);

            return SalesRecoveryCardRefusedResource::collection($sales);
        } catch (Exception $e) {
            Log::warning("Erro buscar dados cartão recusado, SalesRecoveryApiController - getCartRefused");
            report($e);

            return response()->json([
                "message" => "Ocorreu um erro, tente novamente mais tarde",
            ]);
        }
    }

    public function getBoletoOverdue(Request $request)
    {
        $data = $request->all();
        $salesRecoveryService = new SalesRecoveryService();

        $projectIds = ["all"];

        if ($data["project"] != "all") {
            $projectIds = [];
            $projects = explode(",", $data["project"]);

            $showFromApi = false;
            foreach ($projects as $project) {
                $showFromApi = str_starts_with($project,'TOKEN');
                array_push($projectIds, ($showFromApi ? 'TOKEN-':'').current(Hashids::decode(str_replace('TOKEN-','',$project))));
            }
        }

        $client = null;
        if (!empty($data["client"])) {
            $client = $data["client"];
        }

        $clientDocument = null;
        if (!empty($data["client_document"])) {
            $clientDocument = $data["client_document"];
        }

        $plans = null;
        if ($data["plan"] != "all") {
            $plans = [];
            $parsePlans = explode(",", $data["plan"]);

            foreach ($parsePlans as $plan) {
                array_push($plans, $plan);
            }
        }

        $dateStart = null;
        $dateEnd = null;

        $dateRange = FoxUtils::validateDateRange($data["date_range"]);
        if (!empty($data["date_type"]) && $dateRange) {
            $dateStart = $dateRange[0] . " 00:00:00";
            $dateEnd = $dateRange[1] . " 23:59:59";
        }

        $paymentMethod = (new Sale())->present()->getPaymentType("boleto");
        $status = [5];

        $company_id = hashids_decode($data["company"]??auth()->user()->company_default);
        $sales = $salesRecoveryService->getSaleExpiredOrRefused($paymentMethod, $status, $projectIds, $dateStart, $dateEnd, $client, $clientDocument, $plans, $company_id);

        return SalesRecoveryCardRefusedResource::collection($sales);
    }

    /**
     * @param Request $request
     * @return JsonResponse|SalesRecoveryCartAbandonedDetailsResourceTransformer|SalesRecoverydetailsResourceTransformer
     */
    public function getDetails(Request $request)
    {
        try {
            $saleModel = new Sale();
            $checkoutModel = new Checkout();
            $salesRecoveryService = new SalesRecoveryService();

            if ($request->has('checkout') && !empty($request->input('checkout'))) {
                $saleId = current(Hashids::decode($request->input('checkout')));
                $sale   = $saleModel->find($saleId);

                if (!empty($sale)) {
                    return SalesRecoverydetailsResourceTransformer::make($salesRecoveryService->getSalesCartOrBoletoDetails($sale));
                }

                $checkout = $checkoutModel->find($saleId);
                if (!empty($checkout)) {
                    return SalesRecoveryCartAbandonedDetailsResourceTransformer::make($salesRecoveryService->getSalesCheckoutDetails($checkout));
                }

                return response()->json(['message' => 'Ocorreu algum erro, tente novamente mais tarde'], 400);
            }

            return response()->json(['message' => 'Ocorreu algum erro, tente novamente mais tarde'], 400);

        } catch (Exception $e)
        {
            report($e);

            return response()->json(["message" => "Ocorreu algum erro, tente novamente mais tarde"], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function regenerateBoleto(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "saleId" => "required|string",
                "date" => "required|string",
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        "message" => "Preencha os dados corretamente.",
                    ],
                    400
                );
            }

            $sale = Sale::find(current(Hashids::decode($request->saleId)));
            if (empty($sale)) {
                return response()->json(
                    [
                        "message" => "[325] Ocorreu um erro, tente novamente mais tarde. ",
                    ],
                    400
                );
            }

            $totalPaidValue = $sale->original_total_paid_value;
            if (!empty($request->discountValue)) {

                if ($request->discountType == 'percentage') {
                    $discount = ($totalPaidValue * (foxutils()->onlyNumbers($request->discountValue)/100));
                    $discount = number_format($discount/100,2,'.',''); //converte para decimal
                    $totalPaidValue -= $discount*100;
                } else {
                    $discount = (int) preg_replace("/[^0-9]/", "", $request->discountValue);
                    $totalPaidValue -= $discount;
                    $discount = number_format($discount / 100, 2, ".", ""); //converte para decimal
                }

                if(!empty($sale->shopify_discount)){
                    $totalPaidValue+=foxutils()->onlyNumbers($sale->shopify_discount);
                }

                $sale->update([
                    'shopify_discount' => $discount,
                ]);
            }

            $dueDate = $request->input("date");
            if (Carbon::parse($dueDate)->isWeekend()) {
                $dueDate = Carbon::parse($dueDate)
                    ->nextWeekday()
                    ->format("Y-m-d");
            }

            $checkoutService = new CheckoutService();

            $boletoRegenerated = $checkoutService->regenerateBillet(Hashids::connection('sale_id')
            ->encode($sale->id), $totalPaidValue, $dueDate);

            $message = $boletoRegenerated['message']??'[359] Ocorreu um erro tente novamente mais tarde.';
            $status  = 400;
            if ($boletoRegenerated['status'] == 'success') {
                $message = 'Boleto regenerado com sucesso';
                $status  = 200;
            }

            return response()->json([
                'message' => $message,
            ], $status);

        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    "message" => "[374] Ocorreu um erro, tente novamente mais tarde. ",
                ],
                400
            );
        }
    }

    public function export(Request $request)
    {
        try {
            $dataRequest = $request->all();
            $user = auth()->user();

            if ($dataRequest["recovery_type"] == 1) {
                $filename = "report_abandoned_cart" . Hashids::encode($user->id) . ".csv"; //. $dataRequest['format'];
                (new AbandonedCartReportExport($dataRequest, $user, $filename))->queue($filename)->allOnQueue("high");
            } elseif ($dataRequest["recovery_type"] == 3) {
                $filename = "report_card_refused" . Hashids::encode($user->id) . ".csv"; //. $dataRequest['format'];
                (new CardRefusedReportExport($dataRequest, $user, $filename))->queue($filename)->allOnQueue("high");
            } elseif ($dataRequest["recovery_type"] == 4) {
                $filename = "report_pix_expired" . Hashids::encode($user->id) . ".csv"; //. $dataRequest['format'];
                (new PixExpiredReportExport($dataRequest, $user, $filename))->queue($filename)->allOnQueue("high");
            } else {
                $filename = "report_billet_expired" . Hashids::encode($user->id) . ".csv"; //. $dataRequest['format'];
                (new BilletExpiredReportExport($dataRequest, $user, $filename))->queue($filename)->allOnQueue("high");
            }

            return response()->json(["message" => "A exportação começou", "email" => $dataRequest["email"]]);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Erro ao tentar gerar o arquivo Excel." . $e->getMessage()], 400);
        }
    }

    public function getPixOverdue(Request $request)
    {
        $data = $request->all();
        $salesRecoveryService = new SalesRecoveryService();

        $projectIds = ["all"];

        if ($data["project"] != "all") {
            $projectIds = [];
            $projects = explode(",", $data["project"]);

            $showFromApi = false;
            foreach ($projects as $project) {
                $showFromApi = str_starts_with($project,'TOKEN');
                array_push($projectIds, ($showFromApi ? 'TOKEN-':'').current(Hashids::decode(str_replace('TOKEN-','',$project))));
            }
        }

        $client = null;
        if (!empty($data["client"])) {
            $client = $data["client"];
        }

        $clientDocument = null;
        if (!empty($data["client_document"])) {
            $clientDocument = $data["client_document"];
        }

        $plans = null;
        if ($data["plan"] != "all") {
            $plans = [];
            $parsePlans = explode(",", $data["plan"]);

            foreach ($parsePlans as $plan) {
                array_push($plans, $plan);
            }
        }

        $dateStart = null;
        $dateEnd = null;

        $dateRange = FoxUtils::validateDateRange($data["date_range"]);
        if (!empty($data["date_type"]) && $dateRange) {
            $dateStart = $dateRange[0] . " 00:00:00";
            $dateEnd = $dateRange[1] . " 23:59:59";
        }

        $paymentMethod = (new Sale())->present()->getPaymentType("pix");
        $status = [5];

        $company_id = hashids_decode($data["company"]);
        $sales = $salesRecoveryService->getSaleExpiredOrRefused(
            $paymentMethod,
            $status,
            $projectIds,
            $dateStart,
            $dateEnd,
            $client,
            $clientDocument,
            $plans,
            $company_id
        );

        return SalesRecoveryCardRefusedResource::collection($sales);
    }

    public function getProjectsWithRecovery(){
        $projects = SalesRecoveryService::getProjectsWithRecovery();
        $projectsEncoded=[];
        foreach($projects as $item){
            $projectsEncoded[]= Hashids::encode($item->project_id);
        }
        return $projectsEncoded;
    }

    public function getPlans(Request $request)
    {
        try {
            $data = $request->all();

            $projectIds = [];
            if (!empty($data["project_id"])) {
            //if (is_array($data["project_id"])) {
                if(!empty($data['project_id'][0])){ // && $data['project_id'][0]!='all'
                    $showFromApi = false;
                    foreach($data['project_id'] as $project){
                        if(!empty($project)){
                            $showFromApi = str_starts_with($project,'TOKEN');
                            array_push($projectIds, ($showFromApi?'TOKEN-':'').hashids_decode(str_replace('TOKEN-','',$project)));
                        }
                    };
                }
                else{
                    $projects = SalesRecoveryService::getProjectsWithRecovery();
                    foreach($projects as $item){
                        array_push($projectIds, $item->project_id);
                    }
                }
            }

            $user = auth()->user();
            $userId = $user->getAccountOwnerId();
            $plans = null;

            if (current($projectIds)) {

                if (!empty($data['search'])) {
                    $plans = Plan::
                        where('name', 'like', '%' . $data['search'] . '%')
                        ->whereIn('project_id', $projectIds)
                        ->orderby('name')
                        ->limit(30)
                        ->get();

                } else {
                    $plans = Plan::
                        whereIn('project_id', $projectIds)
                        ->orderby('name')
                        ->limit(30)
                        ->get();

                }
                return PlansSelectResource::collection($plans);
            } else {

                $userProjects = SalesRecoveryService::getProjectsWithRecovery();

                if (!empty($data['search'])) {
                    $plans = Plan::
                        where('name', 'like', '%' . $data['search'] . '%')
                        ->whereIn("project_id", $userProjects)
                        ->orderby('name')
                        ->limit(30)
                        ->get();

                } else {
                    $plans = Plan::
                        whereIn("project_id", $userProjects)
                        ->orderby('name')
                        ->limit(30)
                        ->get();
                }
                return PlansSelectResource::collection($plans);
            }
        } catch (Exception $e) {
            report($e);
            return response()->json(
                [
                    "message" => "Ocorreu um erro, ao buscar dados dos planos",
                ],
                400
            );
        }
    }
}
