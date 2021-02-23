<?php

namespace Modules\Pixels\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Pixel;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Project;
use Modules\Core\Services\PixelService;
use Modules\Pixels\Http\Requests\PixelStoreRequest;
use Modules\Pixels\Http\Requests\PixelUpdateRequest;
use Modules\Pixels\Transformers\PixelEditResource;
use Modules\Pixels\Transformers\PixelsResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class PixelsApiController
 * @package Modules\Pixels\Http\Controllers
 */
class PixelsApiController extends Controller
{
    /**
     * @param $projectId
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index($projectId)
    {
        try {
            if (empty($projectId)) {
                return response()->json(['message' => 'Erro ao listar dados de pixels'], 400);
            }

            $pixelModel = new Pixel();
            $projectModel = new Project();

            $project = $projectModel->find(current(Hashids::decode($projectId)));

            $affiliate = Affiliate::where('project_id', $project->id)
                ->where('user_id', auth()->user()->account_owner_id)
                ->first();

            $affiliateId = $affiliate->id ?? null;

            activity()->on($pixelModel)->tap(
                function (Activity $activity) {
                    $activity->log_name = 'visualization';
                }
            )->log('Visualizou tela todos os pixels para o projeto ' . $project->name);

            if (Gate::allows('edit', [$project, $affiliateId])) {
                $pixels = $pixelModel->where('project_id', $project->id)
                    ->where('affiliate_id', $affiliateId)
                    ->orderBy('id', 'DESC');

                return PixelsResource::collection($pixels->paginate(5));
            }

            return response()->json(['message' => 'Sem permissão para listar pixels'], 403);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao listar dados de pixels'], 400);
        }
    }

    public function store(PixelStoreRequest $request, $projectId): JsonResponse
    {
        try {
            $pixelModel = new Pixel();
            $projectModel = new Project();

            $validator = $request->validated();

            if (!$validator || !isset($projectId)) {
                return response()->json('Parametros inválidos', 400);
            }

            $validator['project_id'] = current(Hashids::decode($projectId));

            $affiliateId = 0;
            if (!empty($validator['affiliate_id'])) {
                $affiliateId = current(Hashids::decode($validator['affiliate_id']));
                $validator['affiliate_id'] = $affiliateId;
            } else {
                $validator['affiliate_id'] = null;
            }

            $project = $projectModel->find($validator['project_id']);

            if (!Gate::allows('edit', [$project, $affiliateId])) {
                return response()->json(['message' => 'Sem permissão para salvar pixels'], 403);
            }

            if ($validator['platform'] == 'google_adwords') {
                $order = ['AW-'];
                $validator['code'] = str_replace($order, '', $validator['code']);
            }

            $applyPlanArray = [];
            if (in_array('all', $validator['add_pixel_plans'])) {
                $applyPlanArray[] = 'all';
            } else {
                foreach ($validator['add_pixel_plans'] as $key => $value) {
                    $applyPlanArray[] = current(Hashids::decode($value));
                }
            }

            $applyPlanEncoded = json_encode($applyPlanArray);

            $codeMetaTag = $validator['code_meta_tag_facebook'] ?? null;

            $pixel = $pixelModel->create(
                [
                    'project_id' => $validator['project_id'],
                    'name' => $validator['name'],
                    'code' => $validator['code'],
                    'platform' => $validator['platform'],
                    'status' => $validator['status'],
                    'checkout' => $validator['checkout'],
                    'purchase_boleto' => $validator['purchase_boleto'],
                    'purchase_card' => $validator['purchase_card'],
                    'affiliate_id' => $validator['affiliate_id'],
                    'campaign_id' => $validator['campaign'] ?? null,
                    'apply_on_plans' => $applyPlanEncoded,
                    'purchase_event_name' => $codeMetaTag
                ]
            );


            if (!empty($codeMetaTag)) {
                (new PixelService())->updateCodeMetaTagFacebook($project->id, $codeMetaTag);
            }

            if ($pixel) {
                return response()->json('Pixel Configurado com sucesso!', 200);
            }

            return response()->json('Erro ao criar pixel', 400);
        } catch (Exception $e) {
            report($e);

            return response()->json('Erro ao criar pixel', 400);
        }
    }

    public function update(PixelUpdateRequest $request, $projectId, $id)
    {
        try {
            if (empty($id) || empty($projectId)) {
                return response()->json(['message' => 'Pixel nao encontrado'], 400);
            }

            $validated = $request->validated();

            $pixelModel = new Pixel();

            $pixel = $pixelModel->find(current(Hashids::decode($id)));

            if (empty($pixel)) {
                return response()->json(['message' => 'Pixel nao encontrado'], 400);
            }

            $projectModel = new Project();
            $project = $projectModel->find(current(Hashids::decode($projectId)));
            $affiliateId = (!empty($pixel->affiliate_id)) ? $pixel->affiliate_id : 0;

            if (!Gate::allows('edit', [$project, $affiliateId])) {
                return response()->json(['message' => 'Sem permissão para atualizar pixels'], 403);
            }

            if ($validated['platform'] == 'google_adwords') {
                $order = ['AW-'];
                $validated['code'] = str_replace($order, '', $validated['code']);
            }

            $applyPlanArray = [];
            if (in_array('all', $validated['edit_pixel_plans'])) {
                $applyPlanArray[] = 'all';
            } else {
                foreach ($validated['edit_pixel_plans'] as $key => $value) {
                    $applyPlanArray[] = current(Hashids::decode($value));
                }
            }

            $applyPlanEncoded = json_encode($applyPlanArray);

            if ($pixel->platform == 'taboola' && empty($validated['purchase_event_name'] && empty($pixel->taboola_conversion_name))) {
                $validated['purchase_event_name'] = 'make_purchase';
            }
            if ($pixel->platform == 'outbrain' && empty($validated['purchase_event_name']) && empty($pixel->outbrain_conversion_name)) {
                $validated['purchase_event_name'] = 'Purchase';
            }

            $pixelUpdated = $pixel->update(
                [
                    'name' => $validated['name'],
                    'platform' => $validated['platform'],
                    'status' => $validated['status'],
                    'code' => $validated['code'],
                    'apply_on_plans' => $applyPlanEncoded,
                    'checkout' => $validated['checkout'],
                    'purchase_boleto' => $validated['purchase_boleto'],
                    'purchase_card' => $validated['purchase_card'],
                    'purchase_event_name' => $validated['purchase_event_name'] ?? null
                ]
            );
            if ($pixelUpdated) {
                if (!empty($pixel->code_meta_tag_facebook) && empty($validated['code_meta_tag_facebook'])) {
                    $pixel->update(
                        [
                            'code_meta_tag_facebook' => ''
                        ]
                    );
                }

                if (!empty($validated['code_meta_tag_facebook'])) {
                    (new PixelService())->updateCodeMetaTagFacebook(
                        $project->id,
                        $validated['code_meta_tag_facebook']
                    );
                }
                return response()->json('Sucesso', 200);
            }

            return response()->json(['message' => 'Erro ao tentar atualizar dados!'], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao tentar atualizar dados!'], 400);
        }
    }

    /**
     * @param $projectId
     * @param $id
     * @return JsonResponse
     */
    public function destroy($projectId, $id)
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

