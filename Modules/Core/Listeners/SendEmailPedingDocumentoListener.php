<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Core\Events\SendEmailPendingDocumentEvent;
use Modules\Core\Services\EmailService;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class SendEmailPedingDocumentoListener
 * @package Modules\Core\Listeners
 */
class SendEmailPedingDocumentoListener implements ShouldQueue
{
    use Queueable;
    /**
     * @var SendgridService
     */
    private $emailService;

    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        $this->emailService = new EmailService();
    }

    /**
     * @param SendEmailPendingDocumentEvent $event
     */
    public function handle(SendEmailPendingDocumentEvent $event)
    {
        $data      = $event->request;
        
        $sufixHomolog = '-test';
        if (env('APP_ENV') == 'production') {
            $sufixHomolog = '';
        }

        $url = "https://accounts{$sufixHomolog}.cloudfox.net/personal-info";

        if (! empty($data['companyId'])) {
            $url = "https://accounts{$sufixHomolog}.cloudfox.net/companies/company-detail/".Hashids::encode($data['companyId']);
        }

        try {
            $emailReturn = $this->emailService->sendEmail(
                                'noreply@cloudfox.net',
                                $data['domainName'],
                                $data['clientEmail'],
                                $data['clientName'],
                                'd-025af5d220104323976d672b0a49b266',
                                [
                                    'name'=>$data['clientName'],
                                    'account_url'=>$url,
                                ]
                            );
                            
            if ($emailReturn) {
                if (! empty($data['companyId'])) {
                    Company::find($data['companyId'])->update([
                        'date_last_document_notification'=>now()
                    ]);
                }
                if(! empty($data['userId'])){
                    User::find($data['userId'])->update([
                        'date_last_document_notification'=>now()
                    ]);
                }
            }

        } catch (Exception $e) {            
            report($e);
        }
    }

    public function tags()
    {
        return ['listener:' . static::class, 'SendEmailListener'];
    }
}
