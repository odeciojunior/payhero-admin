<?php

namespace Modules\AstronMembers\Http\Controllers;

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
use Modules\Core\Services\ProjectService;
use Modules\Core\Services\CheckoutService;

use Modules\Core\Entities\AstronMembersIntegration;
use Modules\AstronMembers\Transformers\AstronMembersResource;
use Modules\Projects\Transformers\ProjectsSelectResource;

class AstronMembersApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {

            $astronMembersIntegrations = AstronMembersIntegration::with('project', 'project.usersProjects')
            ->whereHas(
                'project.usersProjects',
                function ($query) {
                    $query
                    ->where('company_id', auth()->user()->company_default)
                    ->where('user_id', auth()->user()->getAccountOwnerId());
                }
            )->get();

            $projects     = collect();
            $userProjects = UserProject::where([[
                'user_id', auth()->user()->getAccountOwnerId()],[
                'company_id', auth()->user()->company_default
            ]])->orderBy('id', 'desc')->get();

            if ($userProjects->count() > 0) {
                foreach ($userProjects as $userProject) {
                    $project = $userProject
                        ->project()
                        ->leftjoin('domains',
                            function ($join) {
                                $join->on('domains.project_id', '=', 'projects.id')
                                    ->where('domains.status', 3)
                                    ->whereNull('domains.deleted_at');
                            }
                        )
                        ->where('projects.status', Project::STATUS_ACTIVE)
                        ->first();
                    if (!empty($project)) {
                        $projects->add($userProject->project);
                    }
                }
            }
            return response()->json([
                "integrations" => AstronMembersResource::collection($astronMembersIntegrations),
                "projects" => ProjectsSelectResource::collection($projects),
            ]);
        } catch (Exception $e) {
            Log::debug($e);
            return response()->json(["message" => __('messages.unexpected_error')], 400);
        }
    }

    /**
     * @param $id
     * @return AstronMembersResource
     */
    public function show($id)
    {
        $astronMembersIntegrationModel = new AstronMembersIntegration();
        $astronMembersIntegration = $astronMembersIntegrationModel->find(current(Hashids::decode($id)));

        return new AstronMembersResource($astronMembersIntegration);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $astronMembersIntegrationModel = new AstronMembersIntegration();

            $projectId = current(Hashids::decode($data["project_id"]));
            $token = md5(uniqid($data["project_id"], true));
            if (!empty($projectId)) {
                $integration = $astronMembersIntegrationModel->where("project_id", $projectId)->first();
                if ($integration) {
                    return response()->json(
                        [
                            "message" => "Projeto já integrado",
                        ],
                        400
                    );
                }


                $integrationCreated = $astronMembersIntegrationModel->create([
                                                                           'link'                => $data['link'],
                                                                           'token'                => $token,
                                                                           'project_id'          => $projectId,
                                                                           'user_id'             => auth()->user()->getAccountOwnerId(),
                                                                       ]);

                if ($integrationCreated) {
                    return response()->json(
                        [
                            "message" => "Integração criada com sucesso!",
                        ],
                        200
                    );
                }

                return response()->json(
                    [
                        "message" => "Ocorreu um erro ao realizar a integração",
                    ],
                    400
                );
            }

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao realizar a integração",
                ],
                400
            );

        } catch (Exception $e) {
            Log::warning("Erro ao realizar integração  AstronMembersController - store");
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao realizar a integração",
                ],
                400
            );
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
                $astronMembersIntegrationModel = new AstronMembersIntegration();
                $projectService = new ProjectService();

                $projects = $projectService->getMyProjects();

                $projectId = current(Hashids::decode($id));
                $integration = $astronMembersIntegrationModel->where("project_id", $projectId)->first();

                if ($integration) {
                    return response()->json(["projects" => $projects, "integration" => $integration]);
                }

                return response()->json(
                    [
                        "message" => "Ocorreu um erro, tente novamente mais tarde!",
                    ],
                    400
                );
            }

            return response()->json(
                [
                    "message" => "Ocorreu um erro, tente novamente mais tarde!",
                ],
                400
            );

        } catch (Exception $e) {
            Log::warning(
                "Erro ao tentar acessar tela editar Integração AstronMembers (AstronMembersController - edit)"
            );
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro, tente novamente mais tarde!",
                ],
                400
            );
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $astronMembersIntegrationModel = new AstronMembersIntegration();
        $data = $request->all();
        $integrationId = current(Hashids::decode($id));
        $astronMembersIntegration = $astronMembersIntegrationModel->find($integrationId);

        $integrationUpdated = $astronMembersIntegration->update([
            "link" => $data["link"],
            //   'boleto_generated'    => $data['boleto_generated'],
            //   'boleto_paid'         => $data['boleto_paid'],
            //   'credit_card_refused' => $data['credit_card_refused'],
            //   'credit_card_paid'    => $data['credit_card_paid'],
            //   'abandoned_cart'      => $data['abandoned_cart'],

            //   'pix_generated'       => $data['pix_generated'],
            //   'pix_paid'            => $data['pix_paid'],
            //   'pix_expired'         => $data['pix_expired'],
        ]);
        if ($integrationUpdated) {
            return response()->json(
                [
                    "message" => "Integração atualizada com sucesso!",
                ],
                200
            );
        }

        return response()->json(
            [
                "message" => "Ocorreu um erro ao atualizar a integração",
            ],
            400
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $integrationId = current(Hashids::decode($id));
            $astronMembersIntegrationModel = new AstronMembersIntegration();
            $integration = $astronMembersIntegrationModel->find($integrationId);
            $integrationDeleted = $integration->delete();
            if ($integrationDeleted) {
                return response()->json(
                    [
                        "message" => "Integração Removida com sucesso!",
                    ],
                    200
                );
            }

            return response()->json(
                [
                    "message" => "Erro ao tentar remover Integração",
                ],
                400
            );
        } catch (Exception $e) {
            Log::warning("Erro ao tentar remover Integração AstronMembers (AstronMembersController - destroy)");
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao tentar remover, tente novamente mais tarde!",
                ],
                400
            );
        }
    }

    public function regenerateBoleto(Request $request)
    {
        $saleId = current(Hashids::decode($request->boleto_id));

        $sale = Sale::with(["project"])->find($saleId);

        if (!empty($sale)) {
            $project = $sale->project;

            $totalPaidValue = preg_replace("/[^0-9]/", "", $sale->sub_total);
            $shippingPrice = preg_replace("/[^0-9]/", "", $sale->shipment_value);
            $dueDays = $project->boleto_due_days ?? 3;
            $dueDate = Carbon::now()
                ->addDays($dueDays)
                ->format("Y-m-d");
            if (Carbon::parse($dueDate)->isWeekend()) {
                $dueDate = Carbon::parse($dueDate)
                    ->nextWeekday()
                    ->format("Y-m-d");
            }

            $checkoutService = new CheckoutService();
            $boletoRegenerated = $checkoutService->regenerateBillet(
                Hashids::connection("sale_id")->encode($sale->id),
                $totalPaidValue + $shippingPrice,
                $dueDate
            );

            $sale = Sale::find($saleId);

            return response()->json(
                [
                    "boleto_link" => $sale->boleto_link,
                    "digitable_line" => $sale->boleto_digitable_line,
                ],
                200
            );
        } else {
            return response()->json(
                [
                    "error" => "sale not found",
                ],
                400
            );
        }
    }
}
