<?php

namespace Modules\ProjectReviews\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ProjectReviews;
use Modules\Core\Entities\ProjectReviewsConfig;
use Modules\ProjectReviews\Http\Requests\ProjectReviewsStoreRequest;
use Modules\ProjectReviews\Http\Requests\ProjectReviewsUpdateRequest;
use Modules\ProjectReviews\Transformers\ProjectReviewsResource;
use Modules\ProjectUpsellRule\Transformers\ProjectsUpsellResource;
use Vinkla\Hashids\Facades\Hashids;

class ProjectReviewsApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $data = $request->all();
            $projectReviewsModel = new ProjectReviews();
            $projectId = current(Hashids::decode($data['project_id']));
            if ($projectId) {
                $projectUpsell = $projectReviewsModel->where('project_id', $projectId);

                return ProjectReviewsResource::collection($projectUpsell->paginate(5));
            } else {
                return response()->json([
                    'message' => 'Erro ao listar dados de reviews',
                ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar reviews (ProjectReviewsApiController - index)');
            report($e);

            return response()->json([
                'message' => 'Erro ao listar dados de reviews',
            ], 400);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return JsonResponse|ProjectReviewsResource
     */
    public function show($id)
    {
        $projectReviewsModel = new ProjectReviews();
        $reviewId = current(Hashids::decode($id));
        if ($reviewId) {
            $review = $projectReviewsModel->find($reviewId);

            return new ProjectReviewsResource($review);
        } else {
            return response()->json([
                'message' => 'Erro ao carregar dados da review',
            ], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse|ProjectsUpsellResource
     */
    public function edit($id)
    {
        $projectReviewsModel = new ProjectReviews();
        $reviewId = current(Hashids::decode($id));
        if ($reviewId) {
            $review = $projectReviewsModel->find($reviewId);

            return new ProjectReviewsResource($review);
        } else {
            return response()->json([
                'message' => 'Erro ao carregar dados da review',
            ], 400);
        }
    }

    /**
     * @param ProjectReviewsStoreRequest $request
     * @return JsonResponse
     */
    public function store(ProjectReviewsStoreRequest $request)
    {
        $projectReviewModel = new ProjectReviews();
        $data = $request->validated();
        $projectId = current(Hashids::decode($data['project_id']));
        $project = Project::find($projectId);

        if (!empty($project)) {
            $applyPlanArray = [];
            if (in_array('all', $data['apply_on_plans'])) {
                $applyPlanArray[] = 'all';
            } else {
                foreach ($data['apply_on_plans'] as $key => $value) {
                    $applyPlanArray[] = current(Hashids::decode($value));
                }
            }

            $applyPlanEncoded = json_encode($applyPlanArray);

            $projectReviewModel->create([
                'project_id'     => $projectId,
                'name'           => $data['name'],
                'description'    => $data['description'],
                'photo'          => $data['photo'] ?? null,
                'stars'          => $data['stars'] ?? 0,
                'active_flag'    => $data['active_flag'] ?? 0,
                'apply_on_plans' => $applyPlanEncoded,
            ]);

            if (!$project->reviewsConfig) {
                ProjectReviewsConfig::create([
                    'project_id' => $projectId
                ]);
            }

            return response()->json(['message' => 'Review criado com sucesso!'], 200);
        } else {
            return response()->json([
                'message' => 'Erro ao criar review',
            ], 400);
        }
    }

    /**
     * @param ProjectReviewsUpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ProjectReviewsUpdateRequest $request, $id)
    {
        $projectReviewModel = new ProjectReviews();
        $data = $request->validated();
        $reviewId = current(Hashids::decode($id));
        $review = $projectReviewModel->find($reviewId);

        if ($reviewId && !empty($review)) {
            $applyPlanArray = [];
            if (in_array('all', $data['apply_on_plans'])) {
                $applyPlanArray[] = 'all';
            } else {
                foreach ($data['apply_on_plans'] as $key => $value) {
                    $applyPlanArray[] = current(Hashids::decode($value));
                }
            }
            $applyPlanEncoded = json_encode($applyPlanArray);

            $reviewUpdated = $review->update([
                'name'           => $data['name'],
                'description'    => $data['description'],
                'photo'          => $data['photo'] ?? null,
                'stars'          => $data['stars'] ?? 0,
                'active_flag'    => $data['active_flag'] ?? 0,
                'apply_on_plans' => $applyPlanEncoded,
            ]);

            if ($reviewUpdated) {
                return response()->json(['message' => 'Review atualizado com sucesso!'], 200);
            } else {
                return response()->json([
                    'message' => 'Erro ao atualizar review',
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'Review não encontrado',
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            if (empty($id)) {
                return response()->json([
                    'message' => 'Erro ao deletar review',
                ], 404);
            }

            $projectReviewModel = new ProjectReviews();
            $review = $projectReviewModel->find(current(Hashids::decode($id)));

            if (!empty($review)) {
                $reviewDeleted = $review->delete();
                if ($reviewDeleted) {
                    return response()->json(['message' => 'Review deletada com sucesso!']);
                }
            }

            return response()->json([
                'message' => 'Erro ao deletar review',
            ], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Erro ao deletar review',
            ], 400);
        }
    }
}
