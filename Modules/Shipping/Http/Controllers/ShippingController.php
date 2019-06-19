<?php

namespace Modules\Shipping\Http\Controllers;

use App\Entities\Project;
use App\Entities\Shipping;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Shipping\Transformers\ShippingResource;
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

    }

    public function store(Request $request)
    {

        $requestData = $request->all();

        $requestData['project'] = Hashids::decode($requestData['projeto'])[0];

        if ($requestData['pre_selected']) {
            $shippings = Shipping::where('project', $requestData['project'])->get()->toArray();
            foreach ($shippings as $shipping) {
                if ($shipping['pre_selected']) {
                    Shipping::find($shipping['id'])->update([
                                                                'pre_selected' => '0',
                                                            ]);
                }
            }
        }

        Shipping::create($requestData);

        return response()->json('success');
    }

    public function show(Request $request)
    {
        try {

            if ($request->input('freteId')) {
                $shippingId = Hashids::decode($request->input('freteId'))[0];

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

    public function edit(Request $request)
    {
        try {
            if ($request->input('frete')) {
                $shippingId = Hashids::decode($request->input('frete'))[0];
                $shipping   = $this->getShipping()->find($shippingId);

                if ($shipping) {

                    return view("shipping::create", ['shipping' => $shipping]);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar pagina editar frete (ShippingController - edit)');
            report($e);
        }
    }

    public function update(Request $request)
    {

        $requestData = $request->all();

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

        return response()->json('success');
    }

    public function delete(Request $request)
    {

        $requestData = $request->all();

        $shipping = Shipping::find($requestData['id']);

        if ($shipping['pre_selected']) {
            $s = Shipping::where([
                                     ['project', $shipping['project']],
                                     ['id', '!=', $shipping['id']],
                                 ])->first();
            if ($s) {
                $s->update([
                               'pre_selected', '1',
                           ]);
            }
        }

        $shipping->delete();

        return response()->json('success');
    }
}

