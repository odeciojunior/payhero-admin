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
    const EMAIL_TYPE = 1;
    const SMS_TYPE   = 2;

    /**
     * @param $projectId
     * @return string
     */
    public function createProjectNotificationDefault($projectId)
    {
        try {
            ProjectNotification::insert([
                                            [
                                                'type_enum'         => self::SMS_TYPE, // sms
                                                'event_enum'        => 1,
                                                'time'              => 'Imediato',
                                                'message'           => 'Olá {primeiro_nome}, não esqueça de pagar seu boleto para enviarmos seu pedido! {url_boleto}',
                                                'notification_enum' => 1,
                                                'project_id'        => $projectId,
                                            ],
                                            [
                                                'type_enum'         => self::SMS_TYPE,
                                                'event_enum'        => 5,
                                                'time'              => '10:00 horas',
                                                'message'           => 'Olá {primeiro_nome}, seu boleto vence hoje, não deixe de efetuar o pagamento e garantir seu pedido! {url_boleto}',
                                                'notification_enum' => 2,
                                                'project_id'        => $projectId,
                                            ],
                                            [
                                                'type_enum'         => self::SMS_TYPE,
                                                'event_enum'        => 4,
                                                'time'              => '1 hora depois',
                                                'message'           => 'Olá {primeiro_nome}, somos da loja {projeto_nome}, vimos que voce não finalizou seu pedido, aproveite o último dia da promoção! {link_carrinho_abandonado}',
                                                'notification_enum' => 3,
                                                'project_id'        => $projectId,
                                            ],
                                            [
                                                'type_enum'         => self::SMS_TYPE,
                                                'event_enum'        => 4,
                                                'time'              => '10:00 horas próximo dia',
                                                'message'           => 'Olá {primeiro_nome}, somos da loja {projeto_nome}, vimos que voce não finalizou seu pedido, aproveite o último dia da promoção! {link_carrinho_abandonado}',
                                                'notification_enum' => 4,
                                                'project_id'        => $projectId,
                                            ],
                                            [
                                                'type_enum'         => self::EMAIL_TYPE,
                                                'event_enum'        => 1,
                                                'time'              => 'Imediato',
                                                'message'           => json_encode([
                                                                                       'subject' => 'Parabéns - Pegue aqui o seu boleto - Pedido {codigo_venda}',
                                                                                       'title'   => 'Aqui está seu boleto',
                                                                                       'content' => 'Olá {primeiro_nome}, Como você optou por Boleto Bancário, estamos enviando por aqui para você não se esquecer. O boleto deve ser pago até a data de vencimento para enviarmos seu(s) pedido(s).',
                                                                                   ]),
                                                'notification_enum' => 5,
                                                'project_id'        => $projectId,
                                            ],
                                            // pagina 2
                                            [
                                                'type_enum'         => self::EMAIL_TYPE,
                                                'event_enum'        => 1,
                                                'time'              => '10:00 horas próximo dia',
                                                'message'           => json_encode([
                                                                                       'subject' => 'Já separamos seu pedido',
                                                                                       'title'   => 'Já separamos seu pedido. Agora só falta você fazer o pagamento do boleto! :)',
                                                                                       'content' => 'Olá {primeiro_nome}, estamos enviando esse e-mail só pra avisar que já empacotamos a sua encomenda e estamos prontos para enviar para você. Assim que o boleto for pago e recebermos a confirmação, sua encomenda será enviada!',
                                                                                   ]),
                                                'notification_enum' => 6,
                                                'project_id'        => $projectId,
                                            ],
                                            [
                                                'type_enum'         => self::EMAIL_TYPE,
                                                'event_enum'        => 1,
                                                'time'              => '10:00 horas 2 dias após',
                                                'message'           => json_encode([
                                                                                       'subject' => 'Vamos ter que liberar sua mercadoria',
                                                                                       'title'   => 'Vamos ter que devolver sua mercadoria para o estoque!',
                                                                                       'content' => 'Olá {primeiro_nome}, por falta de pagamento, vamos ter que liberar sua mercadoria para o estoque novamente. Isso significa que se você não efetuar o pagamento, cancelaremos seu pedido.',
                                                                                   ]),
                                                'notification_enum' => 7,
                                                'project_id'        => $projectId,
                                            ],
                                            [
                                                'type_enum'         => self::EMAIL_TYPE,
                                                'event_enum'        => 5,
                                                'time'              => '10:00 horas',
                                                'message'           => json_encode([
                                                                                       'subject' => 'Hoje vence o seu boleto',
                                                                                       'title'   => 'Seu boleto vence hoje! Não esqueça de pagar seu boleto para finalizar seu pedido.',
                                                                                       'content' => 'Olá {primeiro_nome}, seu boleto vencerá hoje, ainda dá tempo de pagar! Não se esqueça, só enviaremos o seu pedido (que já está separado) se você efetuar o pagamento.',
                                                                                   ]),
                                                'notification_enum' => 8,
                                                'project_id'        => $projectId,
                                            ],
                                            [
                                                'type_enum'         => self::EMAIL_TYPE,
                                                'event_enum'        => 4,
                                                'time'              => '1 hora depois',
                                                'message'           => json_encode([
                                                                                       'subject' => 'Você pode perder dinheiro se ignorar esse email',
                                                                                       'title'   => 'A promoção termina hoje',
                                                                                       'content' => 'Olá {primeiro_nome}, Nossos produtos estão com preços especiais e o estoque é bem limitado. Recomendamos que você finalize a compra ainda hoje para garantir a promoção e economizar dinheiro.',
                                                                                   ]),
                                                'notification_enum' => 9,
                                                'project_id'        => $projectId,
                                            ],
                                            [
                                                'type_enum'         => self::EMAIL_TYPE,
                                                'event_enum'        => 4,
                                                'time'              => '10:00 horas próximo dia',
                                                'message'           => json_encode([
                                                                                       'subject' => 'Posso liberar o seu pedido para outra pessoa?',
                                                                                       'title'   => 'O seu pedido está te esperando',
                                                                                       'content' => 'Olá {primeiro_nome}, vimos que você não aproveitou a promoção de ontem. O seu pedido ainda está separado aguardando a finalização da compra, mas não podemos segurar por muito tempo.',
                                                                                   ]),
                                                'notification_enum' => 10,
                                                'project_id'        => $projectId,
                                            ],
                                            // pagina 3
                                            [
                                                'type_enum'         => self::SMS_TYPE,
                                                'event_enum'        => 3,
                                                'time'              => 'Imediato',
                                                'message'           => 'Olá {primeiro_nome}, seu pedido foi confirmado! Em breve lhe enviaremos o código de rastreio',
                                                'notification_enum' => 11,
                                                'project_id'        => $projectId,
                                            ],
                                            [
                                                'type_enum'         => self::EMAIL_TYPE,
                                                'event_enum'        => 3,
                                                'time'              => 'Imediato',
                                                'message'           => json_encode([
                                                                                       'subject' => 'Sua compra foi aprovada - Pedido {codigo_venda}',
                                                                                       'title'   => 'Sua compra foi aprovada!',
                                                                                       'content' => 'Olá {primeiro_nome}, seu pedido {codigo_venda} foi confirmado. Aqui estão as informações e os detalhes da sua compra.',
                                                                                   ]),
                                                'notification_enum' => 12,
                                                'project_id'        => $projectId,
                                            ],
                                            [
                                                'type_enum'         => self::EMAIL_TYPE,
                                                'event_enum'        => 2,
                                                'time'              => 'Imediato',
                                                'message'           => json_encode([
                                                                                       'subject' => 'Boleto pago - Pedido {codigo_venda}',
                                                                                       'title'   => 'Boleto pago',
                                                                                       'content' => 'Olá {primeiro_nome}, seu pedido {codigo_venda} foi aprovado. Obrigado pela sua compra, nos próximos dias enviaremos o código de rastreio para você acompanhar seu pedido.',
                                                                                   ]),
                                                'notification_enum' => 13,
                                                'project_id'        => $projectId,
                                            ],
                                            [
                                                'type_enum'         => self::EMAIL_TYPE,
                                                'event_enum'        => 6,
                                                'time'              => 'Imediato',
                                                'message'           => json_encode([
                                                                                       'subject' => 'Seu código de rastreio chegou',
                                                                                       'title'   => 'Código de Rastreio!',
                                                                                       'content' => 'Olá {primeiro_nome}, boas notícias seu pedido ja está a caminho do endereço de entrega. Você pode rastrear a entrega do seu pedido diretamente do site dos Correios.',
                                                                                   ]),
                                                'notification_enum' => 14,
                                                'project_id'        => $projectId,
                                            ],
                                            [
                                                'type_enum'         => self::SMS_TYPE,
                                                'event_enum'        => 6,
                                                'time'              => 'Imediato',
                                                'message'           => 'Olá {primeiro_nome}, seu código de rastreio chegou: {codigo_rastreio} Acesse: {link_rastreamento}',
                                                'notification_enum' => 15,
                                                'project_id'        => $projectId,
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
    public function formatNotificationData(string $message, $sale = null, $project = null, $notificationType = null, $linkCheckout = null, $log = null, $trackingCode = null)
    {
        try {
            if (!empty($message)) {
                if (strpos($message, '{primeiro_nome}') !== false) {
                    if (!empty($sale)) {
                        $clientNameExploded = explode(' ', $sale->customer->name);
                        $message            = str_replace('{primeiro_nome}', $clientNameExploded[0], $message);
                    } else if (!empty($log)) {
                        $clientNameExploded = explode(' ', $log->name);
                        $message            = str_replace('{primeiro_nome}', $clientNameExploded[0], $message);
                    }
                }

                if (strpos($message, '{url_boleto}') !== false) {
                    if ($notificationType == 'sms') {
                        $linkShortenerService = new LinkShortenerService();
                        $link                 = $linkShortenerService->shorten($sale->boleto_link);
                        $message              = str_replace('{url_boleto}', $link, $message);
                    } else {
                        $message = str_replace('{url_boleto}', $sale->boleto_link, $message);
                    }
                }

                if (strpos($message, '{codigo_venda}') !== false) {
                    $saleCode = Hashids::connection('sale_id')->encode($sale->id);
                    $message  = str_replace('{codigo_venda}', '#' . $saleCode, $message);
                }

                if (strpos($message, '{codigo_rastreio}') !== false) {
                    $message = str_replace('{codigo_rastreio}', $trackingCode, $message);
                }

                if (strpos($message, '{projeto_nome}') !== false) {
                    $message = str_replace('{projeto_nome}', $project->name, $message);
                }

                if (strpos($message, '{link_rastreamento}') !== false) {
                    $domainModel = new Domain();
                    $domain      = $domainModel->where('project_id', $sale->project_id)
                                               ->where('status', 3)
                                               ->first();
                    if ($notificationType == 'sms') {
                        $linkShortenerService = new LinkShortenerService();
                        $linkBase             = 'https://tracking.' . $domain->name . '/';
                        $link                 = $linkShortenerService->shorten($linkBase . $trackingCode);
                        $message              = str_replace('{link_rastreamento}', $link, $message);
                    } else {
                        $link    = 'https://tracking.' . $domain->name . '/' . $trackingCode;
                        $message = str_replace('{link_rastreamento}', $link, $message);
                    }
                }

                if (strpos($message, '{link_carrinho_abandonado}') !== false) {
                    if ($notificationType == 'sms') {
                        $linkShortenerService = new LinkShortenerService();
                        $link                 = $linkShortenerService->shorten($linkCheckout);
                        $message              = str_replace('{link_carrinho_abandonado}', $link, $message);
                    } else {
                        $message = str_replace('{link_carrinho_abandonado}', $linkCheckout, $message);
                    }
                }

                return $message;
            } else {
                return '';
            }
        } catch
        (Exception $ex) {
            Log::warning('Erro ao formatar dados da notificação de email - ProjectNotificationService - formatNotificationData');
            report($ex);
        }
    }
}
