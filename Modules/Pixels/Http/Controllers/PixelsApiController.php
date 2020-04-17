<?php

namespace Modules\Pixels\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Pixel;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Affiliate;
use Modules\Pixels\Http\Requests\PixelStoreRequest;
use Modules\Pixels\Http\Requests\PixelUpdateRequest;
use Modules\Pixels\Transformers\PixelEditResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Pixels\Transformers\PixelsResource;

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
            if (!empty($projectId)) {
                $pixelModel   = new Pixel();
                $projectModel = new Project();

                $project = $projectModel->find(current(Hashids::decode($projectId)));

                $affiliate = Affiliate::where('project_id', $project->id)
                                      ->where('user_id', auth()->user()->account_owner_id)
                                      ->first();

                $affiliateId = $affiliate->id ?? null;

                activity()->on($pixelModel)->tap(function(Activity $activity) {
                    $activity->log_name = 'visualization';
                })->log('Visualizou tela todos os pixels para o projeto ' . $project->name);

                if (Gate::allows('edit', [$project, $affiliateId])) {

                    $pixels = $pixelModel->where('project_id', $project->id)->where('affiliate_id', $affiliateId)
                                         ->orderBy('id', 'DESC');

                    return PixelsResource::collection($pixels->paginate(5));
                } else {
                    return response()->json([
                                                'message' => 'Sem permissão para listar pixels',
                                            ], 403);
                }
            } else {
                return response()->json([
                                            'message' => 'Erro ao listar dados de pixels',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar pixels (PixelsController - index)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao listar dados de pixels',
                                    ], 400);
        }
    }

    /**
     * @param PixelStoreRequest $request
     * @param $projectId
     * @return JsonResponse
     */
    public function store(PixelStoreRequest $request, $projectId)
    {
        try {
            $pixelModel   = new Pixel();
            $projectModel = new Project();

            $validator = $request->validated();

            if (!$validator || !isset($projectId)) {
                return response()->json('Parametros invalidos', 400);
            }

            $validator['project_id'] = current(Hashids::decode($projectId));

            $affiliateId = 0;
            if (!empty($validator['affiliate_id'])) {
                $affiliateId               = current(Hashids::decode($validator['affiliate_id']));
                $validator['affiliate_id'] = $affiliateId;
            } else {
                $validator['affiliate_id'] = null;
            }

            $project = $projectModel->find($validator['project_id']);

            if (Gate::allows('edit', [$project, $affiliateId])) {

                if ($validator['platform'] != 'outbrain' || $validator['platform'] != 'taboola') {
                    $order             = ['UA-', 'AW-'];
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

                $pixel = $pixelModel->create([
                                                 'project_id'      => $validator['project_id'],
                                                 'name'            => $validator['name'],
                                                 'code'            => $validator['code'],
                                                 'platform'        => $validator['platform'],
                                                 'status'          => $validator['status'],
                                                 'checkout'        => $validator['checkout'],
                                                 'purchase_boleto' => $validator['purchase_boleto'],
                                                 'purchase_card'   => $validator['purchase_card'],
                                                 'affiliate_id'    => $validator['affiliate_id'],
                                                 'campaign_id'     => $validator['campaign'] ?? null,
                                                 'apply_on_plans'  => $applyPlanEncoded,
                                             ]);

                if ($pixel) {
                    return response()->json('Pixel Configurado com sucesso!', 200);
                } else {
                    return response()->json('Erro ao criar pixel', 400);
                }
            } else {
                return response()->json(['message' => 'Sem permissão para salvar pixels'], 403);
            }
        } catch (Exception $e) {
            Log::warning('Erro tentar salvar pixel (PixelsController - store)');
            report($e);

            return response()->json('Erro ao criar pixel', 400);
        }
    }

    /**
     * @param PixelUpdateRequest $request
     * @param $projectId
     * @param $id
     * @return JsonResponse
     */
    public function update(PixelUpdateRequest $request, $projectId, $id)
    {
        $validated = $request->validated();
        try {
            if (isset($validated) && isset($id) && isset($projectId)) {
                $pixelModel   = new Pixel();
                $projectModel = new Project();

                $pixel       = $pixelModel->find(current(Hashids::decode($id)));
                $project     = $projectModel->find(current(Hashids::decode($projectId)));
                $affiliateId = (!empty($pixel->affiliate_id)) ? $pixel->affiliate_id : 0;
                if (Gate::allows('edit', [$project, $affiliateId])) {

                    if ($validated['platform'] != 'outbrain' || $validated['platform'] != 'taboola') {
                        $order             = ['UA-', 'AW-', 'AG-'];
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

                    $pixelUpdated = $pixel->update([
                                                       'name'            => $validated['name'],
                                                       'platform'        => $validated['platform'],
                                                       'status'          => $validated['status'],
                                                       'code'            => $validated['code'],
                                                       'apply_on_plans'  => $applyPlanEncoded,
                                                       'checkout'        => $validated['checkout'],
                                                       'purchase_boleto' => $validated['purchase_boleto'],
                                                       'purchase_card'   => $validated['purchase_card'],
                                                   ]);
                    if ($pixelUpdated) {
                        return response()->json('Sucesso', 200);
                    } else {
                        return response()->json(['message' => 'Erro ao tentar atualizar dados!'], 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para atualizar pixels'], 403);
                }
            }

            return response()->json(['message' => 'Pixel nao encontrado'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar fazer update dos dados do pixel (PixelsController - update)');
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
            if (isset($id) && isset($projectId)) {
                $pixelModel   = new Pixel();
                $projectModel = new Project();

                $pixel       = $pixelModel->find(current(Hashids::decode($id)));
                $projectId   = current(Hashids::decode($projectId));
                $project     = $projectModel->find($projectId);
                $affiliateId = (!empty($pixel->affiliate_id)) ? $pixel->affiliate_id : 0;

                if (Gate::allows('edit', [$project, $affiliateId])) {
                    $pixelDeleted = $pixel->delete();
                    if ($pixelDeleted) {
                        return response()->json('sucesso', 200);
                    } else {
                        return response()->json(['message' => 'Erro ao tentar excluir pixel'], 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para remover pixels'], 403);
                }
            }

            return response()->json(['message' => 'Pixel nao encontrado'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar excluir pixel (PixelsController - destroy)');
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
            if (isset($id) && isset($projectId)) {
                $pixelModel   = new Pixel();
                $projectModel = new Project();

                $pixel       = $pixelModel->find(current(Hashids::decode($id)));
                $project     = $projectModel->find(current(Hashids::decode($projectId)));
                $affiliateId = (!empty($pixel->affiliate_id)) ? $pixel->affiliate_id : 0;

                activity()->on($pixelModel)->tap(function(Activity $activity) use ($id) {
                    $activity->log_name   = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                })->log('Visualizou tela detalhes do pixel: ' . $pixel->name);

                if (Gate::allows('edit', [$project, $affiliateId])) {

                    if (!empty($pixel)) {

                        $pixel->makeHidden(['id', 'project_id', 'campaing_id']);

                        return response()->json($pixel);
                    } else {
                        return response()->json('Erro ao buscar pixel', 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para visualizar pixels'], 403);
                }
            }

            return response()->json('Pixel nao encontrado', 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar detalhes do pixel (PixelController - show)');
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
            if (isset($projectId) && isset($id)) {
                $pixelModel   = new Pixel();
                $projectModel = new Project();

                $pixel       = $pixelModel->find(current(Hashids::decode($id)));
                $project     = $projectModel->find(current(Hashids::decode($projectId)));
                $affiliateId = (!empty($pixel->affiliate_id)) ? $pixel->affiliate_id : 0;

                activity()->on($pixelModel)->tap(function(Activity $activity) use ($id) {
                    $activity->log_name   = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                })->log('Visualizou tela editar pixel: ' . $pixel->name);

                if (Gate::allows('edit', [$project, $affiliateId])) {

                    if ($pixel) {
                        $applyPlanArray = [];
                        $planModel      = new Plan();

                        if (!empty($pixel->apply_on_plans)) {
                            $applyPlanDecoded = json_decode($pixel->apply_on_plans);
                            if (in_array('all', $applyPlanDecoded)) {
                                $applyPlanArray[] = [
                                    'id'   => 'all',
                                    'name' => 'Todos os Planos',
                                ];
                            } else {
                                foreach ($applyPlanDecoded as $key => $value) {
                                    $plan = $planModel->find($value);
                                    if (!empty($plan)) {
                                        $applyPlanArray[] = [
                                            'id'   => Hashids::encode($plan->id),
                                            'name' => $plan->name,
                                        ];
                                    }
                                }
                            }
                        }

                        $pixel->apply_on_plans = $applyPlanArray;

                        $pixel->makeHidden(['id', 'project_id', 'campaing_id']);

                        return new PixelEditResource($pixel);
                    } else {
                        return response()->json('Erro ao buscar pixel', 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para editar pixels'], 403);
                }
            }

            return response()->json('Erro ao buscar pixel', 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar pixel (PixelsController - edit)');
            report($e);

            return response()->json('Erro ao buscar pixel', 400);
        }
    }
}
