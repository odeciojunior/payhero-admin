<?php 

namespace Modules\Core\Helpers;

use App\UserProjeto;

class EmailHelper {

    public static function novaAfiliacao(){

        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom("noreply@cloudfox.app", "Cloudfox");
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

    public static function novaSolicitacaoAfiliacao(){

        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom("noreply@cloudfox.app", "Cloudfox");
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
        $email->setFrom("noreply@cloudfox.app", "Cloudfox");
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

}

