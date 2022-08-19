<?php

namespace Modules\Webhooks\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Webhook;
use Modules\Webhooks\Transformers\WebhooksCollection;
use Modules\Webhooks\Transformers\WebhooksResource;

class WebhooksApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return WebhooksCollection
     */
    public function index(Request $request)
    {
        try {
            $webhooks = Webhook::where([
                "user_id" => auth()->user()->account_owner_id,
                "company_id" => hashids_decode($request->company_id),
            ])->paginate(5);

            return new WebhooksCollection($webhooks);
        } catch (Exception $e) {
            report($e);
            return response()->json(
                ["message" => "Ocorreu um erro durante a pesquisa"],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return WebhooksResource
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            $data = $this->validate(null, $data, "store", null);

            if (!empty($data["error"])) {
                return response()->json(
                    ["message" => $data["message"]],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $webhook = Webhook::create([
                "user_id" => auth()->user()->account_owner_id,
                "company_id" => $data["company_id"],
                "description" => $data["description"],
                "url" => $data["url"],
            ]);

            return response()->json(
                ["message" => "Webhook cadastrado com sucesso"],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            report($e);
            return response()->json(
                ["message" => "Ocorreu um erro ao salvar registro"],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Show the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        try {
            $webhook = Webhook::find(hashids_decode($id));

            if ($webhook) {
                return new WebhooksResource($webhook);
            }

            return response()->json(
                ["message" => "Registro não encontrado"],
                Response::HTTP_BAD_REQUEST
            );
        } catch (Exception $e) {
            report($e);
            return response()->json(
                ["message" => "Ocorreu um erro ao buscar registro"],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();

            $webhook = Webhook::find(hashids_decode($id));

            $data = $this->validate($webhook, $data, "update", $id);

            if (!empty($data["error"])) {
                return response()->json(
                    ["message" => $data["message"]],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $webhook->update([
                "company_id" => $data["company_id"],
                "description" => $data["description"],
                "url" => $data["url"],
            ]);

            return response()->json(
                ["message" => "Webhook atualizado com sucesso"],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            report($e);
            return response()->json(
                ["message" => "Ocorreu um erro ao atualizar registro"],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $webhook = Webhook::find(hashids_decode($id));

            if ($webhook->user_id !== auth()->user()->account_owner_id) {
                return response()->json(
                    ["message" => "Ocorreu um erro ao excluir registro"],
                    Response::HTTP_BAD_REQUEST
                );
            }

            if (!$webhook->delete()) {
                return response()->json(
                    ["message" => "Ocorreu um erro ao excluir registro"],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return response()->json(
                ["message" => "Webhook excluído com sucesso"],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            report($e);
            return response()->json(
                ["message" => "Ocorreu um erro ao excluir registro"],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Validate the request data.
     *
     * @param Webhook $webhook
     * @param array $data
     * @param string $event
     * @param int $id
     * @return array
     */
    public function validate($webhook = null, $data, $event, $id = null)
    {
        if ($event == "update") {
            if (
                !empty($webhook->user_id) &&
                $webhook->user_id !== auth()->user()->account_owner_id
            ) {
                return [
                    "error" => true,
                    "message" => "Ocorreu um erro ao editar registro",
                ];
            }
        }

        if (empty($data["description"])) {
            return [
                "error" => true,
                "message" => "Digite um nome para seu webhook",
            ];
        }

        if (empty($data["company_id"])) {
            return [
                "error" => true,
                "message" => "Selecione uma empresa",
            ];
        }

        $company = Company::find(hashids_decode($data["company_id"]));

        if (!$company) {
            return [
                "error" => true,
                "message" => "Selecione uma empresa",
            ];
        }

        $data["company_id"] = $company->id;

        if (empty($data["url"])) {
            return [
                "error" => true,
                "message" => "Digite uma URL",
            ];
        }

        if (!filter_var($data["url"], FILTER_VALIDATE_URL)) {
            return [
                "error" => true,
                "message" => "Digite uma URL válida",
            ];
        }

        return $data;
    }
}
