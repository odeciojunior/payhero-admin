<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Core\Events\AffiliateEvent;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class WithdrawalRequestSendEmailListener
 * @package Modules\Core\Listeners
 */
class AffiliateSendEmailListener implements ShouldQueue
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
     * @param AffiliateEvent $event
     */
    public function handle(AffiliateEvent $event)
    {
        try {
            $sendGridService = new SendgridService();
            $affiliateEvent  = $event->affiliate->load('user', 'project', 'project.users');
            $producer        = $affiliateEvent->project->users[0];
            $affiliate       = $affiliateEvent->user;
            $project         = $affiliateEvent->project;
            $idEncoded       = Hashids::encode($project->id);
            $data            = [
                'producer_name'  => $producer->name,
                'affiliate_name' => $affiliate->name,
                'project_name'   => $project->name,
                'date'           => $affiliateEvent->created_at->format('d/m/Y h:i:s'),
                'link'           => env('APP_URL') . '/projects/' . $idEncoded,
            ];

            $producer->load('userNotification');

            /**
             * Verifica se o usuario habilitou notificação email de nova afiliação para produtor
             */
            if ($producer->userNotification->new_affiliation) {
                $sendGridService->sendEmail('noreply@cloudfox.net', 'cloudfox', $producer->email, $producer->name, 'd-d8c9706d9d064f38a0a203174d1d43a8', $data);
            }
        } catch (Exception $e) {
            Log::warning('erro ao enviar email de nova afiliação ao para o projeto ' . $project->id);
            report($e);
        }
    }
}
