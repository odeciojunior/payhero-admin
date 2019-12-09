<?php

namespace Modules\HotZapp\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Core\Entities\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\PagarmeService;
use Modules\Core\Services\ProjectService;
use Modules\Core\Services\CheckoutService;
use Modules\Core\Entities\HotzappIntegration;
use Modules\HotZapp\Transformers\HotZappResource;
use Modules\Projects\Transformers\ProjectsSelectResource;

class HotZappApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $hotzappIntegration = new HotzappIntegration();
            $userProjectModel   = new UserProject();
            $projectModel       = new Project();

            $hotzappIntegrations = $hotzappIntegration->where('user_id',auth()->user()->account_owner_id)->with('project')->get();

            $projects     = collect();
            $userProjects = $userProjectModel->where('user_id', auth()->user()->account_owner_id)->get();
            if ($userProjects->count() > 0) {
                foreach ($userProjects as $userProject) {
                    $project = $userProject->project()->where('status', $projectModel->present()->getStatus('active'))
                                           ->first();
                    if (!empty($project)) {
                        $projects->add($userProject->project);
                    }
                }
            }

            return response()->json([
                                        'integrations' => HotZappResource::collection($hotzappIntegrations),
                                        'projects'     => ProjectsSelectResource::collection($projects),
                                    ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * @param $id
     * @return HotZappResource
     */
    public function show($id)
    {

        $hotzappIntegrationModel = new HotzappIntegration();
        $hotzappIntegration      = $hotzappIntegrationModel->find(current(Hashids::decode($id)));

        return new HotZappResource($hotzappIntegration);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        try {
            $data                    = $request->all();
            $hotzappIntegrationModel = new HotzappIntegration();

            $projectId = current(Hashids::decode($data['project_id']));
            if (!empty($projectId)) {
                $integration = $hotzappIntegrationModel->where('project_id', $projectId)->first();
                if ($integration) {
                    return response()->json([
                                                'message' => 'Projeto já integrado',
                                            ], 400);
                }
                if (empty($data['boleto_generated'])) {
                    $data['boleto_generated'] = 0;
                }
                if (empty($data['boleto_paid'])) {
                    $data['boleto_paid'] = 0;
                }
                if (empty($data['credit_card_paid'])) {
                    $data['credit_card_paid'] = 0;
                }
                if (empty($data['credit_card_refused'])) {
                    $data['credit_card_refused'] = 0;
                }
                if (empty($data['abandoned_cart'])) {
                    $data['abandoned_cart'] = 0;
                }

                $integrationCreated = $hotzappIntegrationModel->create([
                                                                           'link'                => $data['link'],
                                                                           'boleto_generated'    => $data['boleto_generated'],
                                                                           'boleto_paid'         => $data['boleto_paid'],
                                                                           'credit_card_refused' => $data['credit_card_refused'],
                                                                           'credit_card_paid'    => $data['credit_card_paid'],
                                                                           'abandoned_cart'      => $data['abandoned_cart'],
                                                                           'project_id'          => $projectId,
                                                                           'user_id'             => auth()->user()->account_owner_id,
                                                                       ]);

                if ($integrationCreated) {
                    return response()->json([
                                                'message' => 'Integração criada com sucesso!',
                                            ], 200);
                } else {

                    return response()->json([
                                                'message' => 'Ocorreu um erro ao realizar a integração',
                                            ], 400);
                }
            } else {

                return response()->json([
                                            'message' => 'Ocorreu um erro ao realizar a integração',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao realizar integração  HotZappController - store');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao realizar a integração',
                                    ], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        try {
            if (!empty($id)) {
                $hotzappIntegrationModel = new HotzappIntegration();
                $projectService          = new ProjectService();

                $projects = $projectService->getMyProjects();

                $projectId   = current(Hashids::decode($id));
                $integration = $hotzappIntegrationModel->where('project_id', $projectId)->first();

                if ($integration) {
                    return response()->json(['projects' => $projects, 'integration' => $integration]);
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                            ], 400);
                }
            } else {

                return response()->json([
                                            'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar Integração HotZapp (HotZappController - edit)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $hotzappIntegrationModel = new HotzappIntegration();
        $data                    = $request->all();
        $integrationId           = current(Hashids::decode($id));
        $hotzappIntegration      = $hotzappIntegrationModel->find($integrationId);
        if (empty($data['boleto_generated'])) {
            $data['boleto_generated'] = 0;
        }
        if (empty($data['boleto_paid'])) {
            $data['boleto_paid'] = 0;
        }
        if (empty($data['credit_card_paid'])) {
            $data['credit_card_paid'] = 0;
        }
        if (empty($data['credit_card_refused'])) {
            $data['credit_card_refused'] = 0;
        }
        if (empty($data['abandoned_cart'])) {
            $data['abandoned_cart'] = 0;
        }
        if (empty($data['abandoned_cart'])) {
            $data['abandoned_cart'] = 0;
        }

        $integrationUpdated = $hotzappIntegration->update([
                                                              'link'                => $data['link'],
                                                              'boleto_generated'    => $data['boleto_generated'],
                                                              'boleto_paid'         => $data['boleto_paid'],
                                                              'credit_card_refused' => $data['credit_card_refused'],
                                                              'credit_card_paid'    => $data['credit_card_paid'],
                                                              'abandoned_cart'      => $data['abandoned_cart'],
                                                          ]);
        if ($integrationUpdated) {
            return response()->json([
                                        'message' => 'Integração atualizada com sucesso!',
                                    ], 200);
        }

        return response()->json([
                                    'message' => 'Ocorreu um erro ao atualizar a integração',
                                ], 400);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $integrationId           = current(Hashids::decode($id));
            $hotzappIntegrationModel = new HotzappIntegration();
            $integration             = $hotzappIntegrationModel->find($integrationId);
            $integrationDeleted      = $integration->delete();
            if ($integrationDeleted) {
                return response()->json([
                                            'message' => 'Integração Removida com sucesso!',
                                        ], 200);
            }

            return response()->json([
                                        'message' => 'Erro ao tentar remover Integração',
                                    ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar remover Integração HotZapp (HotZappController - destroy)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao tentar remover, tente novamente mais tarde!',
                                    ], 400);
        }
    }

    public function regenerateBoleto(Request $request){

        $saleId = current(Hashids::decode($request->boleto_id));

        $sale = Sale::find($saleId);

        if(!empty($sale)){

            $project = $sale->project;

            $totalPaidValue = preg_replace("/[^0-9]/", "", $sale->sub_total);
            $shippingPrice  = preg_replace("/[^0-9]/", "", $sale->shipment_value);
            $dueDays = $project->boleto_due_days ?? 3;
            $dueDate = Carbon::now()->addDays($dueDays)->format('Y-m-d');
            if (Carbon::parse($dueDate)->isWeekend()) {
                $dueDate = Carbon::parse($dueDate)->nextWeekday()->format('Y-m-d');
            }
//            if (in_array($sale->gateway_id, [7])) {
                $checkoutService   = new CheckoutService();
                $boletoRegenerated = $checkoutService->regenerateBillet(Hashids::connection('sale_id')
                                                                                   ->encode($sale->id), ($totalPaidValue + $shippingPrice), $dueDate);
//            } else {
//                $pagarmeService = new PagarmeService($sale, $totalPaidValue, $shippingPrice);
//
//                $boletoRegenerated = $pagarmeService->boletoPayment($dueDate);
//            }

            $sale = Sale::find($saleId);

            return response()->json([
                'boleto_link'    => $sale->boleto_link,
                'digitable_line' => $sale->boleto_digitable_line
            ], 200);
        }
        else{
            return response()->json([
                'error' => 'sale not found'
            ], 400);
        }
    }


}
