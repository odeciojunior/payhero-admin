<?php
namespace Modules\Core\Services\Pipefy;
use GuzzleHttp\Client;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserInformation;

class PipefyService
{

    private $idBoard;

    const LABEL_FIT_TO_SELL                 = 307544031; // Conta apta a vender
    const LABEL_SOLD                        = 307597204; // Começou a vender
    const LABEL_WITHOUT_SELLING             = 307597205; // 30 dias sem vender
    const LABEL_SALES_UNDER_100k            = 307597206; // De R$0,00 a R$100.000,00
    const LABEL_SALES_BETWEEN_100k_1M       = 307597207; // De R$100.000,00 a R$1.000.000,00
    const LABEL_SALES_BETWEEN_1M_10M        = 307597208; // De R$1.000.000,00 a R$10.000.000,00
    const LABEL_SALES_BETWEEN_10M_25M       = 307597209; // De R$10.000.000,00 a R$25.000.000,00
    const LABEL_SALES_BETWEEN_500k_1M       = 307597210; // De R$500.000,00 a R$999.999,99
    const LABEL_SALES_BETWEEN_25M_50M       = 307597211; // De R$25.000.000,00 a R$50.000.000,00
    const LABEL_SALES_OVER_50M              = 307597212; // Maior que R$50.000.000,00
    const LABEL_TOP_SALE                    = 307552829;


    public static $FIELDS_API_USER = [
        "nome" => "name" ,
        "email" => "email" ,
        "cpf" => "document" ,
        "celular" => "cellphone" ,
        "data_do_cadastro" => "created_at" ,
    ];

    public static $FIELD_API_USER_INFORMATIONS = [
        "qual_gateway_voc_utiliza_hoje" => "gateway" ,
        "qual_o_seu_site_de_vendas" => "website_url" ,
        "como_conheceu_a_cloudfox" => "cloudfox_referer" ,
        "qual_seu_nicho_de_atua_o" => "nice" ,
        "qual_e_commerce_voc_usa_hoje" => "ecommerce" ,
        //"tem_site_de_vendas" => "website_url" ,
        //"utiliza_gateway_de_pagamento" => "gateway" ,
        "qual_seu_faturamento_m_dio_mensal" => "monthly_income" ,
//        "range_de_faturamento" => "" , esse campo é preenchido pelo comercial???
    ];

    const  FIELD_REGISTERED_TORES = "lojas_cadastradas";
    const  FIELD_REGISTERED_COMPANIES = "empresas_cadastradas";


    public function __construct()
    {
//        $this->idBoard = '302406140';
        $this->idBoard = '302680894';

    }

