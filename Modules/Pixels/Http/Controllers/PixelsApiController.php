<?php

namespace Modules\Pixels\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Pixel;
use Modules\Core\Entities\PixelConfig;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Project;
use Modules\Core\Services\PixelService;
use Modules\Pixels\Http\Requests\PixelStoreRequest;
use Modules\Pixels\Http\Requests\PixelUpdateRequest;
use Modules\Pixels\Transformers\PixelEditResource;
use Modules\Pixels\Transformers\PixelsResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class PixelsApiController extends Controller
{
    public function index($projectId)
    {
        try {
            if (empty($projectId)) {
                return response()->json(['message' => __('controller.error.generic')], 400);
            }

            $project = Project::find(hashids_decode($projectId));

            $affiliate = Affiliate::where('project_id', $project->id)
                ->where('user_id', auth()->user()->account_owner_id)
                ->first();

            $affiliateId = $affiliate->id ?? null;

            if (!Gate::allows('edit', [$project, $affiliateId])) {
                return response()->json(['message' => __('controller.pixel.permission.index')], 403);
            }

            activity()->on((new Pixel()))->tap(
                function (Activity $activity) {
                    $activity->log_name = 'visualization';
                }
            )->log(__('controller.pixel.log.visualization.index') . ' ' . $project->name);


            $pixels = Pixel::where('project_id', $project->id)
                ->where('affiliate_id', $affiliateId)
                ->orderBy('id', 'DESC');

            return PixelsResource::collection($pixels->paginate(5));
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => __('controller.error.generic')], 400);
        }
    }

    public function store(PixelStoreRequest $request, $projectId): JsonResponse
    {
        try {
            $validator = $request->validated();

            if (!$validator || !isset($projectId)) {
                return response()->json(['message' => __('controller.error.generic')], 400);
            }

            $result = (new PixelService())->store($projectId, $validator);

            return response()->json(['message' => $result['message']], $result['status']);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => __('controller.error.generic')
                ],
                400
            );
        }
    }

    public function edit($projectId, $id)
    {
        try {
            if (empty($projectId) || empty($id)) {
                return response()->json(__('controller.error.generic'), 400);
            }

            $pixel = Pixel::find(hashids_decode($id));

            if (empty($pixel)) {
                return response()->json(__('controller.error.generic'), 400);
            }

            $project = Project::find(hashids_decode($projectId));
            $affiliateId = (!empty($pixel->affiliate_id)) ? $pixel->affiliate_id : 0;

            if (!Gate::allows('edit', [$project, $affiliateId])) {
                return response()->json(['message' => __('controller.pixel.permission.edit')], 403);
            }

            $applyPlanArray = [];
            $planModel = new Plan();

            if (!empty($pixel->apply_on_plans)) {
                $applyPlanDecoded = json_decode($pixel->apply_on_plans);
                if (in_array('all', $applyPlanDecoded)) {
                    $applyPlanArray[] = [
                        'id' => 'all',
                        'name' => 'Todos os Planos',
                        'description' => '',
                    ];
                } else {
                    foreach ($applyPlanDecoded as $key => $value) {
                        $plan = $planModel->select(
                            'plans.id',
                            'plans.name',
                            'plans.description',
                            DB::raw(
                                '(select sum(if(p.shopify_id is not null and p.shopify_id = plans.shopify_id, 1, 0)) from plans p where p.deleted_at is null) as variants'
                            )
                        )->find($value);
                        if (!empty($plan)) {
                            $applyPlanArray[] = [
                                'id' => Hashids::encode($plan->id),
                                'name' => $plan->name,
                                'description' => $plan->variants ? $plan->variants . ' variantes' : $plan->description,
                            ];
                        }
                    }
                }
            }

            $pixel->event_select = '';
            if ($pixel->platform == 'google_adwords') {
                foreach (PixelService::EVENTS as $EVENT) {
                    if ($pixel->$EVENT == true) {
                        $pixel->event_select = $EVENT;
                    }
                }
            }

            $pixel->apply_on_plans = $applyPlanArray;

            $pixel->makeHidden(['id', 'project_id', 'campaing_id']);

            return new PixelEditResource($pixel);
        } catch (Exception $e) {
            report($e);

            return response()->json(__('controller.error.generic'), 400);
        }
    }

    public function update(PixelUpdateRequest $request, $projectId, $id): JsonResponse
    {
        try {
            if (empty($id) || empty($projectId)) {
                return response()->json(['message' => 'Pixel nao encontrado'], 400);
            }

            $result = (new PixelService())->update($id, $request->validated());
            return response()->json(['message' => $result['message']], $result['status']);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao tentar atualizar dados!'], 400);
        }
    }

    public function destroy($projectId, $id): JsonResponse
    {
        try {
            if (empty($projectId) || empty($id)) {
                return response()->json(['message' => 'Pixel nao encontrado'], 400);
            }

            $pixelModel = new Pixel();
            $projectModel = new Project();

            $pixel = $pixelModel->find(current(Hashids::decode($id)));
            $projectId = current(Hashids::decode($projectId));
            $project = $projectModel->find($projectId);
            $affiliateId = (!empty($pixel->affiliate_id)) ? $pixel->affiliate_id : 0;

            if (!Gate::allows('edit', [$project, $affiliateId])) {
                return response()->json(['message' => 'Sem permissão para remover pixels'], 403);
            }

            $pixelDeleted = $pixel->delete();

            if ($pixelDeleted) {
                return response()->json('sucesso', 200);
            }

            return response()->json(['message' => 'Erro ao tentar excluir pixel'], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao tentar excluir pixel'], 400);
        }
    }

    public function show($projectId, $id): JsonResponse
    {
        try {
            if (empty($id) || empty($projectId)) {
                return response()->json('Pixel nao encontrado', 400);
            }

            $pixelModel = new Pixel();
            $projectModel = new Project();

            $pixel = $pixelModel->find(current(Hashids::decode($id)));

            if (empty($pixel)) {
                return response()->json('Pixel nao encontrado', 400);
            }

            $project = $projectModel->find(current(Hashids::decode($projectId)));
            $affiliateId = (!empty($pixel->affiliate_id)) ? $pixel->affiliate_id : 0;
            $pixel->platform_enum = Lang::get('definitions.enum.pixel.platform.' . $pixel->platform);

            if (!Gate::allows('edit', [$project, $affiliateId])) {
                return response()->json(['message' => 'Sem permissão para visualizar pixels'], 403);
            }

            activity()->on($pixelModel)->tap(
                function (Activity $activity) use ($id) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                }
            )->log('Visualizou tela detalhes do pixel: ' . $pixel->name);

            $pixel->makeHidden(['id', 'project_id', 'campaing_id']);

            return response()->json($pixel);
        } catch (Exception $e) {
            report($e);

            return response()->json('Erro ao buscar pixel', 400);
        }
    }

    public function getPixelConfigs($projectId): JsonResponse
    {
        try {
            $project = Project::with('pixelConfigs')->find(hashids_decode($projectId));

            if (empty($project->pixelConfigs)) {
                PixelConfig::create(['project_id' => $project->id]);
            };

            $project->load('pixelConfigs');
            return response()->json(
                [
                    'data' => $project->pixelConfigs->makeHidden(
                        ['id', 'project_id', 'created_at', 'updated_at', 'deleted_at']
                    ),
                    'message' => '',
                    'success' => true,
                ]
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'data' => '',
                    'message' => 'Ocorreu um erro tente novamente mais tarde!',
                    'success' => false,
                ],
                400
            );
        }
    }

    public function storePixelConfigs(Request $request, $projectId): JsonResponse
    {
        try {
            $data = $request->all();

            $project = Project::with('pixelConfigs')->find(hashids_decode($projectId));

            if (empty($project) || empty($project->pixelConfigs)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Ocorreu um erro, tente novamente mais tarde'
                    ]
                );
            }

            $project->pixelConfigs->update(
                [
                    'metatags_facebook' => $data['metatag-verification-facebook'],
                ]
            );

            return response()->json(
                [
                    'data' => '',
                    'success' => true,
                    'message' => 'Configuração de Pixels atualizada com sucesso'
                ]
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocorreu um erro, tente novamente mais tarde'
                ],
                400
            );
        }
    }

}
