<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\ProjectNotification;
use Modules\Core\Entities\Tracking;
use Modules\Products\Transformers\ProductsSaleResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ProjectNotificationService
 * @package Modules\Core\Services
 */
class ProjectNotificationService
{
    public const EMAIL_TYPE = 1;
    public const SMS_TYPE = 2;

    public const BOLETO_GENERATED = 1;
    public const BOLETO_COMPENSATED = 2;
    public const CARD_PAYMENT = 3;
    public const ABANDONED_CART = 4;
    public const BOLETO_EXPIRED = 5;
    public const TRACKING_CODE = 6;
    public const PIX_GENERATED = 7;
    public const PIX_COMPENSATED = 8;
    public const PIX_EXPIRED = 9;

    /**
     * @param $projectId
     * @return string
     */
    public function createProjectNotificationDefault($projectId)
    {
        try {
            ProjectNotification::insert([
                [
                    "type_enum" => self::SMS_TYPE,
                    "event_enum" => self::BOLETO_GENERATED,
                    "time" => "Imediato",
                    "message" =>
                        "Olá {primeiro_nome}, não esqueça de pagar seu boleto para enviarmos seu pedido! {url_boleto}",
                    "notification_enum" => 1,
                    "project_id" => $projectId,
                ],
                [
                    "type_enum" => self::SMS_TYPE,
                    "event_enum" => self::BOLETO_EXPIRED,
                    "time" => "10:00 horas",
                    "message" =>
                        "Olá {primeiro_nome}, seu boleto vence hoje, não deixe de efetuar o pagamento e garantir seu pedido! {url_boleto}",
                    "notification_enum" => 2,
                    "project_id" => $projectId,
                ],
                [
                    "type_enum" => self::SMS_TYPE,
                    "event_enum" => self::ABANDONED_CART,
                    "time" => "1 hora depois",
                    "message" =>
                        "Olá {primeiro_nome}, somos da loja {projeto_nome}, vimos que voce não finalizou seu pedido, aproveite o último dia da promoção! {link_carrinho_abandonado}",
                    "notification_enum" => 3,
                    "project_id" => $projectId,
                ],
                [
                    "type_enum" => self::SMS_TYPE,
                    "event_enum" => self::ABANDONED_CART,
                    "time" => "12:00 horas próximo dia",
                    "message" =>
                        "Olá {primeiro_nome}, somos da loja {projeto_nome}, vimos que voce não finalizou seu pedido, aproveite o último dia da promoção! {link_carrinho_abandonado}",
                    "notification_enum" => 4,
                    "project_id" => $projectId,
                ],
                [
                    "type_enum" => self::EMAIL_TYPE,
                    "event_enum" => self::BOLETO_GENERATED,
                    "time" => "Imediato",
                    "message" => json_encode([
                        "subject" => "Parabéns - Pegue aqui o seu boleto - Pedido {codigo_venda}",
                        "title" => "Aqui está seu boleto",
                        "content" =>
                            "Olá {primeiro_nome}, \r\n\r\nComo você optou por Boleto Bancário, estamos enviando por aqui para você não se esquecer. O boleto deve ser pago até a data de vencimento para enviarmos seu(s) pedido(s).",
                    ]),
                    "notification_enum" => 5,
                    "project_id" => $projectId,
                ],
                // pagina 2
                [
                    "type_enum" => self::EMAIL_TYPE,
                    "event_enum" => self::BOLETO_GENERATED,
                    "time" => "10:00 horas próximo dia",
                    "message" => json_encode([
                        "subject" => "Já separamos seu pedido",
                        "title" => "Já separamos seu pedido. Agora só falta você fazer o pagamento do boleto! :)",
                        "content" =>
                            "Olá {primeiro_nome}, estamos enviando esse e-mail só pra avisar que já empacotamos a sua encomenda e estamos prontos para enviar para você. Assim que o boleto for pago e recebermos a confirmação, sua encomenda será enviada!",
                    ]),
                    "notification_enum" => 6,
                    "project_id" => $projectId,
                ],
                [
                    "type_enum" => self::EMAIL_TYPE,
                    "event_enum" => self::BOLETO_GENERATED,
                    "time" => "11:00 horas 2 dias após",
                    "message" => json_encode([
                        "subject" => "Vamos ter que liberar sua mercadoria",
                        "title" => "Vamos ter que devolver sua mercadoria para o estoque!",
                        "content" =>
                            "Olá {primeiro_nome}, por falta de pagamento, vamos ter que liberar sua mercadoria para o estoque novamente. Isso significa que se você não efetuar o pagamento, cancelaremos seu pedido.",
                    ]),
                    "notification_enum" => 7,
                    "project_id" => $projectId,
                ],
                [
                    "type_enum" => self::EMAIL_TYPE,
                    "event_enum" => self::BOLETO_EXPIRED,
                    "time" => "11:30 horas",
                    "message" => json_encode([
                        "subject" => "Hoje vence o seu boleto",
                        "title" => "Seu boleto vence hoje! Não esqueça de pagar seu boleto para finalizar seu pedido.",
                        "content" =>
                            "Olá {primeiro_nome}, seu boleto vencerá hoje, ainda dá tempo de pagar! Não se esqueça, só enviaremos o seu pedido (que já está separado) se você efetuar o pagamento.",
                    ]),
                    "notification_enum" => 8,
                    "project_id" => $projectId,
                ],
                [
                    "type_enum" => self::EMAIL_TYPE,
                    "event_enum" => self::ABANDONED_CART,
                    "time" => "1 hora depois",
                    "message" => json_encode([
                        "subject" => "Você pode perder dinheiro se ignorar esse email",
                        "title" => "A promoção termina hoje",
                        "content" =>
                            "Olá {primeiro_nome}, \r\n\r\nNossos produtos estão com preços especiais e o estoque é bem limitado. Recomendamos que você finalize a compra ainda hoje para garantir a promoção e economizar dinheiro.",
                    ]),
                    "notification_enum" => 9,
                    "project_id" => $projectId,
                ],
                [
                    "type_enum" => self::EMAIL_TYPE,
                    "event_enum" => self::ABANDONED_CART,
                    "time" => "12:00 horas próximo dia",
                    "message" => json_encode([
                        "subject" => "Posso liberar o seu pedido para outra pessoa?",
                        "title" => "O seu pedido está te esperando",
                        "content" =>
                            "Olá {primeiro_nome}, vimos que você não aproveitou a promoção de ontem. O seu pedido ainda está separado aguardando a finalização da compra, mas não podemos segurar por muito tempo.",
                    ]),
                    "notification_enum" => 10,
                    "project_id" => $projectId,
                ],
                // pagina 3
                [
                    "type_enum" => self::SMS_TYPE,
                    "event_enum" => self::CARD_PAYMENT,
                    "time" => "Imediato",
                    "message" =>
                        "Olá {primeiro_nome}, sua compra foi aprovada na loja {projeto_nome}. Qualquer dúvida entre em contato com o suporte através do link: {sac_link} . Em breve enviaremos o código de rastreio.",
                    "notification_enum" => 11,
                    "project_id" => $projectId,
                ],
                [
                    "type_enum" => self::EMAIL_TYPE,
                    "event_enum" => self::CARD_PAYMENT,
                    "time" => "Imediato",
                    "message" => json_encode([
                        "subject" => "Sua compra foi aprovada - Pedido {codigo_venda}",
                        "title" => "Sua compra foi aprovada!",
                        "content" =>
                            "Olá {primeiro_nome}, seu pedido {codigo_venda} foi confirmado. Aqui estão as informações e os detalhes da sua compra.",
                    ]),
                    "notification_enum" => 12,
                    "project_id" => $projectId,
                ],
                [
                    "type_enum" => self::EMAIL_TYPE,
                    "event_enum" => self::BOLETO_COMPENSATED,
                    "time" => "Imediato",
                    "message" => json_encode([
                        "subject" => "Boleto pago - Pedido {codigo_venda}",
                        "title" => "Boleto pago",
                        "content" =>
                            "Olá {primeiro_nome}, seu pedido {codigo_venda} foi aprovado. Obrigado pela sua compra, nos próximos dias enviaremos o código de rastreio para você acompanhar seu pedido.",
                    ]),
                    "notification_enum" => 13,
                    "project_id" => $projectId,
                ],
                [
                    "type_enum" => self::EMAIL_TYPE,
                    "event_enum" => self::TRACKING_CODE,
                    "time" => "Imediato",
                    "message" => json_encode([
                        "subject" => "Seu código de rastreio chegou",
                        "title" => "Código de Rastreio!",
                        "content" =>
                            "Olá, {primeiro_nome} seu pedido foi enviado! \r\n\r\n Código é {codigo_rastreio}, utilize o link abaixo para rastrear seu pedido: \r\n{link_rastreamento} \r\n\r\n<STRONG>Em até 3 dias úteis este código estará disponível para rastreio no site dos correios.</STRONG>",
                    ]),
                    "notification_enum" => 14,
                    "project_id" => $projectId,
                ],
                [
                    "type_enum" => self::SMS_TYPE,
                    "event_enum" => self::TRACKING_CODE,
                    "time" => "Imediato",
                    "message" =>
                        "Olá {primeiro_nome}, seu pedido foi enviado! \r\nAcesse seu e-mail para consultar link de rastreio. \r\nCódigo de rastreio: {codigo_rastreio}",
                    "notification_enum" => 15,
                    "project_id" => $projectId,
                ],
                [
                    "type_enum" => self::EMAIL_TYPE,
                    "event_enum" => self::PIX_GENERATED,
                    "time" => "Imediato",
                    "message" => json_encode([
                        "subject" => "Seu código pix foi gerado",
                        "title" => "Efetue o pagamento, a promoção termina hoje!",
                        "content" => "Olá {primeiro_nome}, não esqueça de pagar seu PIX para enviarmos seu pedido!",
                    ]),
                    "notification_enum" => 16,
                    "project_id" => $projectId,
                ],
                [
                    "type_enum" => self::EMAIL_TYPE,
                    "event_enum" => self::PIX_COMPENSATED,
                    "time" => "Imediato",
                    "message" => json_encode([
                        "subject" => "PIX pago - Pedido {codigo_venda} ",
                        "title" => "PIX pago com sucesso!",
                        "content" =>
                            "Olá {primeiro_nome}, seu pedido {codigo_venda} foi aprovado. Obrigado pela sua compra, nos próximos dias enviaremos o código de rastreio para você acompanhar seu pedido.",
                    ]),
                    "notification_enum" => 17,
                    "project_id" => $projectId,
                ],
                [
                    "type_enum" => self::EMAIL_TYPE,
                    "event_enum" => self::PIX_EXPIRED,
                    "time" => "Imediato",
                    "message" => json_encode([
                        "subject" => "Finalize sua compra no PIX",
                        "title" => "Seu PIX expirou!",
                        "content" =>
                            "Olá {primeiro_nome}, seu pagemento por PIX expirou, mas não se preocupe, você pode regerar o PIX de pagamento clicando no botão abaixo: ",
                    ]),
                    "notification_enum" => 18,
                    "project_id" => $projectId,
                ],
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param string $message
     * @param null $sale
     * @param null $project
     * @param null $notificationType
     * @param null $linkCheckout
     * @param null $log
     * @param null $trackingCode
     * @return mixed|string
     */
    public function formatNotificationData(
        string $message,
        $sale = null,
        $project = null,
        $notificationType = null,
        $linkCheckout = null,
        $log = null,
        $trackingCode = null
    ) {
        try {
            if (!empty($message)) {
                if (strpos($message, "{primeiro_nome}") !== false) {
                    if (!empty($sale)) {
                        $clientNameExploded = explode(" ", $sale->customer->name);
                        $message = str_replace("{primeiro_nome}", $clientNameExploded[0], $message);
                    } elseif (!empty($log)) {
                        $clientNameExploded = explode(" ", $log->name);
                        $message = str_replace("{primeiro_nome}", $clientNameExploded[0], $message);
                    }
                }

                $domainName = "";
                if (!empty($sale)) {
                    $domain = Domain::select("name")
                        ->where("project_id", $sale->project_id)
                        ->where("status", 3)
                        ->first();
                    $domainName = $domain->name ?? "azcend.com.br";
                }

                if (strpos($message, "{url_boleto}") !== false && !empty($sale)) {
                    $boletoLink =
                        "https://checkout.{$domainName}/order/" .
                        Hashids::connection("sale_id")->encode($sale->id) .
                        "/download-boleto";
                    if ($notificationType == "sms") {
                        $linkShortenerService = new LinkShortenerService();
                        $link = $linkShortenerService->shorten($boletoLink);
                        $message = str_replace("{url_boleto}", $link, $message);
                    } else {
                        $message = str_replace("{url_boleto}", $boletoLink, $message);
                    }
                }

                if (strpos($message, "{codigo_venda}") !== false && !empty($sale)) {
                    $saleCode = Hashids::connection("sale_id")->encode($sale->id);
                    $message = str_replace("{codigo_venda}", "#" . $saleCode, $message);
                }

                if (strpos($message, "{codigo_rastreio}") !== false && !empty($trackingCode)) {
                    $message = str_replace("{codigo_rastreio}", $trackingCode, $message);
                }

                if (strpos($message, "{projeto_nome}") !== false && !empty($project)) {
                    $message = str_replace("{projeto_nome}", $project->name, $message);
                }

                if (strpos($message, "{link_rastreamento}") !== false && !empty($sale)) {
                    $domainModel = new Domain();
                    $domain = $domainModel
                        ->where("project_id", $sale->project_id)
                        ->where("status", 3)
                        ->first();
                    if (!empty($domain)) {
                        if ($notificationType == "sms") {
                            $linkShortenerService = new LinkShortenerService();
                            $linkBase = "https://global.cainiao.com/newDetail.htm?mailNoList=";
                            $link = $linkShortenerService->shorten($linkBase . $trackingCode);
                            $message = str_replace("{link_rastreamento}", $link, $message);
                        } else {
                            $link = "https://global.cainiao.com/newDetail.htm?mailNoList=" . $trackingCode;
                            $message = str_replace("{link_rastreamento}", $link, $message);
                        }
                    }
                }

                if (strpos($message, "{link_carrinho_abandonado}") !== false && !empty($linkCheckout)) {
                    if ($notificationType == "sms") {
                        $linkShortenerService = new LinkShortenerService();
                        $link = $linkShortenerService->shorten($linkCheckout);
                        $message = str_replace("{link_carrinho_abandonado}", $link, $message);
                    } else {
                        $message = str_replace("{link_carrinho_abandonado}", $linkCheckout, $message);
                    }
                }

                return $message;
            }
            return "";
        } catch (Exception $ex) {
            Log::warning(
                "Erro ao formatar dados da notificação de email - ProjectNotificationService - formatNotificationData"
            );
            report($ex);
        }
    }

    public function updateSmsCreditCardPaidNotification($projectId)
    {
        $projectNotification = ProjectNotification::where("project_id", $projectId)
            ->where("notification_enum", 11)
            ->first();
        //'message' => 'Olá {primeiro_nome}, sua compra foi aprovada na loja {projeto_nome}. Qualquer dúvida entre em contato por email {projeto_email} ou telefone {projeto_telefone}. Em breve enviaremos o código de rastreio.',
        $projectNotification->update([
            "message" =>
                "Olá {primeiro_nome}, sua compra foi aprovada na loja {projeto_nome}. Qualquer dúvida entre em contato com o suporte através do link: {sac_link} . Em breve enviaremos o código de rastreio.",
        ]);
    }
}
