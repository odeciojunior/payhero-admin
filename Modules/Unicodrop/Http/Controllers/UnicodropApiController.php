<?php

namespace Modules\Unicodrop\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ReportanaIntegration;
use Modules\Core\Entities\UnicodropIntegration;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ProjectService;
use Modules\Projects\Transformers\ProjectsSelectResource;
use Modules\Unicodrop\Transformers\UnicodropResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class UnicodropApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $ownerId = $user->getAccountOwnerId();

            $unicodropIntegrations = UnicodropIntegration::with(['project', 'project.usersProjects'])
            ->whereHas(
                'project.usersProjects',
                function ($query) {
                    $query
                    ->where('company_id', auth()->user()->company_default)
                    ->where('user_id', auth()->user()->getAccountOwnerId());
                }
            )->get();

            $projects = collect();
            $userProjects = UserProject::where([[
                'user_id', $ownerId],[
                'company_id', $user->company_default
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
                "integrations" => UnicodropResource::collection($unicodropIntegrations),
                "projects" => ProjectsSelectResource::collection($projects),
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => __('messages.unexpected_error')], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view("unicodrop::create");
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
            $projectId = hashids_decode($data["project_id"]);

            if (empty($projectId)) {
                return response()->json(["message" => "Ocorreu um erro ao realizar a integração"], 400);
            }

            $integration = UnicodropIntegration::where("project_id", $projectId)->first();
            if (!empty($integration)) {
                return response()->json(["message" => "Projeto já integrado"], 400);
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
            if (empty($data["pix"])) {
                $data["pix"] = 0;
            }

            UnicodropIntegration::create([
                "token" => $data["token"],
                "billet_generated" => $data["boleto_generated"],
                "billet_paid" => $data["boleto_paid"],
                "credit_card_refused" => $data["credit_card_refused"],
                "credit_card_paid" => $data["credit_card_paid"],
                "abandoned_cart" => $data["abandoned_cart"],
                "pix" => $data["pix"],
                "project_id" => $projectId,
                "user_id" => auth()->user()->account_owner_id,
            ]);

            return response()->json(["message" => "Integração criada com sucesso!"], 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => __('messages.unexpected_error')], 400);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        try {
            $unicodropIntegration = UnicodropIntegration::find(current(Hashids::decode($id)));

            return new UnicodropResource($unicodropIntegration);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => __('messages.unexpected_error')], 400);
        }
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
            $data = $request->all();
            $integrationId = hashids_decode($id);
            $integration = UnicodropIntegration::find($integrationId);

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
            if (empty($data["abandoned_cart"])) {
                $data["abandoned_cart"] = 0;
            }
            if (empty($data["pix"])) {
                $data["pix"] = 0;
            }

            $integration->update([
                "token" => $data["token"],
                "billet_generated" => $data["boleto_generated"],
                "billet_paid" => $data["boleto_paid"],
                "credit_card_refused" => $data["credit_card_refused"],
                "credit_card_paid" => $data["credit_card_paid"],
                "abandoned_cart" => $data["abandoned_cart"],
                "pix" => $data["pix"],
            ]);

            return response()->json(["message" => "Integração atualizada com sucesso"], 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Ocorreu um erro ao atualizar a integração"], 400);
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
            $integrationId = hashids_decode($id);
            $integration = UnicodropIntegration::find($integrationId);

            if (empty($integration)) {
                return response()->json(["message" => "Erro ao tentar remover Integração"], 400);
            }

            $integration->delete();

            return response()->json(["message" => "Integração Removida com sucesso!"], 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(
                ["message" => "Ocorreu um erro ao tentar remover, tente novamente mais tarde!"],
                400
            );
        }
    }
}
