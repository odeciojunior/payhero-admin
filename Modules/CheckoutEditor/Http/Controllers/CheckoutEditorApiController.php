<?php

namespace Modules\CheckoutEditor\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\CheckoutEditor\Transformers\CheckoutConfigResource;
use Modules\Core\Entities\CheckoutConfig;
use Modules\Products\Http\Requests\UpdateCheckoutConfigRequest;

class CheckoutEditorApiController extends Controller
{
    public function show($projectId)
    {
        try {
            $projectId = hashids_decode($projectId);

            $config = CheckoutConfig::where('project_id', $projectId)->first();

            return new CheckoutConfigResource($config);

        } catch (\Exception $e) {
            return foxutils()->isProduction()
                ? response()->json(['message' => 'Erro ao obter as configurações do checkout'])
                : response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, UpdateCheckoutConfigRequest $request)
    {
        try {
            $id = hashids_decode($id);
            $data = $request->all();

            $config = CheckoutConfig::find($id);

            $config::update($data);

            return new CheckoutConfigResource($config);
        } catch (\Exception $e) {
            return foxutils()->isProduction()
                ? response()->json(['message' => 'Erro ao atualizar as configurações do checkout'])
                : response()->json(['message' => $e->getMessage()]);
        }
    }
}
