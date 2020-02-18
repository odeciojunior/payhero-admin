<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Core\Events\AffiliateRequestEvent;
use Modules\Core\Services\EmailService;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class WithdrawalRequestSendEmailListener
 * @package Modules\Core\Listeners
 */
class AffiliateRequestSendEmailListener implements ShouldQueue
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
     * @param AffiliateRequestEvent $event
     */
    public function handle(AffiliateRequestEvent $event)
    {
        try {
            $sendGridService = new SendgridService();
            $affiliateRequest = $event->affiliateRequest->load('user', 'project', 'project.users');
            $producer         = $affiliateRequest->project->users[0];
            $affiliate        = $affiliateRequest->user;
            $project          = $affiliateRequest->project;
            $idEncoded        = Hashids::encode($project->id);
            $data             = [
                'producer_name'  => $producer->name,
                'affiliate_name' => $affiliate->name,
                'project_name'   => $project->name,
                'date'           => $affiliateRequest->created_at->format('d/m/Y h:i:s'),
                'link'           => env('APP_URL') . '/projects/' . $idEncoded,
            ];
            $producer->load('userNotification');

            if ($producer->userNotification->affiliation) {
                $sendGridService->sendEmail('noreply@cloudfox.net', 'cloudfox', $producer->email, $producer->name, 'd-0386c841a52c466e96840eb5a663b400', $data);
            }
        } catch (Exception $e) {
            Log::warning('erro ao enviar email de solicitação de afiliação para o projeto ' . $project->id);
            report($e);
        }
    }
}
