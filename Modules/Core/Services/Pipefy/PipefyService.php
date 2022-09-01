<?php
namespace Modules\Core\Services\Pipefy;
use GuzzleHttp\Client;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserInformation;

class PipefyService
{

    private $idBoard;

    const LABEL_INVOICING       = 307521167;
    const LABEL_FIT_TO_SELL     = 307544031;
    const LABEL_SOLD            = 307552823;
    const LABEL_TOP_SALE        = 307552829;
    const LABEL_WITHOUT_SELLING = 307552837;


    public function __construct()
    {
//        $this->idBoard = '302406140';
        $this->idBoard = '302640582';

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

            $fieldDocument = ' ';
            $fieldCellphone = ' ';
            if (! empty($user->cellphone)) {
                $fieldCellphone = '{field_id: \\"celular\\", field_value: \\"'.$user->cellphone.'\\"}';
            }
            if (! empty($user->document)) {
                $document = str_replace(["."," ","-"],"",$user->document);
                $fieldDocument = '{field_id: \\"cpf\\", field_value: \\"'.$document.'\\"}';
            }
            $title = 'CPF: '.$user->document.' Nome: '.$user->name;
            $graphql = 'mutation { createCard( input: { pipe_id: '.$this->idBoard.', title: \\"'.$title.'\\", fields_attributes: [ {field_id: \\"nome\\", field_value: \\"'.$user->name.'\\"},{field_id: \\"email\\", field_value: \\"'.$user->email.'\\"} '.$fieldDocument.' '.$fieldCellphone.' {field_id: \\"data_do_cadastro\\", field_value: \\"'.$user->created_at.'\\"} ] }) { clientMutationId card { id title } } }';

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
        $fieldDocument = '';
        $fieldCellphone = '';
        $fieldName = '';
        $fieldEmail = '';
        $fieldMonthlyIncome = '';

        if (!empty($user->cellphone)) {
            $fieldCellphone = '{fieldId: \\"celular\\", value: \\"'.$user->cellphone.'\\"}';
        }
        if (!empty($user->document)) {
            $fieldDocument = '{fieldId: \\"cpf\\", value: \\"'.$user->document.'\\"}';
        }
        if (!empty($user->name)) {
            $fieldName = '{fieldId: \\"nome\\", value: \\"'.$user->name.'\\"}';
        }
        if (!empty($user->email)) {
            $fieldEmail = '{fieldId: \\"email\\", value: \\"'.$user->email.'\\"}';
        }
        if (!empty($user->userInformations) && !empty($user->userInformations->monthly_income)) {
            $fieldMonthlyIncome = '{fieldId: \\"faturamento_estimado\\", value: \\"'.($user->userInformations->monthly_income/100).'\\"}';
        }

        $data = $fieldEmail.", ".$fieldCellphone.", ".$fieldDocument.", ".$fieldName.", ".$fieldMonthlyIncome;

        $pipefyCardId = $user->pipefy_card_id;
//        $pipefyCardId = 568589818;

        $graphql = 'mutation {updateFieldsValues(  input: { nodeId: '.$pipefyCardId.', values:[ '.$data.' ]  }),{ success } }';
        $response = $this->request($graphql);

        return true;
    }

    public function updateCardLabel(User $user, array $labels)
    {
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

    public function deleteCard($cardId)
    {

    }


}
