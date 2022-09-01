<?php

namespace Modules\Webhooks\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Webhook;
use Modules\Webhooks\Http\Requests\WebhookIndexRequest;
use Modules\Webhooks\Http\Requests\WebhookStoreRequest;
use Modules\Webhooks\Http\Requests\WebhookUpdateRequest;
use Modules\Webhooks\Transformers\WebhooksCollection;
use Modules\Webhooks\Transformers\WebhooksResource;

class WebhooksApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param WebhookIndexRequest $request
     * @return WebhooksCollection
     */
    public function index(WebhookIndexRequest $request)
    {
        try {
            $webhooks = Webhook::where([
                "user_id" => $request->user_id,
                "company_id" => $request->company_id,
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
     * @param WebhookStoreRequest $request
     * @return Response
     */
    public function store(WebhookStoreRequest $request)
    {
        try {
            Webhook::create([
                "user_id" => $request->user_id,
                "company_id" => $request->company_id,
                "description" => $request->description,
                "url" => $request->url,
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

            if (!$webhook) {
                return response()->json(
                    ["message" => "Registro não encontrado"],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new WebhooksResource($webhook);
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
     * @param WebhookUpdateRequest $request
     * @param int $id
     * @return Response
     */
    public function update(WebhookUpdateRequest $request, $id)
    {
        try {
            $webhook = Webhook::find(hashids_decode($id));

            if ($webhook->user_id !== auth()->user()->account_owner_id) {
                return response()->json(
                    [
                        "message" =>
                            "Você não tem permissão para atualizar este registro",
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $webhook->update([
                "user_id" => $request->user_id,
                "company_id" => $request->company_id,
                "description" => $request->description,
                "url" => $request->url,
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
                    [
                        "message" =>
                            "Você não tem permissão para excluir este registro",
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $webhook->delete();

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
}
