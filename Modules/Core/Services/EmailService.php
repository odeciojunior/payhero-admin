<?php 

namespace Modules\Core\Services;

use App\Entities\UserProjeto;

class EmailService {

    public static function novaAfiliacao(){

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("noreply@app.cloudfox.net", "Cloudfox");
        $email->setSubject("Nova afiliação");
        $email->addTo("felixlorram@gmail.com", "julio");
        $email->addContent("text/plain", "Nova notificação");
        $email->addContent(
            "text/html", "<strong>Nova notificação do Cloudfox</strong>"
        );
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
        } catch (Exception $e) {
            return false;
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }

        return true;
    }

    public static function novaSolicitacaoAfiliacao(){

        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom("noreply@app.cloudfox.net", "Cloudfox");
        $email->setSubject("testando integração com sendgrid");
        $email->addTo("felixlorram@gmail.com", "julio");
        $email->addContent("text/plain", "Nova notificação");
        $email->addContent(
            "text/html", "<strong>Nova notificação do Cloudfox</strong>"
        );
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }

        return true;
    }

    public static function confirmacaoAfiliacao(){

        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom("noreply@app.cloudfox.net", "Cloudfox");
        $email->setSubject("testando integração com sendgrid");
        $email->addTo("felixlorram@gmail.com", "julio");
        $email->addContent("text/plain", "Nova notificação");
        $email->addContent(
            "text/html", "<strong>Nova notificação do Cloudfox</strong>"
        );
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }

        return true;
    }

    public static function sendInvite($to, $parameter){

        try {
            $emailLayout = view('invites::email.invite', [
                'link' => 'https://app.cloudfox.net/register/' . $parameter
            ]);

            $email = new \SendGrid\Mail\Mail();
            $email->setFrom("noreply@cloudfox.net", "Cloudfox");
            $email->setSubject("Convite para o CloudFox");
            $email->addTo($to, "CloudFox");
            $email->addContent(
                    "text/html", $emailLayout->render()
            );
            $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));

            $response = $sendgrid->send($email);
        } catch (Exception $e) {
            Log::warning('Erro ao enviar email de convite (EmailHelper - sendInvite)');
            report($e);
        }

        return true;
    }


}

