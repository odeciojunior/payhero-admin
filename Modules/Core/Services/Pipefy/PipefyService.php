<?php

namespace Modules\Core\Services\Pipefy;

use Exception;
use GuzzleHttp\Client;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Core\Services\FoxUtils;

class PipefyService
{
    const PIPE_MORE_100k = 302667969;
    const PIPE_LESS_100k = 302668239;

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
    const LABEL_FACEBOOK_ADS = 307722758;
    const LABEL_GOOGLE_ADS = 307722759;

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

    public static $FIELDS_API_USER_PIPE_ACCOUNTS = [
        "nome" => "name",
        "email_cadastrado" => "email",
        "cnpjs" => "document",
        "celular" => "cellphone",
        "title" => "document",
        //        "data_do_cadastro" => "created_at",
    ];

    public static $FIELD_API_USER_INFORMATIONS = [
        "nome" => "name",
        "qual_gateway_voc_utiliza_hoje" => "gateway",
        "qual_o_seu_site_de_vendas" => "website_url",
        "como_conheceu_a_cloudfox" => "cloudfox_referer",
        "qual_seu_nicho_de_atua_o" => "niche",
        "qual_e_commerce_voc_usa_hoje" => "ecommerce",
        "qual_seu_faturamento_m_dio_mensal" => "monthly_income",
        //        "range_de_faturamento" => "" , implemntar uma rotina para preencher esse campo
    ];

    public static $FIELD_MODEL_CLOUDFOX_LABEL_API = [
        "cloudfox_referer" => [
            "ad" => "Anúncios",
            "email" => "Email",
            "other" => "Outros",
            "youtube" => "Youtube",
            "facebook" => "Facebook",
            "linkedin" => "Linkedin",
            "instagram" => "Instagram",
            "recomendation" => "Recomendações",
        ],
        "niche" => [
            "others" => "Outros",
            "classes" => "Cursos",
            "subscriptions" => "Assinaturas",
            "digitalProduct" => "Produtos digitais",
            "physicalProduct" => "Produtos físicos",
            "dropshippingImport" => "Dropshipping",
        ],
        "ecommerce" => [
            "wix" => "Wix",
            "shopify" => "Shopify",
            "pageLand" => "Landing Page",
            "wooCommerce" => "Woocommerce",
            "otherEcommerce" => "Outros",
            "integratedStore" => "Loja integrada",
        ],
    ];

    private $idBoard;

    public function __construct()
    {
        if (FoxUtils::isProduction()) {
            $this->idBoard = "302406140";
        } else {
            dd("AMBIENTE LOCAL");
            $this->idBoard = "302736162";
        }
    }

