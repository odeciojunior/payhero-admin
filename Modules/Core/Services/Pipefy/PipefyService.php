<?php

namespace Modules\Core\Services\Pipefy;

use GuzzleHttp\Client;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserInformation;
use Modules\Core\Services\FoxUtils;

class PipefyService
{
    const LABEL_FIT_TO_SELL = 307002686; // Conta apta a vender
    const LABEL_SOLD = 307002687; // Começou a vender
    const LABEL_WITHOUT_SELLING = 307553134; // 30 dias sem vender
    const LABEL_SALES_BETWEEN_0_100k = 307586178; // De R$0,00 a R$100.000,00
    const LABEL_SALES_BETWEEN_100k_1M = 307586180; // De R$100.000,00 a R$1.000.000,00
    const LABEL_SALES_BETWEEN_1M_10M = 307586185; // De R$1.000.000,00 a R$10.000.000,00
    const LABEL_SALES_BETWEEN_10M_25M = 307586186; // De R$10.000.000,00 a R$25.000.000,00
    const LABEL_SALES_BETWEEN_500k_1M = 307586187; // De R$500.000,00 a R$999.999,99
    const LABEL_SALES_BETWEEN_25M_50M = 307586193; // De R$25.000.000,00 a R$50.000.000,00
    const LABEL_SALES_OVER_50M = 307588157; // Maior que R$50.000.000,00
    const LABEL_TOP_SALE = 307552829;

    const PHASE_REFUSED_DOCUMENT = 315203355; //Coluna Documento recusado
    const PHASE_ACTIVE = 315322780; //Coluna cadastro finalizados e ativos
    const PHASE_ACTIVE_AND_SELLING = 315203350; //Coluna cadastro ativo e vendedno

    const FIELD_REGISTERED_TORES = "lojas_cadastradas"; //Coluna cadastro ativo e vendedno
    const FIELD_REGISTERED_COMPANIES = "empresas_cadastradas";
    public static $FIELDS_API_USER = [
        "nome" => "name",
        "email" => "email",
        "cpf" => "document",
        "celular" => "cellphone",
        "data_do_cadastro" => "created_at",
    ];
    public static $FIELD_API_USER_INFORMATIONS = [
        "nome" => "name",
        "qual_gateway_voc_utiliza_hoje" => "gateway",
        "qual_o_seu_site_de_vendas" => "website_url",
        "como_conheceu_a_cloudfox" => "cloudfox_referer",
        "qual_seu_nicho_de_atua_o" => "nice",
        "qual_e_commerce_voc_usa_hoje" => "ecommerce",
        "qual_seu_faturamento_m_dio_mensal" => "monthly_income",
        //        "range_de_faturamento" => "" , implemntar uma rotina para preencher esse campo
    ];
    private $idBoard;

    public function __construct()
    {
        if (FoxUtils::isProduction()) {
            $this->idBoard = "302406140";
        } else {
            $this->idBoard = "302698601";
        }
    }

    public function updateCardUser(User $user)
    {
        $fieldsApi = "";
        foreach (self::$FIELDS_API_USER as $api => $field) {
            if (!empty($user->$field)) {
                $fieldsApi .= '{fieldId: \\"' . $api . '\\", value: \\"' . $user->$field . '\\"} ';
            }
        }
        $pipefyCardId = $user->pipefy_card_id;

        $graphql =
            "mutation {updateFieldsValues(  input: { nodeId: " .
            $pipefyCardId .
            ", values:[ " .
            $fieldsApi .
            " ]  }),{ success } }";
        $response = $this->request($graphql);

        return $response;
    }

    public function request($graphQL)
    {
        try {
            $client = new Client();
            $response = $client->request("POST", env("PIPEFY_API_URL"), [
                "body" => '{"query":"' . $graphQL . '"}',
                "headers" => [
                    "Accept" => "application/json",
                    "Authorization" => "Bearer " . env("PIPEFY_API_TOKEN"),
                    "Content-Type" => "application/json",
                ],
            ]);

            return $response;
        } catch (\Exception $e) {
            report($e->getMessage());
        }
    }

    public function createCardUser(User $user)
    {
        if (empty($user->pipefy_card_id)) {
            $title = "CPF: " . $user->document . " Nome: " . $user->name;
            $fieldsApi = "";
            foreach (self::$FIELDS_API_USER as $api => $field) {
                if (!empty($user->$field)) {
                    $fieldsApi .= '{field_id: \\"' . $api . '\\", field_value: \\"' . $user->$field . '\\"} ';
                }
            }
            $graphql =
                "mutation { createCard( input: { pipe_id: " .
                $this->idBoard .
                ', title: \\"' .
                $title .
                '\\", fields_attributes: [ ' .
                $fieldsApi .
                " ] }) { clientMutationId card { id title } } }";

            $response = $this->request($graphql);

            $pipefyCard = json_decode($response->getBody());
            //            dd($pipefyCard);
            if (isset($pipefyCard->errors)) {
                return false;
            } else {
                $user->pipefy_card_id = $pipefyCard->data->createCard->card->id;
                $user->save();
                return true;
            }
        }
        return true;
    }

