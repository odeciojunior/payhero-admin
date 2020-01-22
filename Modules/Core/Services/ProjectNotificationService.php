<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\ProjectNotification;
use Modules\Products\Transformers\ProductsSaleResource;

/**
 * Class ProjectNotificationService
 * @package Modules\Core\Services
 */
class ProjectNotificationService
{
    /**
     * @param $projectId
     * @return string
     */
    public function createProjectNotificationDefault($projectId)
    {
        try {
            ProjectNotification::insert([
                                            [
                                                'type_enum'  => 2, // sms
                                                'event_enum' => 1,
                                                'time'       => 'Imediato',
                                                'message'    => 'Olá {primeiro_nome}, não esqueça de pagar seu boleto para enviarmos seu pedido! {url_boleto}',
                                                'project_id' => $projectId,
                                            ],
                                            [
                                                'type_enum'  => 2,
                                                'event_enum' => 5,
                                                'time'       => '10:00 horas',
                                                'message'    => 'Olá {primeiro_nome}, seu boleto vence hoje, não deixe de efetuar o pagamento e garantir seu pedido! {url_boleto}',
                                                'project_id' => $projectId,
                                            ],
                                            [
                                                'type_enum'  => 2,
                                                'event_enum' => 4,
                                                'time'       => '1 hora depois',
                                                'message'    => 'Olá {primeiro_nome}, somos da loja {projeto_nome}, vimos que voce não finalizou seu pedido, aproveite o último dia da promoção! {link_carrinho_abandonado}',
                                                'project_id' => $projectId,
                                            ],
                                            [
                                                'type_enum'  => 2,
                                                'event_enum' => 4,
                                                'time'       => '10:00 horas próximo dia',
                                                'message'    => 'Olá {primeiro_nome}, somos da loja {projeto_nome}, vimos que voce não finalizou seu pedido, aproveite o último dia da promoção! {link_carrinho_abandonado}',
                                                'project_id' => $projectId,
                                            ],
                                            [
                                                'type_enum'  => 1,
                                                'event_enum' => 1,
                                                'time'       => 'Imediato',
                                                'message'    => json_encode([
                                                                                'subject' => 'Parabéns - Pegue aqui o seu boleto - Pedido #{{sale_code}}',
                                                                                'title'   => 'Aqui está seu boleto',
                                                                                'content' => 'Olá {{ first_name }}, Como você optou por Boleto Bancário, estamos enviando por aqui para você não se esquecer. O boleto deve ser pago até a data de vencimento para enviarmos seu(s) pedido(s).',
                                                                            ]),
                                                'project_id' => $projectId,
                                            ],
                                            // pagina 2
                                            [
                                                'type_enum'  => 1,
                                                'event_enum' => 1,
                                                'time'       => '10:00 horas próximo dia',
                                                'message'    => json_encode([
                                                                                'subject' => 'Já separamos seu pedido',
                                                                                'title'   => 'Já separamos seu pedido. Agora só falta você fazer o pagamento do boleto! :)',
                                                                                'content' => 'Olá {{ name }}, estamos enviando esse e-mail só pra avisar que já empacotamos a sua encomenda e estamos prontos para enviar para você. Assim que o boleto for pago e recebermos a confirmação, sua encomenda será enviada!',
                                                                            ]),
                                                'project_id' => $projectId,
                                            ],
                                            [
                                                'type_enum'  => 1,
                                                'event_enum' => 1,
                                                'time'       => '10:00 horas 2 dias após',
                                                'message'    => json_encode([
                                                                                'subject' => 'Vamos ter que liberar sua mercadoria',
                                                                                'title'   => 'Vamos ter que devolver sua mercadoria para o estoque!',
                                                                                'content' => 'Olá {{ name }}, por falta de pagamento, vamos ter que liberar sua mercadoria para o estoque novamente. Isso significa que se você não efetuar o pagamento, cancelaremos seu pedido.',
                                                                            ]),
                                                'project_id' => $projectId,
                                            ],
                                            [
                                                'type_enum'  => 1,
                                                'event_enum' => 5,
                                                'time'       => '10:00 horas',
                                                'message'    => json_encode([
                                                                                'subject' => 'Hoje vence o seu boleto',
                                                                                'title'   => 'Seu boleto vence hoje! Não esqueça de pagar seu boleto para finalizar seu pedido.',
                                                                                'content' => 'Olá {{ name }}, seu boleto vencerá hoje, ainda dá tempo de pagar! Não se esqueça, só enviaremos o seu pedido (que já está separado) se você efetuar o pagamento.',
                                                                            ]),
                                                'project_id' => $projectId,
                                            ],
                                            [
                                                'type_enum'  => 1,
                                                'event_enum' => 4,
                                                'time'       => '1 hora depois',
                                                'message'    => json_encode([
                                                                                'subject' => 'Você pode perder dinheiro se ignorar esse email',
                                                                                'title'   => 'A promoção termina hoje',
                                                                                'content' => 'Olá {{ name }}, Nossos produtos estão com preços especiais e o estoque é bem limitado. Recomendamos que você finalize a compra ainda hoje para garantir a promoção e economizar dinheiro.',
                                                                            ]),
                                                'project_id' => $projectId,
                                            ],
                                            [
                                                'type_enum'  => 1,
                                                'event_enum' => 4,
                                                'time'       => '10:00 horas próximo dia',
                                                'message'    => json_encode([
                                                                                'subject' => 'Posso liberar o seu pedido para outra pessoa?',
                                                                                'title'   => 'O seu pedido está te esperando',
                                                                                'content' => 'Olá {{ name }}, vimos que você não aproveitou a promoção de ontem. O seu pedido ainda está separado aguardando a finalização da compra, mas não podemos segurar por muito tempo.',
                                                                            ]),
                                                'project_id' => $projectId,
                                            ],
                                            // pagina 3
                                            [
                                                'type_enum'  => 2,
                                                'event_enum' => 3,
                                                'time'       => 'Imediato',
                                                'message'    => 'Olá {primeiro_nome}, seu pedido foi confirmado! Em breve lhe enviaremos o código de rastreio',
                                                'project_id' => $projectId,
                                            ],
                                            [
                                                'type_enum'  => 1,
                                                'event_enum' => 3,
                                                'time'       => 'Imediato',
                                                'message'    => json_encode([
                                                                                'subject' => 'Sua compra foi aprovada - Pedido #{{sale_code}}',
                                                                                'title'   => 'Sua compra foi aprovada!',
                                                                                'content' => 'Olá {{ first_name }}, seu pedido #{{sale_code}} foi confirmado. Aqui estão as informações e os detalhes da sua compra.',
                                                                            ]),
                                                'project_id' => $projectId,
                                            ],
                                            [
                                                'type_enum'  => 1,
                                                'event_enum' => 2,
                                                'time'       => 'Imediato',
                                                'message'    => json_encode([
                                                                                'subject' => 'Boleto pago - Pedido #{{sale_code}}',
                                                                                'title'   => 'Boleto pago',
                                                                                'content' => 'Olá {{ first_name }}, seu pedido #{{sale_code}} foi aprovado. Obrigado pela sua compra, nos próximos dias enviaremos o código de rastreio para você acompanhar seu pedido.',
                                                                            ]),
                                                'project_id' => $projectId,
                                            ],
                                            [
                                                'type_enum'  => 1,
                                                'event_enum' => 6,
                                                'time'       => 'Imediato',
                                                'message'    => json_encode([
                                                                                'subject' => 'Seu código de rastreio chegou',
                                                                                'title'   => 'Código de Rastreio!',
                                                                                'content' => 'Olá {{ name }}, boas notícias seu pedido ja está a caminho do endereço de entrega. Você pode rastrear a entrega do seu pedido diretamente do site dos Correios.',
                                                                            ]),
                                                'project_id' => $projectId,
                                            ],
                                            [
                                                'type_enum'  => 2,
                                                'event_enum' => 6,
                                                'time'       => 'Imediato',
                                                'message'    => 'Olá {primeiro_nome}, seu código de rastreio chegou: {codigo_rastreio} Acesse: {link_rastreamento}',
                                                'project_id' => $projectId,
                                            ],
                                        ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function formatNotificationData(int $projectId, int $eventEnum)
    {
        try {
            $projectNotificationModel     = new ProjectNotification();
            $projectNotificationPresenter = $projectNotificationModel->present();
            $projectNotification          = $projectNotificationModel->where('project_id', $projectId)
                                                                     ->where('event_enum', $eventEnum)
                                                                     ->where('type_enum', $projectNotificationPresenter->getTypeEnum('email'))
                                                                     ->where('status', $projectNotificationPresenter->getStatus('active'))
                                                                     ->first();
            if (!empty($projectNotification)) {
                $message = json_decode($projectNotification->message);

                return $message;
            } else {
                return '';
            }
        } catch (Exception $ex) {
            Log::warning('Erro ao formatar dados da notificação de email - ProjectNotificationService - formatNotificationData');
            report($ex);
        }
    }
}


