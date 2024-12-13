<?php

namespace Modules\Whatsapp2\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\Whatsapp2Integration;
use Modules\Projects\Transformers\ProjectsSelectResource;
use Modules\Whatsapp2\Transformers\Whatsapp2Resource;
use Spatie\Activitylog\Models\Activity;

class Whatsapp2ApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            activity()->on((new Whatsapp2Integration()))->tap(function (Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todos as integrações whatsapp 2.0');

            $accountOwnerId = auth()->user()->getAccountOwnerId();

            $whatsapp2Integrations = Whatsapp2Integration::with(['project', 'project.usersProjects'])
            ->whereHas(
                'project.usersProjects',
                function ($query) {
                    $query
                    ->where('company_id', auth()->user()->company_default)
                    ->where('user_id', auth()->user()->getAccountOwnerId());
                }
            )->get();

            $projects = collect();
            $userProjects = UserProject::with([
                'project' => function ($query) {
                    $query->where('status', Project::STATUS_ACTIVE);
                }
            ])->where([['user_id', $accountOwnerId],[
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
                'integrations' => Whatsapp2Resource::collection($whatsapp2Integrations),
                'projects' => ProjectsSelectResource::collection($projects),
                'token_whatsapp2' => hashids_encode($accountOwnerId, 'whatsapp2_token'),
            ]);
        } catch (Exception $e) {
            return response()->json(["message" => __('messages.unexpected_error')], 400);
        }
    }

    public function show($id)
    {
        try {
            $whatsapp2Integration = Whatsapp2Integration::find(hashids_decode($id));

            activity()
                ->on(new Whatsapp2Integration())
                ->tap(function (Activity $activity) use ($id) {
                    $activity->log_name = "visualization";
                    $activity->subject_id = hashids_decode($id);
                })
                ->log(
                    "Visualizou tela editar configurações de integração projeto " .
                        $whatsapp2Integration->project->name .
                        " com whatsapp 2.0"
                );

            return new Whatsapp2Resource($whatsapp2Integration);
        } catch (Exception $e) {
            return response()->json(["message" => __('messages.unexpected_error')], 400);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            $projectId = hashids_decode($data["project_id"]);
            if (empty($projectId)) {
                return response()->json(["message" => "Ocorreu um erro ao realizar a integração"], 400);
            }

            $integration = Whatsapp2Integration::where("project_id", $projectId)->first();
            if ($integration) {
                return response()->json(["message" => "Projeto já integrado"], 400);
            }

            if (empty($data["url_checkout"]) || empty($data["url_order"])) {
                return response()->json(["message" => "URl Checkout e URL pedido são obrigatórios!"], 400);
            }
            if (!filter_var($data["url_checkout"], FILTER_VALIDATE_URL)) {
                return response()->json(["message" => "URL Checkout inválido!"], 400);
            }
            if (!filter_var($data["url_order"], FILTER_VALIDATE_URL)) {
                return response()->json(["message" => "URL Pedido inválido!"], 400);
            }

            $integrationCreated = Whatsapp2Integration::create([
                "api_token" => hashids_encode(auth()->user()->account_owner_id, "whatsapp2_token"),
                "url_order" => $data["url_order"],
                "url_checkout" => $data["url_checkout"],
                "billet_generated" => empty($data["boleto_generated"]) ? 0 : $data["boleto_generated"],
                "billet_paid" => empty($data["boleto_paid"]) ? 0 : $data["boleto_paid"],
                "credit_card_refused" => empty($data["credit_card_refused"]) ? 0 : $data["credit_card_refused"],
                "credit_card_paid" => empty($data["credit_card_paid"]) ? 0 : $data["credit_card_paid"],
                "abandoned_cart" => empty($data["abandoned_cart"]) ? 0 : $data["abandoned_cart"],
                "pix_expired" => empty($data["pix_expired"]) ? 0 : $data["pix_expired"],
                "pix_paid" => empty($data["pix_paid"]) ? 0 : $data["pix_paid"],
                "project_id" => $projectId,
                "user_id" => auth()->user()->account_owner_id,
            ]);

            return response()->json(["message" => "Integração criada com sucesso!"]);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro ao realizar a integração"], 400);
        }
    }

    public function edit($id): JsonResponse
    {
        try {
            if (empty($id)) {
                return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde!"], 400);
            }

            activity()
                ->on(new Whatsapp2Integration())
                ->tap(function (Activity $activity) use ($id) {
                    $activity->log_name = "visualization";
                    $activity->subject_id = hashids_decode($id);
                })
                ->log("Visualizou tela editar configurações da integração SAK");

            $integration = Whatsapp2Integration::where("project_id", hashids_decode($id))->first();

            if ($integration) {
                return response()->json(["integration" => $integration]);
            }

            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde!"], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde!"], 400);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $data = $request->all();
            $whatsapp2Integration = Whatsapp2Integration::find(hashids_decode($id));

            if (empty($whatsapp2Integration)) {
                return response()->json(["message" => "Ocorreu um erro!"], 400);
            }

            if (empty($data["url_checkout"]) || empty($data["url_order"])) {
                return response()->json(["message" => "URl Checkout e URL pedido são obrigatórios!"], 400);
            }
            if (!filter_var($data["url_checkout"], FILTER_VALIDATE_URL)) {
                return response()->json(["message" => "URL Checkout inválido!"], 400);
            }
            if (!filter_var($data["url_order"], FILTER_VALIDATE_URL)) {
                return response()->json(["message" => "URL Pedido inválido!"], 400);
            }

            $whatsapp2Integration->update([
                "url_order" => $data["url_order"],
                "url_checkout" => $data["url_checkout"],
                "billet_generated" => empty($data["boleto_generated"]) ? 0 : $data["boleto_generated"],
                "billet_paid" => empty($data["boleto_paid"]) ? 0 : $data["boleto_paid"],
                "credit_card_refused" => empty($data["credit_card_refused"]) ? 0 : $data["credit_card_refused"],
                "credit_card_paid" => empty($data["credit_card_paid"]) ? 0 : $data["credit_card_paid"],
                "abandoned_cart" => empty($data["abandoned_cart"]) ? 0 : $data["abandoned_cart"],
                "pix_expired" => empty($data["pix_expired"]) ? 0 : $data["pix_expired"],
                "pix_paid" => empty($data["pix_paid"]) ? 0 : $data["pix_paid"],
            ]);
            return response()->json(["message" => "Integração atualizada com sucesso!"]);
        } catch (Exception $e) {
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
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $integrationId = hashids_decode($id);
            $whatsapp2IntegrationModel = new Whatsapp2Integration();
            $integration = $whatsapp2IntegrationModel->find($integrationId);
            if (empty($integration)) {
                return response()->json(
                    [
                        "message" => "Erro ao tentar remover Integração",
                    ],
                    400
                );
            } else {
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
            }
        } catch (Exception $e) {
            Log::warning("Erro ao tentar remover Integração Whatsapp 2.0 (Whatsapp2Controller - destroy)");
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao tentar remover, tente novamente mais tarde!",
                ],
                400
            );
        }
    }
}
