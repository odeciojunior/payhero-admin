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
            $sendGridService           = new SendgridService();
            $affiliateRequest          = $event->affiliateRequest->load('user', 'project');
            $affiliateRequestPresenter = $affiliateRequest->present();
            $user                      = $affiliateRequest->user;
            $project                   = $affiliateRequest->project;
            $idEncoded                 = Hashids::encode($project->id);
            $data                      = [
                'name'         => $user->name,
                'project_name' => $project->name,
                'date'         => $affiliateRequest->created_at->format('d/m/Y'),
                'link'         => env('APP_URL') . '/projects/' . $idEncoded,
            ];

            if ($affiliateRequest->status == $affiliateRequestPresenter->getStatus('approved')) {
                $templateId = 'd-f777e4ed8416473b8b2673923139db60';
            } else {
                $templateId = 'd-14c40a9bd9704f9e8999a5d8fdc9cf7c';
            }

            $user->load('userNotification');

            /**
             * Verifica se o usuario habilitou notificação email
             */
            if ($user->userNotification->affiliation) {
                $sendGridService->sendEmail('noreply@cloudfox.net', 'cloudfox', $user->email, $user->name, $templateId, $data);
            }
        } catch (Exception $e) {
            Log::warning('erro ao enviar email de avaliação de afiliado para o projeto ' . $project->id);
            report($e);
        }
    }
}
