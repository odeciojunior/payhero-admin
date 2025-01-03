<?php

declare(strict_types=1);

namespace Modules\AdooreiCheckout\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\AdooreiCheckout\Actions\DeleteAdooreiCheckoutAction;
use Modules\AdooreiCheckout\Http\Requests\UpdateAdooreiCheckoutRequest;
use Modules\Core\Entities\ApiToken;
use Modules\Core\Entities\Webhook;
use Modules\Core\Exceptions\InvalidUrlException;
use Modules\Core\ValueObjects\Url;
use Modules\Integrations\Actions\CreateTokenAction;
use Modules\Integrations\Exceptions\ApiTokenNotFoundException;
use Modules\Integrations\Exceptions\UnauthorizedApiTokenDeletionException;
use Modules\Integrations\Transformers\ApiTokenResource;
use Modules\Projects\Actions\CreateProjectByCheckoutIntegrationAction;
use Modules\Webhooks\Actions\CreateWebhook\CreateWebhookAction;
use Modules\Webhooks\Actions\CreateWebhook\CreateWebhookInputDTO;
use Modules\Webhooks\Actions\GenerateSignatureWebhookAction;
use Modules\Webhooks\Transformers\WebhooksResource;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;
use Vinkla\Hashids\Facades\Hashids;

class AdooreiCheckoutApiController extends Controller
{
    private string $description = 'Adoorei_Checkout';

    public function __construct(
        private readonly Webhook $webhookModel,
        private readonly ApiToken $apiTokenModel,
    ) {
    }

    public function index(Request $request, CreateProjectByCheckoutIntegrationAction $createProjectAction): JsonResponse
    {
        try {
            activity()->on((new Webhook()))->tap(function (Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todos as integrações Adoorei Checkout');

            $webhook = $this->webhookModel
                ->newQuery()
                ->with('company:id,fantasy_name')
                ->where('company_id', auth()->user()->company_default)
                ->where('user_id', auth()->user()?->getAccountOwnerId())
                ->where('description', $this->description)
                ->get();

            /**
             * @todo remover código após verificar se todos os webhooks adoorei_checkout tem o signature criado.
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
             * @todo remover código após verificar se todos os ApiTokens adoorei_checkout tem um project relacionado.
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
                'message' => __('messages.system.error')
            ], ResponseStatus::HTTP_BAD_REQUEST);
        }
    }

    public function show($id): JsonResponse|ApiTokenResource
    {
        try {
            /**
             * @var ApiToken $apiToken
             */
            $apiToken = $this->webhookModel
                ->newQuery()
                ->find(current(Hashids::decode($id)));

            activity()
                ->on($this->webhookModel)
                ->tap(function (Activity $activity) use ($id) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                })
                ->log(sprintf('Visualizou tela de editar configurações de integração %s', $apiToken->description));

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
                    url: new Url($request->get('webhook')),
                )
            );

            return response()->json([
                'message' => __('messages.integration.created'),
            ]);
        } catch (InvalidUrlException) {
            return response()
                ->json([
                    'message' => __('messages.url.invalid')
                ], ResponseStatus::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => __('messages.system.error'),
            ], ResponseStatus::HTTP_BAD_REQUEST);
        }
    }

    public function edit($id): JsonResponse
    {
        try {
            activity()
                ->on($this->apiTokenModel)
                ->tap(function (Activity $activity) use ($id) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                })
                ->log('Visualizou tela editar configurações da integração Adoorei Checkout');

            $integration = $this->apiTokenModel
                ->newQuery()
                ->find($id);

            if (empty($integration)) {
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

            return response()->json(['Webhook' => $webhook, 'integration' => $integration]);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => __('messages.system.error'),
            ], ResponseStatus::HTTP_BAD_REQUEST);
        }
    }

    public function update($id, UpdateAdooreiCheckoutRequest $request): JsonResponse
    {
        try {
            $requestValidated = $request->validated();

            $webhook = $this->webhookModel
                ->newQuery()
                ->where('user_id', auth()->user()?->getAccountOwnerId())
                ->where('company_id', auth()->user()->company_default)
                ->where('description', $this->description)
                ->first();

            if (empty($webhook)) {
                return response()
                    ->json([
                        'message' => __('messages.integration.not_found')
                    ], ResponseStatus::HTTP_NOT_FOUND);
            }

            $webhook->update([
                'url' => $requestValidated['webhook'],
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

    public function destroy(string $id, DeleteAdooreiCheckoutAction $action): JsonResponse
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
