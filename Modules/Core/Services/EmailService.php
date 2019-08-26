<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use SendGrid;
use SendGrid\Mail\Mail;

class EmailService
{
    /**
     * @param $to
     * @param $parameter
     * @return string
     * @throws \Throwable
     */
    public function sendInvite($to, $parameter)
    {

        try {
            $emailLayout = view('invites::email.invite', [
                'link' => 'https://app.cloudfox.net/register/' . $parameter,
            ]);

            $email = new Mail();
            $email->setFrom("noreply@cloudfox.net", "Cloudfox");
            $email->setSubject("Convite para o CloudFox");
            $email->addTo($to, "CloudFox");
            $email->addContent(
                "text/html", $emailLayout->render()
            );
            $sendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));

            return $sendgrid->send($email);
        } catch (Exception $e) {
            Log::warning('Erro ao enviar email de convite (EmailHelper - sendInvite)');
            report($e);

            return 'error';
        }
    }
}