    public function request($graphQL)
    {
        try {
            $client = new Client();
            $response = $client->request('POST', env('PIPEFY_API_URL'), [
                'body' => '{"query":"'.$graphQL.'"}',
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.env('PIPEFY_API_TOKEN'),
                    'Content-Type' => 'application/json',
                ],
            ]);

            return $response;
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function createCardUser(User $user)
    {
        if (empty($user->pipefy_card)){

            $title = 'CPF: '.$user->document.' Nome: '.$user->name;
            $fieldsApi = '';
            foreach (self::$FIELDS_API_USER as $api =>$field){
                if (!empty($user->$field)){
                    $fieldsApi .= '{field_id: \\"'.$api.'\\", field_value: \\"'.$user->$field.'\\"} ';
                }
            }
            $graphql = 'mutation { createCard( input: { pipe_id: '.$this->idBoard.', title: \\"'.$title.'\\", fields_attributes: [ '.$fieldsApi.' ] }) { clientMutationId card { id title } } }';

            $response = $this->request($graphql);

            $pipefyCard = json_decode($response->getBody());

            if (!empty($pipefyCard->data->createCard->card->id)) {
                $pipefyCardArray = [
                    'pipefy_card_id' => $pipefyCard->data->createCard->card->id,
                ];
                $user->pipefy_card = json_encode($pipefyCardArray);
                $user->save();
            }

            return $user;
        }
        return false;
    }

    public function updateCardUser(User $user)
    {
        $fieldsApi = '';
        foreach (self::$FIELDS_API_USER as $api =>$field){
            if (!empty($user->$field)){
                $fieldsApi .= '{fieldId: \\"'.$api.'\\", value: \\"'.$user->$field.'\\"} ';
            }
        }

//        $pipefyCardId = $user->pipefy_card_id;
        $pipefyCardId = 573666192;

        $graphql = 'mutation {updateFieldsValues(  input: { nodeId: '.$pipefyCardId.', values:[ '.$fieldsApi.' ]  }),{ success } }';
        $response = $this->request($graphql);

        return true;
    }

    public function updateCardUserinformations(User $user)
    {
        $userInformations = $user->userInformations()->first();
        $fieldsApi = '';
        foreach (self::$FIELD_API_USER_INFORMATIONS as $api =>$field){
            if (!empty($userInformations->$field) && $api != 'qual_e_commerce_voc_usa_hoje' && $api != 'como_conheceu_a_cloudfox'){
                $fieldsApi .= '{fieldId: \\"'.$api.'\\", value: \\"'.$userInformations->$field.'\\"} ';
            }elseif (!empty($userInformations->$field) && $api == 'qual_e_commerce_voc_usa_hoje'){
                $options = json_decode($userInformations->$field);
                $values = '[ ';
                foreach ($options as $key => $option){
                    if ($option == 1){
                        $values .= '\\"'.ucfirst($key).'\\",';
                    }
                }
                $values .= ' ]';
                $fieldsApi .= '{fieldId: \\"'.$api.'\\", value: '.$values.'} ';
            }elseif (!empty($userInformations->$field) && $api == 'como_conheceu_a_cloudfox'){
                $options = json_decode($userInformations->$field);
                $values = '[ ';
                foreach ($options as $key => $option){
                    if ($option == 1){
                        $values .= '\\"'.ucfirst($key).'\\",';
                    }
                }
                $values .= ' ]';
                $fieldsApi .= '{fieldId: \\"'.$api.'\\", value: '.$values.'} ';
            }
        }

//        $pipefyCardId = $user->pipefy_card_id;
        $pipefyCardId = 574412788;

        $graphql = 'mutation {updateFieldsValues(  input: { nodeId: '.$pipefyCardId.', values:[ '.$fieldsApi.' ]  }),{ success userErrors{ message }} }';
        $response = $this->request($graphql);
        $pipefyCard = json_decode($response->getBody());
        //dd($pipefyCard);
        return true;
    }

    public function updateCardLabel(User $user, array $labels)
    {
        $pipefyCardId = 574419411;

        $graphqlLabels = '{ card(id: '.$pipefyCardId.'){ labels{ id } } }';
        $response = $this->request($graphqlLabels);
        $pipefyCard = json_decode($response->getBody());
        foreach ($pipefyCard->data->card->labels as $label){
            array_unshift($labels, $label->id);
        }

        $labels = array_unique($labels);

        $data = "[";
        foreach ($labels as $label){
            $data .= '\\"'.$label.'\\", ';
        }
        $data .= "]";

//        $pipefyCardId = $pipefyData->pipefy_card_id;


        $graphql = 'mutation { updateCard( input: { id: '.$pipefyCardId.', label_ids: '.$data.' }, ),{  card { id title  }} }';
        $response = $this->request($graphql);
        $pipefyCard = json_decode($response->getBody());

        if (!empty($pipefyCard->data->updateCard->card->id)) {
            $pipefyCardArray = [
                'pipefy_card_id' => $pipefyCard->data->updateCard->card->id,
                'labels' => $labels
            ];
            $user->pipefy_card = json_encode($pipefyCardArray);
            $user->save();
        }


        return true;
    }

/*
    public function updateCardLabel(User $user, array $labels)
    {
        return false;// Faz o update sem manter o historico
        $pipefyData = json_decode($user->pipefy_card);
        $pipefyLabels = array_merge($pipefyData->labels,$labels);
        $labels = array_unique($pipefyLabels);

        $data = "[";
        foreach ($labels as $label){
            $data .= '\\"'.$label.'\\", ';
        }
        $data .= "]";

//        $pipefyCardId = $pipefyData->pipefy_card_id;
        $pipefyCardId = 568589818;

        $graphql = 'mutation { updateCard( input: { id: '.$pipefyCardId.', label_ids: '.$data.' }, ),{  card { id title  }} }';
        $response = $this->request($graphql);
        $pipefyCard = json_decode($response->getBody());

        if (!empty($pipefyCard->data->updateCard->card->id)) {
            $pipefyCardArray = [
                'pipefy_card_id' => $pipefyCard->data->updateCard->card->id,
                'labels' => $labels
            ];
            $user->pipefy_card = json_encode($pipefyCardArray);
            $user->save();
        }

        return true;
    }
*/

}