    public function updateCardUser(User $user)
    {
        try {
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
        } catch (Exception $e) {
            report($e);
            return false;
        }
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
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function createCardUser(User $user, $pipefyPipe = null)
    {
        try {
            if (empty($user->pipefy_card_id)) {
                $pipefyCardDataLocal = empty($user->pipefy_card_data) ? [] : json_decode($user->pipefy_card_data, true);

                $pipefyCardDataLocal["pipe_id"] = empty($pipefyPipe) ? $this->idBoard : $pipefyPipe;

                $title = "CPF: " . $user->document . " Nome: " . $user->name;
                $fieldsApi = "";
                foreach (self::$FIELDS_API_USER as $api => $field) {
                    if (!empty($user->$field)) {
                        $fieldsApi .= '{field_id: \\"' . $api . '\\", field_value: \\"' . $user->$field . '\\"} ';
                    }
                }
                $pipefyPipe = empty($pipefyPipe) ? $this->idBoard : $pipefyPipe;

                $graphql =
                    "mutation { createCard( input: { pipe_id: " .
                    $pipefyPipe .
                    ', title: \\"' .
                    $title .
                    '\\", fields_attributes: [ ' .
                    $fieldsApi .
                    " ] }) { clientMutationId card { id title } } }";

                $response = $this->request($graphql);

                if (!empty($response->getBody())) {
                    $pipefyCard = json_decode($response->getBody());
                    //                dd($pipefyCard);
                    if (isset($pipefyCard->errors)) {
                        return false;
                    } else {
                        $user->pipefy_card_id = $pipefyCard->data->createCard->card->id;
                        $user->pipefy_card_data = json_encode($pipefyCardDataLocal);
                        $user->save();
                        return true;
                    }
                } else {
                    return false;
                }
            }
            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function createCardUserNewPipe(User $user, $pipefyPipe)
    {
        try {
            $pipefyCardDataLocal = empty($user->pipefy_card_data) ? [] : json_decode($user->pipefy_card_data, true);
            $pipefyCardDataLocal["pipe_id"] = $pipefyPipe;

            if (!empty($user->total_commission_value) && $user->total_commission_value > 0) {
                $value = $user->total_commission_value / 100;
                $valueMonthlyIncome = number_format($value, 2, ",", ".");
                $title = $valueMonthlyIncome . " - " . $user->name;
            } else {
                $title = $user->name;
            }

            $fieldsApi = "";
            foreach (self::$FIELDS_API_USER_PIPE_ACCOUNTS as $api => $field) {
                if ($api == "cnpjs") {
                    $companies = Company::where("user_id", $user->id)->get();
                    $documents = "";
                    foreach ($companies as $company) {
                        $documents .= FoxUtils::getDocument($company->document) . "; ";
                    }
                    $fieldsApi .= '{field_id: \\"' . $api . '\\", field_value: \\"' . $documents . '\\"} ';
                } elseif ($api == "title") {
                    $fieldsApi .= '{field_id: \\"' . $api . '\\", field_value: \\"' . $title . '\\"} ';
                } elseif (!empty($user->$field)) {
                    $fieldsApi .= '{field_id: \\"' . $api . '\\", field_value: \\"' . $user->$field . '\\"} ';
                }
            }

            $pipefyPipe = $pipefyPipe;

            $graphql =
                "mutation { createCard( input: { pipe_id: " .
                $pipefyPipe .
                ', title: \\"' .
                $title .
                '\\", fields_attributes: [ ' .
                $fieldsApi .
                " ] }) { clientMutationId card { id title } } }";

            $response = $this->request($graphql);

            if (!empty($response->getBody())) {
                $pipefyCard = json_decode($response->getBody());
                if (isset($pipefyCard->errors)) {
                    //                    dd($pipefyCard->errors);
                    return false;
                } else {
                    $user->pipefy_card_id = $pipefyCard->data->createCard->card->id;
                    $user->pipefy_card_data = json_encode($pipefyCardDataLocal);
                    $user->save();

                    return true;
                }
            } else {
                return false;
            }

            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function updateCardUserinformations(User $user)
    {
        try {
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
                    $api != "como_conheceu_a_cloudfox" &&
                    $api != "qual_seu_nicho_de_atua_o"
                ) {
                    $fieldsApi .= '{fieldId: \\"' . $api . '\\", value: \\"' . $userInformations->$field . '\\"} ';
                } elseif (
                    !empty($userInformations->$field) &&
                    ($api == "qual_e_commerce_voc_usa_hoje" ||
                        $api == "qual_seu_nicho_de_atua_o" ||
                        $api == "como_conheceu_a_cloudfox")
                ) {
                    $options = self::$FIELD_MODEL_CLOUDFOX_LABEL_API[$field];
                    $values = "[ ";
                    foreach ($options as $modelAtribute => $optionLabel) {
                        $selected = json_decode($userInformations->$field);
                        foreach ($selected as $option => $select) {
                            if ($option == $modelAtribute && $select == 1) {
                                $values .= '\\"' . $optionLabel . '\\",';
                            }
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
            if (!empty($response->getBody())) {
                $pipefyCard = json_decode($response->getBody());
                if (isset($pipefyCard->errors)) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function updateCardLabel(User $user, array $labels, $pipefyPipe = null)
    {
        try {
            $pipefyCardId = $user->pipefy_card_id;
            $pipefyCardDataLocal = empty($user->pipefy_card_data) ? [] : json_decode($user->pipefy_card_data, true);
            $pipefyCardDataLocal["pipe_id"] = empty($pipefyPipe) ? $this->idBoard : $pipefyPipe;
            $status = false;
            foreach ($labels as $label) {
                if (!empty($pipefyCardDataLocal["labels"])) {
                    foreach ($pipefyCardDataLocal["labels"] as $dataLocal) {
                        if ($dataLocal != $label) {
                            $status = true;
                        } else {
                            $status = false;
                            break;
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
                if (!empty($response->getBody())) {
                    $pipefyCard = json_decode($response->getBody());
                    if (isset($pipefyCard->errors)) {
                        return false;
                    } elseif (!empty($pipefyCard->data->updateCard->card->id)) {
                        $dataLocal = ["labels" => $labels];
                        $dataLocal = array_merge($dataLocal, ["pipe" => $pipefyCardDataLocal["pipe_id"]]);
                        if (!empty($pipefyCardDataLocal["phase"])) {
                            $dataLocal = array_merge($dataLocal, ["phase" => $pipefyCardDataLocal["phase"]]);
                        }
                        $user->pipefy_card_data = json_encode($dataLocal);
                        $user->save();
                    }
                } else {
                    return false;
                }
            }
            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function moveCardToPhase(User $user, $phase, $pipefyPipe = null)
    {
        try {
            $pipefyCardId = $user->pipefy_card_id;
            $pipefyCardDataLocal = empty($user->pipefy_card_data) ? [] : json_decode($user->pipefy_card_data, true);
            $pipefyCardDataLocal["pipe_id"] = empty($pipefyPipe) ? $this->idBoard : $pipefyPipe;
            if (empty($pipefyCardDataLocal["phase"])) {
                $pipefyPhase = ["pipe_id" => $pipefyCardDataLocal["pipe_id"], "phase" => $phase];
                $graphql =
                    "mutation { moveCardToPhase(  input: { card_id: " .
                    $pipefyCardId .
                    ", destination_phase_id:" .
                    $phase .
                    "  }),{ card { id current_phase{ name } } } }";
            } elseif ($pipefyCardDataLocal["phase"] != $phase) {
                $pipefyPhase = ["pipe_id" => $pipefyCardDataLocal["pipe_id"], "phase" => $phase];
                $graphql =
                    "mutation { moveCardToPhase(  input: { card_id: " .
                    $pipefyCardId .
                    ", destination_phase_id:" .
                    $phase .
                    "  }),{ card { id current_phase{ name } } } }";
            }

            if (!empty($graphql)) {
                $response = $this->request($graphql);
                if (!empty($response->getBody())) {
                    $pipefyCard = json_decode($response->getBody());
                    if (isset($pipefyCard->errors)) {
                        return false;
                    } elseif (!empty($pipefyCard->data->moveCardToPhase->card->current_phase->name)) {
                        $user->pipefy_card_data = json_encode($pipefyPhase);
                        $user->save();
                    }
                } else {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function deleteCard(User $user)
    {
        try {
            if (!empty($user->pipefy_card_id)) {
                $graphql = "mutation{   deleteCard(input: {id: {$user->pipefy_card_id} }){success} }";
                $response = $this->request($graphql);
                if (!empty($response->getBody())) {
                    $pipefyCard = json_decode($response->getBody());

                    $user->pipefy_card_id = null;
                    $user->pipefy_card_data = null;
                    $user->save();
                }
            }
            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function updateAssignee(User $user)
    {
        try {
            $graphql =
                "mutation {updateCard(  input: { id: " .
                $user->pipefy_card_id .
                ', assignee_ids:[ \\"302090612\\", \\"302144504\\" ]  }),{ clientMutationId card{ id title }} }';
            //        dd($graphql);
            $response = $this->request($graphql);
            //        $pipefyCard = json_decode($response->getBody());
            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }
}
