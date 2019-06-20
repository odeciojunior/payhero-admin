<?php

namespace Modules\Shipping\Http\Controllers;

use App\Entities\Project;
use App\Entities\Shipping;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Shipping\Http\Requests\ShippingStoreRequest;
use Modules\Shipping\Http\Requests\ShippingUpdateRequest;
use Modules\Shipping\Transformers\ShippingResource;
use phpseclib\System\SSH\Agent\Identity;
use Vinkla\Hashids\Facades\Hashids;

class ShippingController extends Controller
{
    private $shippingModel;
    private $projectModel;

    public function __construct() { }

    private function getShipping()
    {
        if (!$this->shippingModel) {
            $this->shippingModel = app(Shipping::class);
        }

        return $this->shippingModel;
    }

    private function getProject()
    {
        if (!$this->projectModel) {
            $this->projectModel = app(Project::class);
        }

        return $this->projectModel;
    }

    public function index(Request $request)
    {
        try {
            $projectId = $request->input("project");

            if ($projectId) {
                $projectId = Hashids::decode($projectId)[0];

                $project = $this->getProject()->with('shippings')->find($projectId);

                return ShippingResource::collection($project->shippings);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados (ShippingController - index)');
            report($e);
        }
    }

    public function create()
    {
        try {
            return view("shipping::create");
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela criar frete (ShippingController - create)');
            report($e);
        }
    }

    public function store(ShippingStoreRequest $request)
    {
        try {
            $shippingValidated = $request->validated();
            if ($shippingValidated) {
                $shippingValidated['project'] = current(Hashids::decode($shippingValidated['project']));
                if ($shippingValidated['pre_selected']) {
                    $shippings = $this->getShipping()->where([
                                                                 'project'      => $shippingValidated['project'],
                                                                 'pre_selected' => 1,
                                                             ])->first();
                    if ($shippings) {
                        $shippings->update(['pre_selected' => 0]);
                    }
                }
                $shippingCreated = $this->getShipping()->create($shippingValidated);
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

    public function show(Request $request)
    {
        try {

            if ($request->input('freteId')) {
                $shippingId = current(Hashids::decode($request->input('freteId')));

                $shipping = $this->getShipping()->find($shippingId);

                if ($shipping) {
                    return view("shipping::details", ['shipping' => $shipping]);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados (ShippingController - show)');
            report($e);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            if ($request->input('frete')) {
                $shippingId = current(Hashids::decode($request->input('frete')));
                $shipping   = $this->getShipping()->find($shippingId);

                if ($shipping) {

                    return view("shipping::edit", ['shipping' => $shipping]);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar pagina editar frete (ShippingController - edit)');
            report($e);
        }
    }

    public function update(ShippingUpdateRequest $request, $id)
    {
        try {
            $requestValidated = $request->validated();
            if ($requestValidated) {
                $shippingId = current(Hashids::decode($id));
                $shipping   = $this->getShipping()->find($shippingId);
                if ($requestValidated['pre_selected'] && !$shipping->pre_selected) {
                    $shippingPreSelected = $this->getShipping()
                                                ->where(
                                                    [
                                                        'project', $shipping->project,
                                                        'pre_selected' => '1',
                                                    ]
                                                )
                                                ->first();

                    if (isset($shippingPreSelected)) {
                        $shippingPreSelected->update(['pre_selected' => 0]);
                    }
                }

                if ($requestValidated['pre_selected'] && !$shipping->pre_selected) {
                    $s = $this->getShipping()->where(['project' => $shipping->project, 'pre_selected' => 1])->first();
                    if ($s) {
                        $s->update(['pre_selected' => 0]);
                    }
                }

                $shippingUpdated = $shipping->update($requestValidated);

                if ($shippingUpdated) {
                    return response()->json(['message' => 'Dados atualizados com sucesso!'], 200);
                }

                return response()->json(['message' => 'Erro ao tentar atualizar dados!'], 400);
                /*/*   if ($shippingRequest['pre_selected'] && !$shipping->pre_selected) {
                       $shippingPreSelected = $this->getShipping()->where('project', $shipping->project)
                                                   ->where('pre_selected', 1)->first();
                       if ($shippingPreSelected) {
                           $shippingPreSelected->update(['pre_selected' => 0]);

                           $shippingUpdate = $shipping->update($shippingRequest);

                           if ($shippingUpdate) {
                               return response()->json('success');
                           }
                       }
                   } else {
                       $shipping->update($shippingRequest);
                   }

                if ($shippingRequest['pre_selected'] && (!$shippingRequest['pre_selected'] || !!$shippingRequest['status'])) {
                    $shipp = $this->getShipping()->where(['project' => $shipping->project])
                                  ->notWhere('id', $shipping->id)->first();
                    if ($shipp) {
                        $shipp->update(['pre_selected' => 1]);
                    }
                }

                if (!$shipping->pre_selected && $shippingRequest['pre_selected']) {
                    $shipp = $this->getShipping()->where([
                                                             'project'      => $shipping->project,
                                                             'pre_selected' => $shipping->pre_selected,
                                                         ])->first();
                    if ($shipp) {
                        $shipp->update(['pre_selected' => 0]);
                    }
                }

                $shipping->update($shippingRequest);
                */
            }
        } catch (Exception  $e) {
            Log::warning('Erro ao tentar atualizar frete');
            report($e);
        }
        /*   $requestData = $request->all();
           dd($requestData);

           $shipping = Shipping::find($requestData['id']);

           if ($shipping['pre_selected'] && (!$requestData['pre_selected'] || !$requestData['status'])) {
               $s = Shipping::where([
                                        ['project', $shipping['project']],
                                        ['id', '!=', $shipping['id']],
                                    ])->first();
               if ($s) {
                   $s->update([
                                  'pre_selected' => '1',
                              ]);
               }
           }
           if (!$shipping['pre_selected'] && $requestData['pre_selected']) {
               $s = Shipping::where([
                                        ['project', $shipping['project']],
                                        ['pre_selected', '1'],
                                    ])->first();
               if ($s) {
                   $s->update([
                                  'pre_selected' => '0',
                              ]);
               }
           }

           $shipping->update($requestData);

           return response()->json('success');*/
    }

    public function destroy($id)
    {
        try {
            if (isset($id)) {
                $shippingId = Hashids::decode($id)[0];
                $shipping   = $this->getShipping()->find($shippingId);

                if ($shipping) {
                    if ($shipping->pre_selected) {
                        $shippingPreSelected = $this->getShipping()
                                                    ->where([
                                                                ['project', $shipping->project],
                                                                ['id', '!=', $shipping->id],
                                                            ])->first();

                        $shipUpdateSelected = $shippingPreSelected->update(['pre_selected' => 1]);

                        if (!$shipUpdateSelected) {

                            return response()->json(['message' => 'Erro ao tentar remover frete!'], 400);
                        }
                    }

                    if ($shipping->delete()) {

                        return response()->json(['message' => 'Frete removido com sucesso!'], 200);
                    }
                }

                return response()->json(['message' => 'Erro ao tentar remover frete!'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar excluir frete (ShippingController - destroy)');
            report($e);
        }
    }
}

