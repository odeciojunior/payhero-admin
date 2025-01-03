<?php

declare(strict_types=1);

namespace Modules\VegaCheckout\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\ApiToken;
use Modules\Core\Entities\Webhook;
use Modules\Core\ValueObjects\Url;
use Modules\Integrations\Actions\CreateTokenAction;
use Modules\Integrations\Exceptions\ApiTokenNotFoundException;
use Modules\Integrations\Exceptions\UnauthorizedApiTokenDeletionException;
use Modules\Integrations\Transformers\ApiTokenResource;
use Modules\Projects\Actions\CreateProjectByCheckoutIntegrationAction;
use Modules\VegaCheckout\Actions\DeleteVegaCheckoutAction;
use Modules\Webhooks\Actions\CreateWebhook\CreateWebhookAction;
use Modules\Webhooks\Actions\CreateWebhook\CreateWebhookInputDTO;
use Modules\Webhooks\Actions\GenerateSignatureWebhookAction;
use Modules\Webhooks\Transformers\WebhooksResource;
use PharIo\Manifest\InvalidUrlException;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;
use Vinkla\Hashids\Facades\Hashids;

class VegaCheckoutApiController extends Controller
{
    private string $description = 'Vega_Checkout';

    public function __construct(
        private readonly Webhook $webhookModel,
        private readonly ApiToken $apiTokenModel,
    ) {
    }

    public function index(Request $request, CreateProjectByCheckoutIntegrationAction $createProjectAction): JsonResponse
    {
        try {
            activity()->on($this->webhookModel)->tap(function (Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todos as integrações Vega Checkout');

            $webhook = $this->webhookModel
                ->newQuery()
                ->with('company:id,fantasy_name')
                ->where('company_id', auth()->user()->company_default)
                ->where('user_id', auth()->user()?->getAccountOwnerId())
                ->where('description', $this->description)
                ->get();

            /**
             * @todo remover código após verificar se todos os webhooks vega_checkout tem o signature criado.
             *
             * @var Webhook $item
             */
            foreach ($webhook as $item) {
                if (empty($item->signature)) {
                    $signature = GenerateSignatureWebhookAction::handle([
                        'userId' => $item->user_id,
                        'companyId' => $item->company_id,
                        'description' => $this->description,
                        'url' => new Url($item->url),
                    ]);
                    $item->update(['signature' => $signature]);
                }
            }

            $apiToken = $this->apiTokenModel
                ->newQuery()
                ->where('company_id', auth()->user()->company_default)
                ->where('user_id', auth()->user()?->getAccountOwnerId())
                ->where('description', $this->description)
                ->get();

            /**
             * @todo remover código após verificar se todos os ApiTokens vega_checkout tem um project relacionado.
             *
             * @var ApiToken $item
             */
            foreach ($apiToken as $item) {
                if (is_null($item->project_id)) {
                    $project = $createProjectAction->handle([
                        'company_id' => $item->company_id,
                        'name' => $item->name,
                        'platform_enum' => $item->platform_enum,
                    ]);
                    if (!is_null($project)) {
                        $item->update(['project_id' => $project->id]);
                    }
                }
            }

            return response()->json([
                'integrations' => ApiTokenResource::collection($apiToken),
                'Webhooks' => WebhooksResource::collection($webhook),
            ]);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => __('messages.system.error'),
            ], ResponseStatus::HTTP_BAD_REQUEST);
        }
    }

    public function show($id): JsonResponse|ApiTokenResource
    {
        try {
            /**
             * @var ApiToken $apiToken
             */
            $apiToken = $this->apiTokenModel
                ->newQuery()
                ->find(current(Hashids::decode($id)));

            activity()
                ->on($this->apiTokenModel)
                ->tap(function (Activity $activity) use ($id) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                })
                ->log(sprintf('Visualizou tela editar configurações de integração: %s', $apiToken->description));

            return new ApiTokenResource($apiToken);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => __('messages.system.error')
            ], ResponseStatus::HTTP_BAD_REQUEST);
        }
    }

    public function store(Request $request, CreateWebhookAction $createWebhookAction): JsonResponse
    {
        try {
            $apiToken = $this->apiTokenModel
                ->newQuery()
                ->where('description', $this->description)
                ->where('company_id', auth()->user()->company_default)
                ->where('user_id', auth()->user()?->getAccountOwnerId())
                ->first();

            if (empty($apiToken)) {
                return response()->json([
                    'message' => __('messages.system.error'),
                ], ResponseStatus::HTTP_BAD_REQUEST);
            }

            $createWebhookAction->handle(
                new CreateWebhookInputDTO(
                    userId: auth()->user()->getAccountOwnerId(),
                    companyId: auth()->user()->company_default,
                    description: $this->description,
                    url: new Url('https://pay.vegacheckout.com.br/api/postback/azcend'),
                )
            );

            return response()->json([
                'message' => __('messages.integration.created'),
            ]);
        } catch (InvalidUrlException) {
            return response()->json([
                'message' => __('messages.url.invalid')
            ], ResponseStatus::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => __('messages.system.error'),
            ], ResponseStatus::HTTP_BAD_REQUEST);
        }
    }

    public function edit(string $id): JsonResponse
    {
        try {
            activity()
                ->on($this->apiTokenModel)
                ->tap(function (Activity $activity) use ($id) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                })
                ->log('Visualizou tela editar configurações da integração Vega Checkout');

            $apiToken = $this->apiTokenModel
                ->newQuery()
                ->where('id', $id)
                ->first();

            if (empty($apiToken)) {
                return response()->json([
                    'message' => __('messages.integration.not_found'),
                ], ResponseStatus::HTTP_BAD_REQUEST);
            }

            $webhook = $this->webhookModel
                ->newQuery()
                ->where('description', $this->description)
                ->where('user_id', auth()->user()?->getAccountOwnerId())
                ->where('company_id', auth()->user()->company_default)
                ->first();

            return response()->json(['Webhook' => $webhook, 'integration' => $apiToken]);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => __('messages.system.error'),
            ], ResponseStatus::HTTP_BAD_REQUEST);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $data = $request->all();
            $integrationId = current(Hashids::decode($id));

            $webhook = $this->webhookModel
                ->newQuery()
                ->find($integrationId);

            if (empty($data['clientid'])) {
                return response()->json([
                    'message' => 'CLIENT ID é obrigatório!'
                ], ResponseStatus::HTTP_BAD_REQUEST);
            }

            $webhook->update([
                'clientid' => $data['clientid'],
            ]);

            return response()->json([
                'message' => __('messages.integration.updated'),
            ]);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => __('messages.system.error'),
            ], ResponseStatus::HTTP_BAD_REQUEST);
        }
    }

    public function destroy($id, DeleteVegaCheckoutAction $action): JsonResponse
    {
        try {
            $action->handle(current(Hashids::decode($id)));

            return response()
                ->json([
                    'message' => __('messages.integration.deleted'),
                ]);
        } catch (UnauthorizedApiTokenDeletionException) {
            return response()->json([
                'message' => __('messages.system.unauthorized'),
            ], ResponseStatus::HTTP_FORBIDDEN);
        } catch (ApiTokenNotFoundException) {
            return response()->json([
                'message' => __('messages.integration.not_found'),
            ], ResponseStatus::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            report($e);

            return response()
                ->json([
                    'message' => __('messages.system.error'),
                ], ResponseStatus::HTTP_BAD_REQUEST);
        }
    }
}
