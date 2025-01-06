<?php

declare(strict_types=1);

namespace Modules\Integrations\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\ApiToken;
use Modules\Integrations\Actions\CreateTokenAction;
use Modules\Integrations\Actions\DeleteApiTokenAction;
use Modules\Integrations\Exceptions\ApiTokenNotFoundException;
use Modules\Integrations\Exceptions\InvalidTokenTypeException;
use Modules\Integrations\Exceptions\TokenAlreadyExistsException;
use Modules\Integrations\Exceptions\UnauthorizedApiTokenDeletionException;
use Modules\Integrations\Http\Requests\StoreApiTokenRequest;
use Modules\Integrations\Transformers\ApiTokenCollection;
use Modules\Integrations\Transformers\ApiTokenResource;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class IntegrationsApiController
 * @package Modules\Integrations\Http\Controllers
 */
class IntegrationsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return AnonymousResourceCollection|ApiTokenCollection
     */
    public function index(Request $request)
    {
        try {
            $apiTokenModel = new ApiToken();
            $tokens = $apiTokenModel
                ->newQuery()
                ->where(
                    "user_id",
                    auth()
                        ->user()
                        ->getAccountOwnerId(),
                )
                ->where("company_id", auth()->user()->company_default)
                ->latest()
                ->paginate(10);
            return new ApiTokenCollection($tokens);
        } catch (Exception $ex) {
            Log::debug($ex);

            return redirect()
                ->back()
                ->with("error", "Ocorreu um erro durante a pesquisa.");
        }
    }

    public function store(StoreApiTokenRequest $request, CreateTokenAction $action): JsonResponse|ApiTokenResource
    {
        try {
            $data = $request->validated();
            $data["company_id"] = current(hashids()->decode($request->get("company_id")));
            $newToken = $action->handle($data);
            return new ApiTokenResource($newToken);
        } catch (TokenAlreadyExistsException) {
            return response()->json([
                'message' => 'Já existe um token com a descrição informada!'
            ], Response::HTTP_OK);
        } catch (InvalidTokenTypeException) {
            return response()->json([
                'message' => 'Tipo do token inválido!'
            ], Response::HTTP_BAD_REQUEST);
        } catch (Exception $ex) {
            report($ex);

            return response()->json(['message' => 'Ocorreu um erro ao salvar.'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param $encodedId
     * @return JsonResponse|RedirectResponse
     */
    public function show($encodedId)
    {
        try {
            $apiTokenModel = new ApiToken();
            $apiToken = $apiTokenModel->newQuery()->find(current(Hashids::decode($encodedId)));

            return response()->json(
                [
                    "description" => $apiToken->description,
                    "token_type_enum" => $apiToken->integration_type_enum,
                    "postback" => $apiToken->postback,
                ],
                200,
            );
        } catch (Exception $ex) {
            Log::debug($ex);

            return redirect()
                ->back()
                ->with("error", "Ocorreu um erro ao excluir.");
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param  string  $encodedId
     * @return Response
     */
    public function update(Request $request, $encodedId)
    {
        try {
            $apiTokenModel = new ApiToken();
            $apiToken = $apiTokenModel->newQuery()->find(current(Hashids::decode($encodedId)));

            if ($apiToken->user_id !== auth()->user()->account_owner_id) {
                return response()->json(["message" => "Ocorreu um erro ao editar."], Response::HTTP_BAD_REQUEST);
            }

            $description = $request->get("description");
            if (empty($description)) {
                return response()->json(["message" => "O campo Descrição é obrigatório!"], Response::HTTP_BAD_REQUEST);
            }

            $tokenTypeEnum = $request->get("token_type_enum");
            if ($tokenTypeEnum == ApiToken::INTEGRATION_TYPE_CHECKOUT_API) {
                $postback = $request->get("postback");
                if (empty($postback)) {
                    return response()->json(
                        ["message" => "O campo Postback é obrigatório!"],
                        Response::HTTP_BAD_REQUEST,
                    );
                }
            }

            $apiToken->update([
                "description" => $description,
                "postback" => $postback ?? null,
            ]);

            return response()->json(["message" => "Integração editada com sucesso"], Response::HTTP_OK);
        } catch (Exception $ex) {
            Log::debug($ex);

            return redirect()
                ->back()
                ->with("error", "Ocorreu um erro ao excluir.");
        }
    }

    /**
     * @param  string  $encodedId
     * @param  DeleteApiTokenAction  $action
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(string $encodedId, DeleteApiTokenAction $action): JsonResponse|RedirectResponse
    {
        try {
            $apiTokenDecrypt = current(Hashids::decode($encodedId));
            $action->handle((int)$apiTokenDecrypt);

            return response()->json([
                'message' => 'Registro excluído com sucesso'
            ], Response::HTTP_OK);
        } catch (ApiTokenNotFoundException|UnauthorizedApiTokenDeletionException) {
            return response()->json([
                'message' => 'Ocorreu um erro ao excluir.',
            ], Response::HTTP_BAD_REQUEST);
        } catch (Exception $ex) {
            report($ex);

            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao excluir.');
        }
    }

    /**
     * Update the specified resource in storage.
     * @param  string  $encodedId
     * @return Response
     */
    public function refreshToken($encodedId)
    {
        try {
            $apiTokenModel = new ApiToken();
            /** @var ApiToken $apiToken */
            $apiToken = $apiTokenModel->newQuery()->find(current(Hashids::decode($encodedId)));
            $scopes = $apiToken->present()->getTokenScope();
            $tokenIntegration = ApiToken::generateTokenIntegration($apiToken->description, $scopes);
            $result = $apiToken->update([
                "token_id" => $tokenIntegration->token->getKey(),
                "access_token" => $tokenIntegration->accessToken,
                "scopes" => json_encode($scopes, true),
            ]);
            if (!$result) {
                return response()->json(
                    ["message" => "Ocorreu um erro ao atualizar registro."],
                    Response::HTTP_BAD_REQUEST,
                );
            }

            return response()->json(["message" => "Token atualizado com sucesso"], Response::HTTP_OK);
        } catch (Exception $ex) {
            Log::debug($ex);

            return redirect()
                ->back()
                ->with("error", "Ocorreu um erro ao atualizar registro.");
        }
    }
}
