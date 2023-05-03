<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Core\Events\EvaluateAffiliateRequestEvent;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class WithdrawalRequestSendEmailListener
 * @package Modules\Core\Listeners
 */
class EvaluateAffiliateRequestSendEmailListener implements ShouldQueue
{
    use Queueable;

    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param EvaluateAffiliateRequestEvent $event
     * Email de avaliação de pedido de afiliação
     * Para o usuario que solicitou a afiliação
     */
    public function handle(EvaluateAffiliateRequestEvent $event)
    {
        try {
            $sendGridService = new SendgridService();
            $affiliateRequest = $event->affiliateRequest->load("user", "project");
            $affiliateRequestPresenter = $affiliateRequest->present();
            $user = $affiliateRequest->user;
            $project = $affiliateRequest->project;
            $idEncoded = Hashids::encode($project->id);
            $data = [
                "name" => $user->name,
                "project_name" => $project->name,
                "date" => $affiliateRequest->created_at->format("d/m/Y"),
                "link" => env("APP_URL") . "/projects",
            ];

            if ($affiliateRequest->status == $affiliateRequestPresenter->getStatus("approved")) {
                $templateId = "not"; // done
            } else {
                $templateId = "not"; // done
            }

            $user->load("userNotification");

            /**
             * Verifica se o usuario habilitou notificação email
             */
            if ($user->userNotification->affiliation) {
                $sendGridService->sendEmail(
                    "help@nexuspay.com.br",
                    "nexuspay",
                    $user->email,
                    $user->name,
                    $templateId,
                    $data
                );
            }
        } catch (Exception $e) {
            Log::warning("erro ao enviar email de avaliação de afiliado para o projeto " . $project->id);
            report($e);
        }
    }
}
