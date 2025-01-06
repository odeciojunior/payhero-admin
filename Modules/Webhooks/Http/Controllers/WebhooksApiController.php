<?php

declare(strict_types=1);

namespace Modules\Webhooks\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Webhook;
use Modules\Core\ValueObjects\Url;
use Modules\Webhooks\Actions\CreateWebhook\CreateWebhookAction;
use Modules\Webhooks\Actions\CreateWebhook\CreateWebhookInputDTO;
use Modules\Webhooks\Actions\GenerateSignatureWebhookAction;
use Modules\Webhooks\Http\Requests\WebhookIndexRequest;
use Modules\Webhooks\Http\Requests\WebhookStoreRequest;
use Modules\Webhooks\Http\Requests\WebhookUpdateRequest;
use Modules\Webhooks\Transformers\WebhooksCollection;
use Modules\Webhooks\Transformers\WebhooksResource;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class WebhooksApiController extends Controller
{
    public function __construct(
        private readonly Webhook $webhookModel,
    ) {
    }

    public function index(WebhookIndexRequest $request): JsonResponse|WebhooksCollection
    {
        try {
            $webhooks = $this->webhookModel
                ->newQuery()
                ->where('user_id', $request->user_id)
                ->where('company_id', $request->company_id)
                ->whereNotIn('description', ['Vega_Checkout', 'Adoorei_Checkout'])
                ->paginate(5);

            return new WebhooksCollection($webhooks);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => __('messages.system.error')
            ], ResponseStatus::HTTP_BAD_REQUEST);
        }
    }

    public function store(WebhookStoreRequest $request, CreateWebhookAction $createWebhookAction): JsonResponse
    {
        try {
            $createWebhookAction->handle(
                new CreateWebhookInputDTO(
                    userId: $request->user_id,
                    companyId: $request->company_id,
                    description: $request->description,
                    url: new Url($request->url),
                )
            );

            return response()->json([
                'message' => __('messages.webhook.created')
            ]);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => __('messages.unexpected_error')
            ], ResponseStatus::HTTP_BAD_REQUEST);
        }
    }

    public function show($id): JsonResponse|WebhooksResource
    {
        try {
            $webhook = $this->webhookModel
                ->newQuery()
                ->with('company:id,fantasy_name')
                ->find(hashids_decode($id));

            if (!$webhook) {
                return response()->json([
                    'message' => __('messages.webhook.not_found')
                ], ResponseStatus::HTTP_BAD_REQUEST);
            }

            /**
             * @todo remover código após verificar se todos os webhooks criados tem o signature criado.
             *
             * @var Webhook $webhook
             */
            if (empty($webhook->signature)) {
                $signature = GenerateSignatureWebhookAction::handle([
                    'user_id' => $webhook->user_id,
                    'company_id' => $webhook->company_id,
                    'description' => $webhook->description,
                    'url' => new Url($webhook->url),
                ]);
                $webhook->update(['signature' => $signature]);
            }

            return new WebhooksResource($webhook);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => __('messages.unexpected_error')
            ], ResponseStatus::HTTP_BAD_REQUEST);
        }
    }

    public function update(WebhookUpdateRequest $request, $id): JsonResponse
    {
        try {
            $webhook = $this->webhookModel
                ->newQuery()
                ->find(hashids_decode($id));

            if ($webhook->user_id !== auth()->user()->account_owner_id) {
                return response()->json([
                    'message' => __('messages.system.unauthorized')
                ], ResponseStatus::HTTP_BAD_REQUEST);
            }

            $webhook->update([
                "user_id" => $request->user_id,
                "company_id" => $request->company_id,
                "description" => $request->description,
                "url" => $request->url,
            ]);

            return response()->json([
                'message' => __('messages.webhook.updated')
            ], ResponseStatus::HTTP_OK);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => __('messages.system.error')
            ], ResponseStatus::HTTP_BAD_REQUEST);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $webhook = $this->webhookModel
                ->newQuery()
                ->find(hashids_decode($id));

            if ($webhook->user_id !== auth()->user()->account_owner_id) {
                return response()->json([
                    'message' => __('messages.system.unauthorized')
                ], ResponseStatus::HTTP_BAD_REQUEST);
            }

            $webhook->delete();

            return response()->json([
                'message' => __('messages.webhook.deleted')
            ], ResponseStatus::HTTP_OK);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => __('messages.system.error')
            ], ResponseStatus::HTTP_BAD_REQUEST);
        }
    }
}
