<?php

namespace Modules\Integrations\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\ApiToken;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
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
    public function index()
    {
        try {
            $apiTokenModel = new ApiToken();
            $tokens = $apiTokenModel
                ->newQuery()
                ->where("user_id", auth()->user()->account_owner_id)
                ->latest()
                ->paginate();

            return new ApiTokenCollection($tokens);
        } catch (Exception $ex) {
            Log::debug($ex);

            return redirect()
                ->back()
                ->with("error", "Ocorreu um erro durante a pesquisa.");
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return ApiTokenResource
     */
    public function store(Request $request)
    {
        try {
            $description = $request->get("description");
            if (empty($description)) {
                return response()->json(
                    ["message" => "O campo Nome da Integração é obrigatório!"],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $tokenTypeEnum = $request->get("token_type_enum");
            if (empty($tokenTypeEnum)) {
                return response()->json(
                    ["message" => "O Tipo de Integração é obrigatório!"],
                    Response::HTTP_BAD_REQUEST
                );
            }

            if ($tokenTypeEnum == ApiToken::INTEGRATION_TYPE_CHECKOUT_API) {
                $company = Company::find(current(hashids()->decode($request->get("company_id"))));
                if (!$company) {
                    return response()->json(
                        ["message" => "O campo Empresa é obrigatório para a integração Checkout API"],
                        Response::HTTP_BAD_REQUEST
                    );
                }

                $postback = $request->get("postback");
                if (empty($postback)) {
                    return response()->json(
                        ["message" => "O campo Postback é obrigatório!"],
                        Response::HTTP_BAD_REQUEST
                    );
                }
            }

            /** @var User $user */
            $apiTokenModel = new ApiToken();
            $apiTokenPresenter = $apiTokenModel->present();

            $scopes = $apiTokenPresenter->getTokenScope($tokenTypeEnum);
            if (empty($scopes)) {
                return response()->json(["message" => "Tipo do token inválido!"], Response::HTTP_BAD_REQUEST);
            }

            $tokenIntegration = ApiToken::generateTokenIntegration($description, $scopes);
            /** @var ApiToken $token */
            $token = $apiTokenModel->create([
                "user_id" => auth()->user()->account_owner_id,
                "company_id" => $tokenTypeEnum == 4 ? $company->id : null,
                "token_id" => $tokenIntegration->token->getKey(),
                "access_token" => $tokenIntegration->accessToken,
                "scopes" => json_encode($scopes, true),
                "integration_type_enum" => $tokenTypeEnum,
                "description" => $description,
                "postback" => $postback ?? null,
            ]);

            return new ApiTokenResource($token);
        } catch (Exception $ex) {
            Log::warning("Ocorreu um erro ao salvar integração.");
            report($ex);

            return response()->json(["message" => "Ocorreu um erro ao salvar."], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
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
                200
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
     * @param string $encodedId
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
                        Response::HTTP_BAD_REQUEST
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
     * Remove the specified resource from storage.
     * @param string $encodedId
     * @return Response
     */
    public function destroy($encodedId)
    {
        try {
            $apiTokenModel = new ApiToken();
            /** @var ApiToken $apiToken */
            $apiToken = $apiTokenModel->newQuery()->find(current(Hashids::decode($encodedId)));
            if ($apiToken->user_id !== auth()->user()->account_owner_id) {
                return response()->json(["message" => "Ocorreu um erro ao excluir."], Response::HTTP_BAD_REQUEST);
            }
            if (!$apiToken->delete()) {
                return response()->json(["message" => "Ocorreu um erro ao excluir."], Response::HTTP_BAD_REQUEST);
            }

            return response()->json(["message" => "Registro excluído com sucesso"], Response::HTTP_OK);
        } catch (Exception $ex) {
            Log::debug($ex);

            return redirect()
                ->back()
                ->with("error", "Ocorreu um erro ao excluir.");
        }
    }

    /**
     * Update the specified resource in storage.
     * @param string $encodedId
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
                    Response::HTTP_BAD_REQUEST
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
