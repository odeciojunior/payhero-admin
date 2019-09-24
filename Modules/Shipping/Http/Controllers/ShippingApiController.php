<?php


namespace Modules\Shipping\Http\Controllers;


use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Modules\Shipping\Http\Requests\ShippingStoreRequest;
use Modules\Shipping\Http\Requests\ShippingUpdateConfigResource;
use Modules\Shipping\Http\Requests\ShippingUpdateRequest;
use Modules\Shipping\Transformers\ShippingResource;
use Vinkla\Hashids\Facades\Hashids;

class ShippingApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $shippingModel = new Shipping();
            $projectModel = new Project();

            if ($request->has('project') && !empty($request->input("project"))) {
                $projectId = current(Hashids::decode($request->input("project")));

                $project = $projectModel->find($projectId);

                if (Gate::allows('edit', [$project])) {
                    $shippings = $shippingModel->where('project_id', $projectId);

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
     * @return JsonResponse
     */
    public function store(ShippingStoreRequest $request)
    {
        try {
            $shippingModel = new Shipping();
            $projectModel = new Project();

            $shippingValidated = $request->validated();

            if ($shippingValidated) {
                $shippingValidated['project_id'] = current(Hashids::decode($shippingValidated['project_id']));

                $project = $projectModel->find($shippingValidated['project_id']);

                if (Gate::allows('edit', [$project])) {
                    if ($shippingValidated['value'] == null || preg_replace("/[^0-9]/", "", $shippingValidated['value']) == 0) {
                        $shippingValidated['value'] = '0,00';
                    }
                    if (empty($shippingValidated['status'])) {
                        $shippingValidated['status'] = 0;
                    }
                    if (empty($shippingValidated['pre_selected'])) {
                        $shippingValidated['pre_selected'] = 0;
                    }
                    if ($shippingValidated['pre_selected']) {
                        $shippings = $shippingModel->where([
                            'project_id' => $shippingValidated['project_id'],
                            'pre_selected' => 1,
                        ])->first();
                        if ($shippings) {
                            $shippings->update(['pre_selected' => 0]);
                        }
                    }
                    $shippingCreated = $shippingModel->create($shippingValidated);
                    if ($shippingCreated) {
                        return response()->json(['message' => 'Frete cadastrado com sucesso!'], 200);
                    }

                    return response()->json(['message' => 'Erro ao tentar cadastrar frete!'], 400);
                } else {
                    return response()->json(['message' => 'Sem permissão para cadastrar um frete'], 403);
                }
            }
            return response()->json([
                'message' => 'Erro ao tentar cadastrar frete',
            ], 404);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar cadastrar frete (ShippingController - store)');
            report($e);
            return response()->json([
                'message' => 'Erro ao tentar cadastrar frete',
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        try {
            $shippingModel = new Shipping();
            if ($request->input('freteId')) {
                $shippingId = current(Hashids::decode($request->input('freteId')));
                $shipping = $shippingModel->with(['project'])->find($shippingId);

                $project = $shipping->getRelation('project');

                if (Gate::allows('edit', [$project])) {

                    if ($shipping) {
                        $shipping->makeHidden(['id', 'project_id', 'campaing_id'])->unsetRelation('project');
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
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function edit(Request $request, $id)
    {
        try {

            $shippingModel = new Shipping();

            if ($request->input('frete')) {
                $shippingId = current(Hashids::decode($request->input('frete')));
                $shipping = $shippingModel->with(['project'])->find($shippingId);

                $project = $shipping->getRelation('project');

                if (Gate::allows('edit', [$project])) {
                    if ($shipping) {
                        $shipping->makeHidden(['id', 'project_id', 'campaing_id'])->unsetRelation('project');
                        return response()->json($shipping, 200);
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

    /**
     * @param ShippingUpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ShippingUpdateRequest $request, $id)
    {
        try {
            $shippingModel = new Shipping();

            $requestValidated = $request->validated();

            if (empty($requestValidated['pre_selected'])) {
                $requestValidated['pre_selected'] = 0;
            }
            if (empty($requestValidated['status'])) {
                $requestValidated['status'] = 0;
            }

            if (isset($requestValidated) && isset($id)) {

                $shippingId = current(Hashids::decode($id));
                $shipping = $shippingModel->with(['project'])->find($shippingId);

                $project = $shipping->getRelation('project');

                if (Gate::allows('edit', [$project])) {

                    if ($requestValidated['value'] == null || preg_replace("/[^0-9]/", "", $requestValidated['value']) == 0) {
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

                    $shippingUpdated = $shipping->update($requestValidated);

                    if (!$requestValidated['pre_selected'] && !$shipping->pre_selected) {
                        $sp = $shippingModel->where([
                            ['project_id', $shipping->project_id],
                            ['pre_selected', 1],
                        ])->get();

                        if (count($sp) == 0) {
                            $shipp = $shippingModel->where('project_id', $shipping->project_id)->first();
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
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $shippingModel = new Shipping();

            if (isset($id) && !empty($id)) {
                $shipping = $shippingModel->with(['project'])
                    ->withCount(['sales'])
                    ->find(current(Hashids::decode($id)));

                if ($shipping->sales_count > 0) {
                    return response()->json(['message' => 'Impossivel excluir, existem vendas associados a este frete!'], 400);
                }

                if ($shipping) {

                    $project = $shipping->getRelation('project');
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
            ], 400);
        }
    }
}