    public function updateCardUserinformations(User $user)
    {
        $userInformations = $user->userInformations()->first();
        $fieldsApi = "";
        foreach (self::$FIELD_API_USER_INFORMATIONS as $api => $field) {
            if ($api == "nome") {
                if (!empty($userInformations->monthly_income) && $userInformations->monthly_income > 0) {
                    $valueMonthlyIncome = number_format($userInformations->monthly_income, "0", ",", ".");
                    $fieldsApi .=
                        '{fieldId: \\"' .
                        $api .
                        '\\", value: \\"' .
                        $valueMonthlyIncome .
                        " - " .
                        $user->$field .
                        '\\"} ';
                }
            } elseif (
                !empty($userInformations->$field) &&
                $api != "qual_e_commerce_voc_usa_hoje" &&
                $api != "como_conheceu_a_cloudfox"
            ) {
                $fieldsApi .= '{fieldId: \\"' . $api . '\\", value: \\"' . $userInformations->$field . '\\"} ';
            } elseif (!empty($userInformations->$field) && $api == "qual_e_commerce_voc_usa_hoje") {
                $options = json_decode($userInformations->$field);
                $values = "[ ";
                foreach ($options as $key => $option) {
                    if ($option == 1) {
                        $values .= '\\"' . ucfirst($key) . '\\",';
                    }
                }
                $values .= " ]";
                $fieldsApi .= '{fieldId: \\"' . $api . '\\", value: ' . $values . "} ";
            } elseif (!empty($userInformations->$field) && $api == "como_conheceu_a_cloudfox") {
                $options = json_decode($userInformations->$field);
                $values = "[ ";
                foreach ($options as $key => $option) {
                    if ($option == 1) {
                        $values .= '\\"' . ucfirst($key) . '\\",';
                    }
                }
                $values .= " ]";
                $fieldsApi .= '{fieldId: \\"' . $api . '\\", value: ' . $values . "} ";
            }
        }

        $pipefyCardId = $user->pipefy_card_id;

        $graphql =
            "mutation {updateFieldsValues(  input: { nodeId: " .
            $pipefyCardId .
            ", values:[ " .
            $fieldsApi .
            " ]  }),{ success userErrors{ message }} }";
        $response = $this->request($graphql);
        $pipefyCard = json_decode($response->getBody());
        if (isset($pipefyCard->errors)) {
            return false;
        } else {
            return true;
        }
    }

    public function updateAssignee(User $user)
    {
        $graphql =
            "mutation {updateCard(  input: { id: " .
            $user->pipefy_card_id .
            ', assignee_ids:[ \\"302090612\\", \\"302144504\\" ]  }),{ clientMutationId card{ id title }} }';
        //        dd($graphql);
        $response = $this->request($graphql);
        $pipefyCard = json_decode($response->getBody());
    }

    public function updateCardLabel(User $user, array $labels)
    {
        $pipefyCardId = $user->pipefy_card_id;
        $pipefyCardDataLocal = empty($user->pipefy_card_data) ? [] : json_decode($user->pipefy_card_data, true);
        $status = false;
        foreach ($labels as $label) {
            if (!empty($pipefyCardDataLocal["labels"])) {
                foreach ($pipefyCardDataLocal["labels"] as $dataLocal) {
                    if ($dataLocal != $label) {
                        $status = true;
                    }
                }
            } else {
                $status = true;
            }
        }

        if ($status) {
            $graphqlLabels = "{ card(id: " . $pipefyCardId . "){ labels{ id } } }";
            $response = $this->request($graphqlLabels);
            $pipefyCard = json_decode($response->getBody());
            foreach ($pipefyCard->data->card->labels as $label) {
                array_unshift($labels, $label->id);
            }

            $labels = array_unique($labels);

            $data = "[";
            foreach ($labels as $label) {
                $data .= '\\"' . $label . '\\", ';
            }
            $data .= "]";

            $graphql =
                "mutation { updateCard( input: { id: " .
                $pipefyCardId .
                ", label_ids: " .
                $data .
                " }, ),{  card { id title  }} }";
            $response = $this->request($graphql);
            $pipefyCard = json_decode($response->getBody());

            if (isset($pipefyCard->errors)) {
                return false;
            } elseif (!empty($pipefyCard->data->updateCard->card->id)) {
                if (!empty($pipefyCardDataLocal["phase"])) {
                    $dataLocal = array_merge(["labels" => $labels], ["phase" => $pipefyCardDataLocal["phase"]]);
                } else {
                    $dataLocal = ["labels" => $labels];
                }

                $user->pipefy_card_data = json_encode($dataLocal);
                $user->save();
            }
        }

        return true;
    }

    public function moveCardToPhase(User $user, $phase)
    {
        $pipefyCardId = $user->pipefy_card_id;
        $pipefyCardDataLocal = empty($user->pipefy_card_data) ? [] : json_decode($user->pipefy_card_data);

        if (empty($pipefyCardDataLocal->phase)) {
            $pipefyPhase = ["phase" => $phase];
            $graphql =
                "mutation { moveCardToPhase(  input: { card_id: " .
                $pipefyCardId .
                ", destination_phase_id:" .
                $phase .
                "  }),{ card { id current_phase{ name } } } }";
        } elseif ($pipefyCardDataLocal->phase != $phase) {
            $pipefyPhase = ["phase" => $phase];
            $graphql =
                "mutation { moveCardToPhase(  input: { card_id: " .
                $pipefyCardId .
                ", destination_phase_id:" .
                $phase .
                "  }),{ card { id current_phase{ name } } } }";
        }

        if (!empty($graphql)) {
            $response = $this->request($graphql);
            $pipefyCard = json_decode($response->getBody());
            if (isset($pipefyCard->errors)) {
                return false;
            } elseif (!empty($pipefyCard->data->moveCardToPhase->card->current_phase->name)) {
                $user->pipefy_card_data = json_encode($pipefyPhase);
                $user->save();
            }
        }

        return true;
    }
}
