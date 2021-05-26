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

            if (!empty($validator['affiliate_id'])) {
                $validator['affiliate_id'] = hashids_decode($validator['affiliate_id']);
                $affiliateId = $validator['affiliate_id'];
            } else {
                $affiliateId = 0;
                $validator['affiliate_id'] = null;
            }

            $project = Project::find(hashids_decode($projectId));

            if (!Gate::allows('edit', [$project, $affiliateId])) {
                return response()->json(['message' => __('controller.pixel.permission.create')], 403);
            }

            $applyPlanEncoded = json_encode(foxutils()->getApplyPlans($validator['add_pixel_plans']));

            if ($validator['platform'] == 'google_adwords') {
                $validator['code'] = str_replace(['AW-'], '', $validator['code']);
            }

            if (!in_array($validator['platform'], ['taboola', 'outbrain'])) {
                $validator['purchase-event-name'] = null;
            }

            if (in_array($validator['platform'], ['taboola', 'outbrain']) && empty($validator['purchase-event-name'])) {
                $validator['purchase-event-name'] = $validator['platform'] == 'taboola' ? 'make_purchase' : 'Purchase';
            }

            $facebookToken = null;
            $isApi = false;
            if ($validator['platform'] == 'facebook' && !empty($validator['api-facebook']) && $validator['api-facebook'] == 'api') {
                $facebookToken = $validator['facebook-token-api'];
                $isApi = true;
            }

            Pixel::create(
                [
                    'project_id' => $project->id,
                    'name' => $validator['name'],
                    'code' => $validator['code'],
                    'platform' => $validator['platform'],
                    'status' => $validator['status'] == "true",
                    'checkout' => $validator['checkout'] == "true",
                    'purchase_boleto' => $validator['purchase_boleto'] == "true",
                    'purchase_card' => $validator['purchase_card'] == "true",
                    'affiliate_id' => $validator['affiliate_id'],
                    'campaign_id' => $validator['campaign'] ?? null,
                    'apply_on_plans' => $applyPlanEncoded,
                    'purchase_event_name' => $validator['purchase-event-name'],
                    'facebook_token' => $facebookToken,
                    'is_api' => $isApi,
                    'value_percentage_purchase_boleto' => empty($validator['value_percentage_purchase_boleto']) ? 100 : $validator['value_percentage_purchase_boleto']
                ]
            );

            return response()->json(
                [
                    'message' => 'Pixel ' . __('controller.success.create'),
                    'success' => true
                ],
                200
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => __('controller.error.generic'),
                    'success' => false
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

            activity()->on((new Pixel()))->tap(
                function (Activity $activity) use ($pixel) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = $pixel->id;
                }
            )->log(__('controller.pixel.log.visualization.edit ') . $pixel->name);

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

            return response()->json(__('controller.error.generic'), 400);
        }
    }

    public function update(PixelUpdateRequest $request, $projectId, $id): JsonResponse
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
            if (!in_array($validated['platform'], ['taboola', 'outbrain'])) {
                $validated['purchase_event_name'] = null;
            }


            if ($validated['platform'] == 'taboola' && empty($validated['purchase_event_name'] && empty($pixel->taboola_conversion_name))) {
                $validated['purchase_event_name'] = 'make_purchase';
            } elseif ($validated['platform'] == 'outbrain' && empty($validated['purchase_event_name']) && empty($pixel->outbrain_conversion_name)) {
                $validated['purchase_event_name'] = 'Purchase';
            } elseif ($validated['platform'] == 'facebook') {
                $validated['purchase_event_name'] = '';
                if ($validated['is_api'] == 'api') {
                    $validated['is_api'] = true;
                } else {
                    $validated['is_api'] = false;
                    $validated['facebook_token_api'] = null;
                }
            }

            if ($validated['platform'] != 'facebook') {
                $validated['is_api'] = false;
            }

            $pixelUpdated = $pixel->update(
                [
                    'name' => $validated['name'],
                    'platform' => $validated['platform'],
                    'status' => $validated['status'] == 'true',
                    'code' => $validated['code'],
                    'apply_on_plans' => $applyPlanEncoded,
                    'checkout' => $validated['checkout'] == 'true',
                    'purchase_boleto' => $validated['purchase_boleto'] == 'true',
                    'purchase_card' => $validated['purchase_card'] == 'true',
                    'purchase_event_name' => $validated['purchase_event_name'] ?? null,
                    'facebook_token' => $validated['facebook_token_api'],
                    'is_api' => $validated['is_api'],
                    'value_percentage_purchase_boleto' => $validated['value_percentage_purchase_boleto']
                ]
            );
            if ($pixelUpdated) {
                return response()->json('Sucesso', 200);
            }

            return response()->json(['message' => 'Erro ao tentar atualizar dados!'], 400);
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
                    'url_webhook_events_facebook' => $data['webhook-facebook-pixel'],
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
