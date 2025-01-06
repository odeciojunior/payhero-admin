<?php

namespace Modules\ConvertaX\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\ConvertaxIntegration;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\UserProject;
use Modules\ConvertaX\Transformers\ConvertaxResource;
use Modules\Projects\Transformers\ProjectsSelectResource;

class ConvertaXApiController extends Controller
{
    /**
     * Return resource of integrations.
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            activity()->on(new ConvertaxIntegration())->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todos as integrações do ConvertaX');

            $convertaxIntegrations = ConvertaxIntegration::with(['project', 'project.usersProjects'])
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
                'user_id', $user->getAccountOwnerId()],[
                'company_id', $user->company_default
            ]])->get();
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
                'data' => ConvertaxResource::collection($convertaxIntegrations),
                'projects' => ProjectsSelectResource::collection($projects),
            ]);

            //return ConvertaxResource::collection($convertaxIntegrations);
        } catch (Exception $e) {
            return response()->json(["message" => __('messages.unexpected_error')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $convertaxIntegrationModel = new ConvertaxIntegration();

            $projectId = current(Hashids::decode($data["project_id"]));

            if (!empty($projectId)) {
                $integration = $convertaxIntegrationModel->where("project_id", $projectId)->first();
                if ($integration) {
                    return response()->json(
                        [
                            "message" => "Projeto já integrado",
                        ],
                        400
                    );
                }
                if (empty($data["boleto_generated"])) {
                    $data["boleto_generated"] = 0;
                }
                if (empty($data["boleto_paid"])) {
                    $data["boleto_paid"] = 0;
                }
                if (empty($data["credit_card_paid"])) {
                    $data["credit_card_paid"] = 0;
                }
                if (empty($data["credit_card_refused"])) {
                    $data["credit_card_refused"] = 0;
                }
                if (empty($data["abandoned_cart"])) {
                    $data["abandoned_cart"] = 0;
                }

                $data["value"] = preg_replace("/[.,]/", "", $data["value"]);

                $integrationCreated = $convertaxIntegrationModel->create([
                    "link" => $data["link"],
                    "value" => $data["value"],
                    "boleto_generated" => $data["boleto_generated"],
                    "boleto_paid" => $data["boleto_paid"],
                    "credit_card_refused" => $data["credit_card_refused"],
                    "credit_card_paid" => $data["credit_card_paid"],
                    "abandoned_cart" => $data["abandoned_cart"],
                    "project_id" => $projectId,
                    "user_id" => auth()->user()->account_owner_id,
                ]);
                if (!empty($integrationCreated)) {
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
            Log::warning("Erro ao realizar integração  ConvertaXController - store");
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
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $convertaxIntegrationModel = new ConvertaxIntegration();
        $convertaxIntegration = $convertaxIntegrationModel->with(["project"])->find(current(Hashids::decode($id)));

        activity()
            ->on($convertaxIntegration)
            ->tap(function (Activity $activity) use ($convertaxIntegration) {
                $activity->log_name = "visualization";
                $activity->subject_id = $convertaxIntegration->id;
            })
            ->log("Visualizou tela integração do ConvertaX");

        return new ConvertaxResource($convertaxIntegration);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view("convertax::edit");
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $convertaxIntegrationModel = new ConvertaxIntegration();

            $data = $request->all();
            $data["value"] = preg_replace("/[.,]/", "", $data["value"]);

            $integrationId = current(Hashids::decode($id));
            $convertaxIntegration = $convertaxIntegrationModel->find($integrationId);
            if (!empty($convertaxIntegration)) {
                if (empty($data["boleto_generated"])) {
                    $data["boleto_generated"] = 0;
                }
                if (empty($data["boleto_paid"])) {
                    $data["boleto_paid"] = 0;
                }
                if (empty($data["credit_card_paid"])) {
                    $data["credit_card_paid"] = 0;
                }
                if (empty($data["credit_card_refused"])) {
                    $data["credit_card_refused"] = 0;
                }
                if (empty($data["abandoned_cart"])) {
                    $data["abandoned_cart"] = 0;
                }

                $integrationUpdated = $convertaxIntegration->update([
                    "link" => $data["link"],
                    "value" => $data["value"],
                    "boleto_generated" => $data["boleto_generated"],
                    "boleto_paid" => $data["boleto_paid"],
                    "credit_card_refused" => $data["credit_card_refused"],
                    "credit_card_paid" => $data["credit_card_paid"],
                    "abandoned_cart" => $data["abandoned_cart"],
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

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao atualizar a integração",
                ],
                400
            );

        } catch (Exception $e) {
            Log::warning("Erro ao tentar atualizar integração com convertaX (ConvertaXController - update)");
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao atualizar a integração",
                ],
                400
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $integrationId = current(Hashids::decode($id));
            $convertaxIntegrationModel = new ConvertaxIntegration();

            $integration = $convertaxIntegrationModel->find($integrationId);
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
        }
    }
}
