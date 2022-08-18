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
    public function index()
    {
        try {
            $webhooks = Webhook::where(
                "user_id",
                auth()->user()->account_owner_id
            )->paginate();

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

            if (empty($data["description"])) {
                return response()->json(
                    ["message" => "Digite um nome para seu webhook"],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $company = Company::find(hashids_decode($data["company_id"]));

            if (!$company) {
                return response()->json(
                    ["message" => "Selecione uma empresa"],
                    Response::HTTP_BAD_REQUEST
                );
            }

            if (empty($data["url"])) {
                return response()->json(
                    ["message" => "Digite uma URL válida"],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $webhook = Webhook::create([
                "user_id" => auth()->user()->account_owner_id,
                "company_id" => $company->id,
                "description" => $data["description"],
                "url" => $data["url"],
            ]);

            return new WebhooksResource($webhook);
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

            if ($webhook->user_id !== auth()->user()->account_owner_id) {
                return response()->json(
                    ["message" => "Ocorreu um erro ao editar registro"],
                    Response::HTTP_BAD_REQUEST
                );
            }

            if (empty($data["description"])) {
                return response()->json(
                    ["message" => "Digite um nome para seu webhook"],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $company = Company::find(hashids_decode($data["company_id"]));

            if (!$company) {
                return response()->json(
                    ["message" => "Selecione uma empresa"],
                    Response::HTTP_BAD_REQUEST
                );
            }

            if (empty($data["url"])) {
                return response()->json(
                    ["message" => "Digite uma URL válida"],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $webhook->update([
                "company_id" => $company->id,
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
}
