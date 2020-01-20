<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\ProjectNotification;

/**
 * Class ProjectNotificationService
 * @package Modules\Core\Services
 */
class ProjectNotificationService
{
    /**
     * @param int $projectId
     */
    public function createProjectNotificationDefault($projectId)
    {
        try {

            ProjectNotification::create([
                'type_enum'  => 2, // sms
                'event_enum' => 1,
                'time'       => 'Imediato',
                'message'    => 'Olá {primeiro_nome}, não esqueça de pagar seu boleto para enviarmos seu pedido! {url_boleto}',
                'project_id' => $projectId,
            ]);

            ProjectNotification::create([
                'type_enum'  => 2,
                'event_enum' => 5,
                'time'       => '10:00 horas',
                'message'    => 'Olá {primeiro_nome}, seu boleto vence hoje, não deixe de efetuar o pagamento e garantir seu pedido! {url_boleto}',
                'project_id' => $projectId,
            ]);

            ProjectNotification::create([
                'type_enum'  => 2,
                'event_enum' => 4,
                'time'       => '1 hora depois',
                'message'    => 'Olá {primeiro_nome}, somos da loja {projeto_nome}, vimos que voce não finalizou seu pedido, aproveite o último dia da promoção! {link_carrinho_abandonado}',
                'project_id' => $projectId,
            ]);

            ProjectNotification::create([
                'type_enum'  => 2,
                'event_enum' => 4,
                'time'       => '10:00 horas próximo dia',
                'message'    => 'Olá {primeiro_nome}, somos da loja {projeto_nome}, vimos que voce não finalizou seu pedido, aproveite o último dia da promoção! {link_carrinho_abandonado}',
                'project_id' => $projectId,
            ]);

            ProjectNotification::create([
                'type_enum'  => 1,
                'event_enum' => 1,
                'time'       => 'Imediato',
                'message'    => 'Olá {primeiro_nome}, aqui está seu boleto. Como você optou por Boleto Bancário, estamos enviando por aqui para você não se esquecer. O boleto deve ser pago até a data de vencimento para enviarmos seu(s) pedido(s)! {url_boleto}',
                'project_id' => $projectId,
            ]);

            // pagina 2

            ProjectNotification::create([
                'type_enum'  => 1,
                'event_enum' => 1,
                'time'       => '10:00 horas próximo dia',
                'message'    => 'Olá {primeiro_nome}, estamos enviando esse email só para avisar que já empacotamos sua encomenda e estamos prontos para enviar para você. Assim que o boleto for pago e recebermos a confirmação sua encomenda será enviada!',
                'project_id' => $projectId,
            ]);

            ProjectNotification::create([
                'type_enum'  => 1,
                'event_enum' => 1,
                'time'       => '10:00 horas 2 dias após',
                'message'    => 'Caso você tenha pago o boleto, desconsidere esse e-mail. Olá {primeiro_nome}, por falta de pagamento vamos ter que liberar sua mercadoria para o estoque novamente. Isso siginigfica que se você não efetuar o pagamento, cancelaremos seu pedido!',
                'project_id' => $projectId,
            ]);

            ProjectNotification::create([
                'type_enum'  => 1,
                'event_enum' => 5,
                'time'       => '10:00 horas',
                'message'    => 'Olá {primeiro_nome}, seu boleto vencerá hoje, ainda dá tempo de pagar! Não se esqueça, só enviaremos o seu pedido (que já está separado) se você efetuar o pagamento! {url_boleto}',
                'project_id' => $projectId,
            ]);

            ProjectNotification::create([
                'type_enum'  => 1,
                'event_enum' => 4,
                'time'       => '1 hora depois',
                'message'    => 'Olá {primeiro_nome}, nossos produtos estão com preços especiais e o estoque é bem limitado. Recomendamos que você finalize a compra ainda hoje para garantir a promoção e economizar dinheiro! {link_carrinho_abandonado}',
                'project_id' => $projectId,
            ]);

            ProjectNotification::create([
                'type_enum'  => 1,
                'event_enum' => 4,
                'time'       => '10:00 horas próximo dia',
                'message'    => 'Olá {primeiro_nome}, vimos que você não aproveitou a promoção de ontem. O seu pedido ainda está separado aguardando a finalização da compra, mas não podemos segurar por muito tempo! {link_carrinho_abandonado}',
                'project_id' => $projectId,
            ]);

            // pagina 3
            ProjectNotification::create([
                'type_enum'  => 2,
                'event_enum' => 3,
                'time'       => 'Imediato',
                'message'    => 'Olá {primeiro_nome}, seu pedido foi confirmado! Em breve lhe enviaremos o código de rastreio',
                'project_id' => $projectId,
            ]);

            ProjectNotification::create([
                'type_enum'  => 1,
                'event_enum' => 3,
                'time'       => 'Imediato',
                'message'    => 'Olá {primeiro_nome}, Seu pedido {codigo_pedido} foi confirmado. Aqui estão as informações e os detalhes da sua compra. {nome_produto} {qtde_produto} {valor_compra}',
                'project_id' => $projectId,
            ]);

            ProjectNotification::create([
                'type_enum'  => 1,
                'event_enum' => 2,
                'time'       => 'Imediato',
                'message'    => 'Olá {primeiro_nome}, Seu pedido {codigo_venda} foi aprovado. Obrigado pela sua compra, nos próximos dias enviaremos o código de rastreiopara você acompanhar seu pedido.',
                'project_id' => $projectId,
            ]);

            ProjectNotification::create([
                'type_enum'  => 1,
                'event_enum' => 6,
                'time'       => 'Imediato',
                'message'    => 'Olá {primeiro_nome}, Boas notícias seu pedido ja está a caminho do endereço de entrega. Você pode rastrear a entrega do seu pedido diretamente do site dos Correios. {nome_produto} {codigo_rastreio} {link_rastreamento} {qtde_produto}',
                'project_id' => $projectId,
            ]);

            ProjectNotification::create([
                'type_enum'  => 2,
                'event_enum' => 6,
                'time'       => 'Imediato',
                'message'    => 'Olá {primeiro_nome}, seu código de rastreio chegou: {codigo_rastreio} Acesse: {link_rastreamento}',
                'project_id' => $projectId,
            ]);

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}