    /**
     * @param $projectId
     * @param $id
     * @return JsonResponse
     */
    public function show($projectId, $id)
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

            activity()->on($pixelModel)->tap(
                function (Activity $activity) use ($id) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                }
            )->log('Visualizou tela detalhes do pixel: ' . $pixel->name);

            if (!Gate::allows('edit', [$project, $affiliateId])) {
                return response()->json(['message' => 'Sem permissão para visualizar pixels'], 403);
            }

            $pixel->makeHidden(['id', 'project_id', 'campaing_id']);

            return response()->json($pixel);
        } catch (Exception $e) {
            report($e);

            return response()->json('Erro ao buscar pixel', 400);
        }
    }

    /**
     * @param $projectId
     * @param $id
     * @return JsonResponse|PixelEditResource
     */
    public function edit($projectId, $id)
    {
        try {
            if (empty($projectId) || empty($id)) {
                return response()->json('Erro ao buscar pixel', 400);
            }

            $pixelModel = new Pixel();
            $projectModel = new Project();

            $pixel = $pixelModel->find(current(Hashids::decode($id)));

            if (empty($pixel)) {
                return response()->json('Erro ao buscar pixel', 400);
            }

            $project = $projectModel->find(current(Hashids::decode($projectId)));
            $affiliateId = (!empty($pixel->affiliate_id)) ? $pixel->affiliate_id : 0;

            if (!Gate::allows('edit', [$project, $affiliateId])) {
                return response()->json(['message' => 'Sem permissão para editar pixels'], 403);
            }

            activity()->on($pixelModel)->tap(
                function (Activity $activity) use ($id) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                }
            )->log('Visualizou tela editar pixel: ' . $pixel->name);

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
                            'plans.*',
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

            $pixel->apply_on_plans = $applyPlanArray;

            $pixel->makeHidden(['id', 'project_id', 'campaing_id']);

            return new PixelEditResource($pixel);
        } catch (Exception $e) {
            report($e);

            return response()->json('Erro ao buscar pixel', 400);
        }
    }
}
