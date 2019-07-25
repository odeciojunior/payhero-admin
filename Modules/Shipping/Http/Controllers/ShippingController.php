<?php

namespace Modules\Shipping\Http\Controllers;

use App\Entities\Project;
use App\Entities\Shipping;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Shipping\Http\Requests\ShippingStoreRequest;
use Modules\Shipping\Http\Requests\ShippingUpdateConfigResource;
use Modules\Shipping\Http\Requests\ShippingUpdateRequest;
use Modules\Shipping\Transformers\ShippingResource;
use Vinkla\Hashids\Facades\Hashids;

class ShippingController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $projectId = $request->input("project");

            $projectModel = new Project();

            if ($projectId) {
                $projectId = Hashids::decode($projectId)[0];

                $project = $projectModel->with('shippings')->find($projectId);

                return ShippingResource::collection($project->shippings);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados (ShippingController - index)');
            report($e);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        try {
            return view("shipping::create");
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela criar frete (ShippingController - create)');
            report($e);
        }
    }

    /**
     * @param ShippingStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ShippingStoreRequest $request)
    {
        try {
            $shippingValidated = $request->validated();

            $shippingModel = new Shipping();

            if ($shippingValidated) {
                $shippingValidated['project'] = current(Hashids::decode($shippingValidated['project']));
                if ($shippingValidated['value'] == null || preg_replace("/[^0-9]/", "", $shippingValidated['value']) == 0) {
                    $shippingValidated['value'] = '0,00';
                }

                if ($shippingValidated['pre_selected']) {

                    $shippings = $shippingModel->where([
                                                           'project'      => $shippingValidated['project'],
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
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar cadastrar frete (ShippingController - store)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        try {

            $shippingModel = new Shipping();

            if ($request->input('freteId')) {
                $shippingId = current(Hashids::decode($request->input('freteId')));

                $shipping = $shippingModel->find($shippingId);

                if ($shipping) {
                    return view("shipping::details", ['shipping' => $shipping]);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados (ShippingController - show)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        try {

            $shippingModel = new Shipping();

            if ($request->input('frete')) {
                $shippingId = current(Hashids::decode($request->input('frete')));
                $shipping   = $shippingModel->find($shippingId);

                if ($shipping) {
                    return view("shipping::edit", ['shipping' => $shipping]);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar pagina editar frete (ShippingController - edit)');
            report($e);
        }
    }

    /**
     * @param ShippingUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ShippingUpdateRequest $request, $id)
    {
        try {
            $requestValidated = $request->validated();

            $shippingModel = new Shipping();

            if (isset($requestValidated) && isset($id)) {

                $shippingId = current(Hashids::decode($id));
                $shipping   = $shippingModel->find($shippingId);
                if ($requestValidated['value'] == null || preg_replace("/[^0-9]/", "", $requestValidated['value']) == 0) {
                    $requestValidated['value'] = '0,00';
                }
                if ($requestValidated['pre_selected'] && !$shipping->pre_selected) {
                    $s = $shippingModel->where([
                                                   ['project', $shipping->project],
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
                                                    ['project', $shipping->project],
                                                    ['pre_selected', 1],
                                                ])->get();

                    if (count($sp) == 0) {
                        $shipp = $shippingModel->where('project', $shipping->project)->first();
                        $shipp->update(['pre_selected' => 1]);
                    }
                }

                $mensagem = "Frete atualizado com sucesso!";
                if ($shippingUpdated) {
                    $shippings = $shippingModel->where([['project', $shipping->project], ['status', 1]])
                                               ->get();

                    if (count($shippings) == 0) {
                        $sh = $shippingModel->where(['project' => $shipping->project])->first();

                        $sh->update(['status' => 1]);
                        $mensagem = 'É obrigatório deixar um frete ativado';
                    }

                    return response()->json(['message' => $mensagem], 200);
                }

                return response()->json(['message' => 'Erro ao tentar atualizar dados!'], 400);
            }

            return response()->json(['message' => 'Erro ao tentar atualizar dados!'], 400);
        } catch (Exception  $e) {
            Log::warning('Erro ao tentar atualizar frete');
            report($e);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $shippingModel = new Shipping();

            if (isset($id) && !empty($id)) {
                $shipping = $shippingModel->withCount(['sales'])->find(current(Hashids::decode($id)));

                if ($shipping->sales_count > 0) {
                    return response()->json(['message' => 'Impossivel excluir, existem vendas associados a este frete!'], 400);
                }

                if ($shipping) {
                    if ($shipping->pre_selected) {
                        $shippingPreSelected = $shippingModel
                            ->where([
                                        ['project', $shipping->project],
                                        ['id', '!=', $shipping->id],
                                    ])->first();

                        if ($shippingPreSelected) {
                            $shipUpdateSelected = $shippingPreSelected->update(['pre_selected' => 1]);
                        }
                    }

                    if ($shipping->delete()) {

                        return response()->json(['message' => 'Frete removido com sucesso!'], 200);
                    }
                }
            }

            return response()->json(['message' => 'Erro ao tentar remover frete!'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar excluir frete (ShippingController - destroy)');
            report($e);
        }
    }

    /**
     * @param ShippingUpdateConfigResource $request
     * @param $projectId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateConfig(ShippingUpdateConfigResource $request, $projectId)
    {
        try {
            $projectModel = new Project();

            $requestValidated = $request->validated();

            if ($projectId && $requestValidated) {

                $project = $projectModel->find(current(Hashids::decode($projectId)));

                if ($project) {

                    if (!$requestValidated['shipment']) {
                        $requestValidated['carrier']              = null;
                        $requestValidated['shipment_responsible'] = null;
                    }

                    $projectUpdate = $project->update($requestValidated);

                    if ($projectUpdate) {
                        return response()->json(['message' => 'Configurações frete atualizados com sucesso'], 200);
                    }
                }
            }

            return response()->json(['message' => 'Erro ao tentar atualizar dados'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar atualiza configurações de frete ShippingController - updateConfig ');
            report($e);
        }
    }
}

