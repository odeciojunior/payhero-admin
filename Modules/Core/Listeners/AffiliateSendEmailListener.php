<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
     * @param AffiliateRequestEvent $event
     */
    public function handle(AffiliateEvent $event)
    {
        $sendGridService = new SendgridService();
        $affiliate       = $event->affiliate->load('user', 'project', 'project.users');
        $producer        = $affiliate->project->users[0];
        $affiliate       = $affiliate->user;
        $project         = $affiliate->project;

        $idEncoded       = Hashids::encode($project->id);
        $data            = [
            'producer_name'  => $producer->name,
            'affiliate_name' => $affiliate->name,
            'project_name'   => $project->name,
            'date'           => $affiliate->created_at->format('d/m/Y h:i:s'),
            'link'           => env('APP_URL') . '/projects/' . $idEncoded,
        ];
        $sendGridService->sendEmail('noreply@cloudfox.net', 'cloudfox', $producer->email, $producer->name, 'd-d8c9706d9d064f38a0a203174d1d43a8', $data);
    }
}
