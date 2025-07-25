<?php

namespace Modules\DemoAccount\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ProjectService;
use Modules\Core\Transformers\CompaniesSelectResource;
use Spatie\Activitylog\Models\Activity;
use Modules\Projects\Http\Controllers\ProjectsApiController;
use Modules\Projects\Transformers\ProjectsResource;
use Modules\Projects\Transformers\UserProjectResource;
use Modules\Shopify\Transformers\ShopifyIntegrationsResource;
use Vinkla\Hashids\Facades\Hashids;

class ProjectsApiDemoController extends ProjectsApiController
{

    public function show($id)
    {
        try {
            $userId = User::DEMO_ID;

            $id = hashids_decode($id);
            $project = Project::where('id', $id)
                ->where('status', Project::STATUS_ACTIVE)
                ->with(
                    [
                        'affiliates' => function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        },
                        'usersProjects.company'
                    ]
                )->first();

            if (empty($project)) {
                return response()->json([
                    'message' => 'Projeto não encontrado!',
                    'account_is_approved' => true
                ], 400);
            }

            $resume = $this->getProjectResume($project->id,$userId);

            $project->chargeback_count = $resume['chargeback_count'];
            $project->without_tracking = $resume['without_tracking'];
            $project->approved_sales = $resume['approved_sales'];
            $project->approved_sales_value = $resume['approved_sales_value'];
            $project->open_tickets = $resume['open_tickets'];
            $project->producer = $resume['producer'];

            return new ProjectsResource($project);
            
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao exibir detalhes do projeto'], 400);
        }
    }

    public function edit($id): JsonResponse
    {
        try {
            $user = User::with('companies')->find(User::DEMO_ID);

            $project = Project::with(
                [
                    'usersProjects',
                    'usersProjects.company' =>
                        function ($query) use ($user) {
                            $query->where('user_id', $user->account_owner_id);
                        }
                ]
            )->find(hashids_decode($id));

            $userProject = UserProject::where('user_id', $user->account_owner_id)
            ->where('project_id', hashids_decode($id))->first();

            $userProject = new UserProjectResource($userProject);

            $shopifyIntegrations = ShopifyIntegration::where('user_id', $user->account_owner_id)
            ->where('project_id', hashids_decode($id))->get();

            $shopifyIntegrations = ShopifyIntegrationsResource::collection($shopifyIntegrations);

            $companies = CompaniesSelectResource::collection($user->companies);

            $project = new ProjectsResource($project);

            return response()->json(compact('companies', 'project', 'userProject', 'shopifyIntegrations'));
            
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Erro ao carregar configurações do projeto',
                ],
                400
            );
        }
    }
}