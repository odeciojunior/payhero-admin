<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Core\Events\UserRegisteredEvent;
use Modules\Core\Services\SendgridService;

/**
 * Class SendEmailRegisteredListener
 * @package Modules\Core\Listeners
 */
class SendEmailRegisteredListener implements ShouldQueue
{
    use Queueable;

    /**
     * @var SendgridService
     */
    private $emailService;

    public function __construct()
    {
        $this->emailService = new SendgridService();
    }

    /**
     * @param UserRegisteredEvent $event
     */
    public function handle(UserRegisteredEvent $event)
    {
        $data = $event->request;

        try {
            $this->emailService->sendEmail(
                "noreply@azcend.com.br",
                $data["domainName"],
                $data["clientEmail"],
                $data["clientName"],
                $data["templateId"],
                $data["bodyEmail"]
            );
        } catch (Exception $e) {
            Log::warning("Erro ao enviar email de cadastro de novos usuários");
            report($e);
        }
    }

    public function tags()
    {
        return ["listener:" . static::class, "SendEmailRegisteredListener"];
    }
}
