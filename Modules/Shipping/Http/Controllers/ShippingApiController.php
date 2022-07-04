<?php

namespace Modules\Shipping\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Modules\Core\Services\CacheService;
use Modules\Shipping\Http\Requests\ShippingStoreRequest;
use Modules\Shipping\Http\Requests\ShippingUpdateRequest;
use Modules\Shipping\Transformers\ShippingResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ShippingApiController
 * @package Modules\Shipping\Http\Controllers
 */
class ShippingApiController extends Controller
{
    /**
     * @param $projectId
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index($projectId)
    {
        try {
            $shippingModel = new Shipping();
            $projectModel = new Project();

            if (isset($projectId)) {

                $project = $projectModel->find(current(Hashids::decode($projectId)));

                activity()->on($shippingModel)->tap(function (Activity $activity) {
                    $activity->log_name = 'visualization';
                })->log('Visualizou tela todos os fretes do projeto ' . $project->name);

                if (Gate::allows('edit', [$project])) {
                    $shippings = $shippingModel->where('project_id', $project->id);

                    return ShippingResource::collection($shippings->orderBy('id', 'DESC')->paginate(5));
                } else {
                    return response()->json([
                        'message' => 'Sem permissão para visualizar os fretes',
                    ], 400);
                }
            } else {
                return response()->json([
                    'message' => 'Erro ao listar dados de frete',
                ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados (ShippingController - index)');
            report($e);

            return response()->json([
                'message' => 'Erro ao listar dados de frete',
            ], 400);
        }
    }

    /**
     * @param ShippingStoreRequest $request
     * @param $projectId
     * @return JsonResponse
     */
    public function store(ShippingStoreRequest $request, $projectId)
    {
        try {
            $shippingModel = new Shipping();
            $projectModel = new Project();

            $shippingValidated = $request->validated();


            if ($shippingValidated) {
                $shippingValidated['project_id'] = current(Hashids::decode($projectId));

                $project = $projectModel->find($shippingValidated['project_id']);

                if (Gate::allows('edit', [$project])) {
                    if ($shippingValidated['value'] == null || preg_replace("/[^0-9]/", "",
                            $shippingValidated['value']) == 0) {
                        $shippingValidated['value'] = '0,00';
                    }
                    if (empty($shippingValidated['status'])) {
                        $shippingValidated['status'] = 0;
                        $shippingValidated['pre_selected'] = 0;
                    }
                    if (empty($shippingValidated['pre_selected'])) {
                        $shippingValidated['pre_selected'] = 0;
                    }
                    if ($shippingValidated['pre_selected'] && $shippingValidated['status']) {
                        $shippings = $shippingModel->where([
                            'project_id' => $shippingValidated['project_id'],
                            'pre_selected' => 1,
                        ])->first();
                        if ($shippings) {
                            $shippings->update(['pre_selected' => 0]);
                        }
                    }
                    $shippingValidated['rule_value'] = preg_replace('/[^0-9]/', '', $shippingValidated['rule_value']);
                    if (empty($shippingValidated['rule_value'])) {
                        $shippingValidated['rule_value'] = 0;
                    }

                    if (Str::contains($shippingValidated['type'], 'melhorenvio')) {
                        $hashid = str_replace('melhorenvio-', '', $shippingValidated['type']);
                        $melhorenvioId = hashids_decode($hashid);
                        $shippingValidated['melhorenvio_integration_id'] = $melhorenvioId;
                        $shippingValidated['type'] = 'melhorenvio';
                    }

                    $shippingValidated['type_enum'] = $shippingModel->present()->getTypeEnum($shippingValidated['type']);

                    $applyPlanArray = $this->getDecodedPlanIds($shippingValidated['apply_on_plans']);
                    $shippingValidated['apply_on_plans'] = json_encode($applyPlanArray);

                    $notApplyPlanArray = [];
                    if (isset($shippingValidated['not_apply_on_plans']) && !in_array('all', $shippingValidated['not_apply_on_plans'])) {
                        foreach ($shippingValidated['not_apply_on_plans'] as $key => $value) {
                            $notApplyPlanArray[] = current(Hashids::decode($value));
                        }
                    }
                    $shippingValidated['not_apply_on_plans'] = json_encode($notApplyPlanArray);

                    $shippingCreated = $shippingModel->create($shippingValidated);
                    if ($shippingCreated) {
                        CacheService::forgetContainsUnique(CacheService::SHIPPING_RULES, $shippingValidated['project_id']);
                        return response()->json(['message' => 'Frete cadastrado com sucesso!'], 200);
                    }

                    return response()->json(['message' => 'Erro ao tentar cadastrar frete!'], 400);
                } else {
                    return response()->json(['message' => 'Sem permissão para cadastrar um frete'], 403);
                }
            }

            return response()->json([
                'message' => 'Erro ao tentar cadastrar frete',
            ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar cadastrar frete (ShippingController - store)');
            report($e);

            return response()->json([
                'message' => 'Erro ao tentar cadastrar frete',
            ], 500);
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
            if (isset($projectId) && isset($id)) {
                $shippingModel = new Shipping();
                $projectModel = new Project();

                $shipping = $shippingModel->find(current(Hashids::decode($id)));
                $project = $projectModel->find(current(Hashids::decode($projectId)));

                activity()->on($shippingModel)->tap(function (Activity $activity) use ($id) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                })->log('Visualizou tela detalhes do frete ' . $shipping->name);

                if (Gate::allows('edit', [$project])) {

                    if ($shipping) {
                        $shipping->makeHidden(['id', 'project_id', 'campaing_id']);
                        $shipping->rule_value = number_format($shipping->rule_value / 100, 2, ',', '.');

                        return response()->json($shipping, 200);
                    } else {
                        return response()->json(['message' => 'Erro ao tentar visualizar frete!'], 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para visualizar este frete'], 403);
                }
            }

            return response()->json(['message' => 'Erro ao tentar visualizar frete!'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados (ShippingController - show)');
            report($e);

            return response()->json(['message' => 'Erro ao tentar visualizar frete!'], 400);
        }
    }

    /**
     * @param $projectId
     * @param $id
     * @return JsonResponse
     */
    public function edit($projectId, $id)
    {
        try {
            if (isset($projectId) && isset($id)) {
                $shippingModel = new Shipping();
                $projectModel = new Project();

                $shipping = $shippingModel->find(current(Hashids::decode($id)));
                $project = $projectModel->find(current(Hashids::decode($projectId)));

                activity()->on($projectModel)->tap(function (Activity $activity) use ($shipping) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = $shipping->id;
                })->log('Visualizou tela editar configuração do frete ' . $shipping->name);

                if (Gate::allows('edit', [$project])) {
                    if ($shipping) {
                        $shipping->makeHidden(['id', 'project_id', 'campaing_id'])->unsetRelation('project');

                        return response()->json(ShippingResource::make($shipping), 200);
                    } else {
                        return response()->json(['message' => 'Erro ao tentar editar o frete!'], 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para editar este frete'], 403);
                }
            }

            return response()->json(['message' => 'Erro ao tentar editar frete!'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar pagina editar frete (ShippingController - edit)');
            report($e);

            return response()->json(['message' => 'Erro ao tentar editar frete!'], 400);
        }
    }

    public function update(ShippingUpdateRequest $request, $projectId, $id)
    {
        try {
            $requestValidated = $request->validated();

            if (empty($requestValidated['pre_selected'])) {
                $requestValidated['pre_selected'] = 0;
            }
            if (empty($requestValidated['status'])) {
                $requestValidated['status'] = 0;
                $requestValidated['pre_selected'] = 0;
            }

            if (isset($requestValidated) && isset($projectId) && isset($id)) {

                $shippingModel = new Shipping();
                $projectModel = new Project();

                $shipping = $shippingModel->find(current(Hashids::decode($id)));
                $project = $projectModel->find(current(Hashids::decode($projectId)));

                if (Gate::allows('edit', [$project])) {
                    // if($requestValidated['regions_values']){
                    //     Log::debug($requestValidated['regions_values']);
                    // }
                    if ($requestValidated['value'] == null || preg_replace("/[^0-9]/", "",
                            $requestValidated['value']) == 0) {
                        $requestValidated['value'] = '0,00';
                    }
                    if ($requestValidated['pre_selected'] && !$shipping->pre_selected) {
                        $s = $shippingModel->where([
                            ['project_id', $shipping->project_id],
                            ['id', '!=', $shipping->id],
                            ['pre_selected', 1],
                        ])->first();

                        if ($s) {
                            $s->update(['pre_selected' => 0]);
                        }
                    }
                    $requestValidated['rule_value'] = preg_replace('/[^0-9]/', '', $requestValidated['rule_value']);
                    if (empty($requestValidated['rule_value'])) {
                        $requestValidated['rule_value'] = 0;
                    }

                    $applyPlanArray = $this->getDecodedPlanIds($requestValidated['apply_on_plans']);
                    $requestValidated['apply_on_plans'] = $applyPlanArray;

                    $notApplyPlanArray = [];
                    if (isset($requestValidated['not_apply_on_plans']) && !in_array('all', $requestValidated['not_apply_on_plans'])) {
                        foreach ($requestValidated['not_apply_on_plans'] as $key => $value) {
                            $notApplyPlanArray[] = current(Hashids::decode($value));
                        }
                    }
                    $requestValidated['not_apply_on_plans'] = $notApplyPlanArray;

                    if (!$this->verifyAllPlansHasActiveShippings($shipping, array_diff($applyPlanArray, $notApplyPlanArray), $requestValidated['status'])) {
                        return response()->json(['message' => 'Impossível editar, existem planos que ficarão sem nenhum frete vinculado!'], 400);
                    }

                    if (Str::contains($requestValidated['type'], 'melhorenvio')) {
                        $hashid = str_replace('melhorenvio-', '', $requestValidated['type']);
                        $melhorenvioId = hashids_decode($hashid);
                        $requestValidated['melhorenvio_integration_id'] = $melhorenvioId;
                        $requestValidated['type'] = 'melhorenvio';
                    }

                    $requestValidated['type_enum'] = $shippingModel->present()->getTypeEnum($requestValidated['type']);

                    $oldApplyOnPlans = json_decode($shipping->apply_on_plans);
                    $oldNotApplyOnPlans = json_decode($shipping->not_apply_on_plans);

                    $shippingUpdated = $shipping->update($requestValidated);

                    if (!$requestValidated['pre_selected'] && !$shipping->pre_selected) {
                        $sp = $shippingModel->where([
                            ['project_id', $shipping->project_id],
                            ['pre_selected', 1],
                        ])->get();

                        if (count($sp) == 0) {
                            $shipp = $shippingModel->where([
                                'project_id' => $shipping->project_id,
                                'status' => 1
                            ])->first();
                            $shipp->update(['pre_selected' => 1]);
                        }
                    }

                    $mensagem = "Frete atualizado com sucesso!";
                    if ($shippingUpdated) {
                        $shippings = $shippingModel->where([['project_id', $shipping->project_id], ['status', 1]])
                            ->get();

                        if (count($shippings) == 0) {
                            $sh = $shippingModel->where(['project_id' => $shipping->project_id])->first();

                            $sh->update(['status' => 1]);
                            $mensagem = 'É obrigatório deixar um frete ativado';
                        }

                        foreach ($oldApplyOnPlans as $plan) {
                            if($plan !== 'all') {
                                CacheService::forget(CacheService::SHIPPING_PLAN, $plan);
                                CacheService::forget(CacheService::SHIPPING_PLAN_VARIANTS, $plan);
                            }
                        }
                        foreach ($oldNotApplyOnPlans as $plan) {
                            CacheService::forget(CacheService::SHIPPING_PLAN, $plan);
                            CacheService::forget(CacheService::SHIPPING_PLAN_VARIANTS, $plan);
                        }
                        CacheService::forgetContainsUnique(CacheService::SHIPPING_RULES, $shipping->project_id);

                        return response()->json([
                            'message' => $mensagem,
                        ], 200);
                    }

                    return response()->json([
                        'message' => 'Erro ao tentar atualizar dados!',
                    ], 400);
                } else {
                    return response()->json([
                        'message' => 'Sem permissão para atualizar o frete',
                    ], 403);
                }
            } else {
                return response()->json([
                    'message' => 'Erro ao tentar atualizar dados!',
                ], 400);
            }
        } catch (Exception  $e) {
            Log::warning('Erro ao tentar atualizar frete');
            report($e);

            return response()->json([
                'message' => 'Erro ao tentar atualizar dados!',
            ], 400);
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
            if (isset($id) && !empty($id)) {

                $shippingModel = new Shipping();
                $projectModel = new Project();

                $shipping = $shippingModel->withCount(['sales'])
                    ->find(current(Hashids::decode($id)));

                if ($shipping->sales_count > 0) {
                    return response()->json(['message' => 'Impossível excluir, existem vendas associados a este frete!'], 400);
                }

                if ($shipping) {
                    $project = $projectModel->withCount('shippings')->find(current(Hashids::decode($projectId)));
                    if ($project->shippings_count == 1) {
                        return response()->json(['message' => 'Impossivel excluir, o projeto deve ter no minímo 1 frete cadastrado!'],
                            400);
                    }

                    if (!$this->verifyAllPlansHasActiveShippings($shipping)) {
                        return response()->json(['message' => 'Impossível excluir, existem planos que ficarão sem nenhum frete vinculado!'], 400);
                    }

                    if (Gate::allows('edit', [$project])) {

                        if ($shipping->pre_selected) {
                            $shippingPreSelected = $shippingModel
                                ->where([
                                    ['project_id', $shipping->project_id],
                                    ['id', '!=', $shipping->id],
                                ])->first();

                            if ($shippingPreSelected) {
                                $shipUpdateSelected = $shippingPreSelected->update(['pre_selected' => 1]);
                            }
                        }

                        if ($shipping->delete()) {
                            return response()->json(['message' => 'Frete removido com sucesso!'], 200);
                        } else {
                            return response()->json(['message' => 'Erro ao tentar remover frete!'], 400);
                        }
                    } else {
                        return response()->json(['message' => 'Sem permissão para remover este frete'], 403);
                    }
                }

                return response()->json([
                    'message' => 'Erro ao tentar excluir dados!',
                ], 400);
            }

            return response()->json([
                'message' => 'Erro ao tentar excluir dados!',
            ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar excluir frete (ShippingController - destroy)');
            report($e);

            return response()->json([
                'message' => 'Erro ao tentar excluir dados!',
            ], 500);
        }
    }

    private function verifyAllPlansHasActiveShippings($shipping, array $currentApplyOnPlans = [], $active_flag = false): bool
    {
        $where = [
            ['project_id', $shipping->project_id],
            ['status', true],
        ];

        $otherShippings = $shipping->where($where)->get();

        $plansHasShipping = [];
        foreach ($shipping->project->plans as $plan) {
            if (!isset($plansHasShipping[$plan->id])) {
                $plansHasShipping[$plan->id] = false;
            }

            foreach ($otherShippings as $otherShipping) {
                $applyOnPlans = json_decode($otherShipping->apply_on_plans);
                $notApplyOnPlans = json_decode($otherShipping->not_apply_on_plans);
                $applyOnPlans = array_diff($applyOnPlans, $notApplyOnPlans);

                if ($shipping->id == $otherShipping->id
                    && !empty($currentApplyOnPlans)
                    && $active_flag
                    && ((isset($currentApplyOnPlans[0]) && $currentApplyOnPlans[0] == 'all') || in_array($plan->id, $currentApplyOnPlans))) {
                    $plansHasShipping[$plan->id] = true;
                } else if ($shipping->id != $otherShipping->id
                    && (in_array($plan->id, $applyOnPlans) || (isset($applyOnPlans[0]) && $applyOnPlans[0] == 'all'))) {
                    $plansHasShipping[$plan->id] = true;
                }
            }
        }

        foreach ($plansHasShipping as $hasShipping) {
            if (!$hasShipping) {
                return false;
            }
        }

        return true;
    }

    public function getShippings(Request $request): JsonResponse
    {
        try {
            $data = (object)$request->all();
            $projectId = hashids_decode($data->project_id);

            $shippings = Shipping::select(['id', 'name', 'information', 'type'])
                ->where('status', Shipping::STATUS_ACTIVE)
                ->where('project_id', $projectId)
                ->paginate(10);

            foreach ($shippings as &$shipping){
                $shipping->id = hashids_encode($shipping->id);
            }

            return response()->json($shippings);

        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao listar fretes'], 400);
        }
    }

    private function getDecodedPlanIds(array $encodedIds)
    {
        $decodedIds = [];
        if (in_array('all', $encodedIds)) {
            $decodedIds[] = 'all';
        } else {
            foreach ($encodedIds as $key => $value) {
                $decodedIds[] = current(Hashids::decode($value));
            }
        }
        return $decodedIds;
    }
}
