<?php

namespace Modules\Melhorenvio\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\MelhorenvioIntegration;
use Modules\Core\Entities\Shipping;
use Modules\Core\Services\MelhorenvioService;
use Modules\Melhorenvio\Http\Requests\MelhorenvioStoreRequest;
use Modules\Melhorenvio\Transformers\MelhorenvioIntegrationResource;

class MelhorenvioApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userId = auth()->user()->account_owner_id;

            $integrations = MelhorenvioIntegration::where("user_id", $userId);

            if ($request->input("completed") == 1) {
                $integrations->where("completed", true);
            }

            return MelhorenvioIntegrationResource::collection($integrations->get());
        } catch (\Exception $e) {
            return response()->json(["message" => "Erro ao listar integrações."], 400);
        }
    }

    public function store(MelhorenvioStoreRequest $request)
    {
        try {
            $data = $request->all();
            $data["user_id"] = auth()->user()->account_owner_id;
            $data["access_token"] = null;
            $data["refresh_token"] = null;
            $data["expiration"] = null;
            $data["completed"] = false;

            $integration = MelhorenvioIntegration::updateOrCreate($data);

            $url = (new MelhorenvioService($integration))->getAuthorizationUrl();

            return response()->json(["url" => $url]);
        } catch (\Exception $e) {
            return response()->json(
                ["message" => "Erro ao realizar integração! Verifique os dados e tente novamente."],
                400
            );
        }
    }

    public function destroy($id)
    {
        try {
            $id = hashids_decode($id);

            $shippings = Shipping::withCount("sales")
                ->where("melhorenvio_integration_id", $id)
                ->get();

            foreach ($shippings as $shipping) {
                if ($shipping->sales_count) {
                    $shipping->update([
                        "status" => 0,
                    ]);
                } else {
                    $shipping->delete();
                }
            }

            MelhorenvioIntegration::find($id)->delete();

            return response()->json(["message" => "Integração excluída com sucesso!"]);
        } catch (\Exception $e) {
            return response()->json(["message" => "Erro ao excluir integração!"], 400);
        }
    }

    public function continueIntegration($id)
    {
        try {
            $id = hashids_decode($id);

            $integration = MelhorenvioIntegration::find($id);

            $url = (new MelhorenvioService($integration))->getAuthorizationUrl();

            return response()->json(["url" => $url]);
        } catch (\Exception $e) {
            return response()->json(
                ["message" => "Erro ao continuar integração! Verifique os dados e tente novamente."],
                400
            );
        }
    }

    public function finishIntegration(Request $request)
    {
        try {
            $requestData = $request->all();

            $integrationId = hashids_decode($requestData["state"]);

            $integration = MelhorenvioIntegration::find($integrationId);

            $melhorenvio = new MelhorenvioService($integration);

            $melhorenvio->requestAccessToken($requestData["code"]);
        } catch (\Exception $e) {
            report($e);
        }
        return redirect(route("melhorenvio.index"));
    }
}
