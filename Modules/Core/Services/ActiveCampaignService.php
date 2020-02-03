<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ActivecampaignIntegration;
use Modules\Core\Entities\ActivecampaignEvent;
use Modules\Core\Entities\ActivecampaignSent;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\ActivecampaignCustom;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Checkout;

class ActiveCampaignService
{
    /**
     * @var mixed
     */
    private $apiKey;
    /**
     * @var string
     */
    private $apiUrl;
    /**
     * @var mixed
     */
    private $integrationId;

     /**
     * @param mixed $data
     * @return bool|string
     * @throws ServiceException
     */
    public function createOrUpdateContact($data)
    {
        try {
            $data = ['contact' => $data];
            return $this->sendDataActiveCampaign($data, 'contact/sync', 'POST');
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $apiUrl
     * @param $apiKey
     * @param $integrationId
     */
    public function setAccess($apiUrl, $apiKey, $integrationId)
    {
        $this->apiKey        = $apiKey;
        $this->apiUrl        = $apiUrl;
        $this->integrationId = $integrationId;
        if ($this->integrationId != null && $this->apiKey != null && $this->apiUrl != null) {
            return true;
        }
        return false;
    }

    /**
     * @return bool|string
     */
    public function getTags()
    {
        $tags = $this->sendDataActiveCampaign('', 'tags?limit=100', 'GET');
        $tags = json_decode($tags, true);
        $total = (int)$tags['meta']['total'] ?? 0;
        $pages = ($total > 0) ? ceil($total/100) : 0;
        $return = $tags;

        for ($i=1; $i < $pages; $i++) { 
            $tags = $this->sendDataActiveCampaign('', 'tags?limit=100&offset=' . ($i*100), 'GET');
            $tags = json_decode($tags, true);
            $return = array_merge_recursive($return, $tags);
        }
        return $return;
    }

    /**
     * @param $contactId
     * @return bool|string
     */
    public function getTagsContact($contactId)
    {
        return $this->sendDataActiveCampaign('', 'contacts/' . $contactId . '/contactTags', 'GET');
    }

    /**
     * @param $tagId
     * @param $contactId
     * @return bool|string
     */
    public function addTagContact($tagId, $contactId)
    {
        $data = ['contactTag' => ['contact' => $contactId, 'tag' => $tagId]];

        return $this->sendDataActiveCampaign($data, 'contactTags', 'POST');
    }

    /**
     * @param $contactTagId
     * @return bool|string
     */
    public function removeTagContact($contactTagId)
    {
        return $this->sendDataActiveCampaign('', 'contactTags/' . $contactTagId, 'DELETE');
    }

    /**
     * @param $data
     * @param $url
     * @param $method
     * @return bool|string
     */
    private function sendDataActiveCampaign($data, $url, $method)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . '/api/3/' . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (!empty($data))
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $headers   = [];
        $headers[] = 'Api-token:' . $this->apiKey;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return $result;
    }

    /**
     * @return json|null
     */
    public function getLists()
    {
        $lists = $this->sendDataActiveCampaign('', 'lists?limit=100', 'GET');
        $lists = json_decode($lists, true);
        $total = (int)$lists['meta']['total'] ?? 0;
        $pages = ($total > 0) ? ceil($total/100) : 0;
        $return = $lists;

        for ($i=1; $i < $pages; $i++) { 
            $lists = $this->sendDataActiveCampaign('', 'lists?limit=100&offset=' . ($i*100), 'GET');
            $lists = json_decode($lists, true);
            $return = array_merge_recursive($return, $lists);
        }
        return $return;
    }

    /**
     * @param  int $listId
     * @param  int $contactId
     * @param  int $status
     * @return json|null
     */
    public function updateContactList($listId, $contactId, $status)
    {
        // status = 1 - adiciona contato na lista
        // status = 0 - remove contato da lista
        $data = ['contactList' => ['contact' => $contactId, 'list' => $listId, 'status' => $status]];

        return $this->sendDataActiveCampaign($data, 'contactLists', 'POST');
    }

    /**
     * @param  $instanceId
     * @param  $eventSale
     * @param  $name
     * @param  $phone
     * @param  $email
     * @param  $projectId
     * @param  $instance
     */
    public function execute($instanceId, $eventSale, $name, $phone, $email, $projectId, $instance, $dataCustom = [], $checkoutId = 0)
    {
        try {
            $activecampaignIntegration = new ActivecampaignIntegration;
            $activecampaignEvent = new ActivecampaignEvent;
            $integration = $activecampaignIntegration->where('project_id', $projectId)->first();

            if(!empty($integration->id)) {
                $event = $activecampaignEvent->where('event_sale', $eventSale)->where('activecampaign_integration_id', $integration->id)->first();

                if(!empty($event->id)) {
                    $this->setAccess($integration->api_url, $integration->api_key, $integration->id);

                    $data = [
                        'firstName' => $name,
                        'phone'     => $phone,
                        'email'     => $email,
                        'lastName'  => '',
                    ];

                    $planSales = PlanSale::with('plan')->where('sale_id', $instanceId)->get();
                    $trackings = Tracking::where('sale_id', $instanceId)->get();
                    $domain    = Domain::where('project_id', $projectId)->first();
                    $checkout  = Checkout::find($checkoutId);

                    $trackingCode = '';
                    $trackingUrl  = '';
                    $domainName   = (!empty($domain->name)) ? $domain->name : 'cloudfox.net';
                    foreach ($trackings as $tracking) {
                        $trackingCode .= $tracking->tracking_code . ', ';
                        $trackingUrl .= 'https://tracking.' . $domainName . '/'.$tracking->tracking_code . ', ';
                    }
                    $dataCustom['projeto_nome']  = Project::find($projectId)->name;
                    $dataCustom['codigo_pedido'] = Hashids::encode($instanceId);

                    foreach ($planSales as $plan) {
                        $dataCustom['produtos'][] = $plan->plan->name;
                    }

                    if(!empty($trackingCode)) {
                        $dataCustom['codigo_rastreio']   = $trackingCode;
                        $dataCustom['link_rastreamento'] = $trackingUrl;
                    }
                    $dataCustom['link_carrinho_abandonado'] = 'https://checkout.' . $domainName . '/recovery/' . $checkout->id_log_session;

                    return $this->sendContact($data, $event, $instanceId, $instance, $dataCustom);
                }
                return response()->json(['message' => 'Ocorreu algum erro'], 400);

            } else {
                return response()->json(['message' => 'Projeto não integrado com ActiveCampaign'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * @param  array $data
     * @param  int $eventEnum
     * @param  int $instanceId
     * @param  string $instance
     */
    public function sendContact($data, $event, $instanceId, $instance, $dataCustom)
    {
        try {

            $contact = $this->createOrUpdateContact($data);
            $contact = json_decode($contact, true);
            if (isset($contact['contact']['id'])) {

                $customFields = ActivecampaignCustom::where('activecampaign_integration_id', $this->integrationId)->get();

                $sentCustom = [];
                $returnCustom = [];
                foreach ($dataCustom as $key => $value) {
                    $field = $customFields->firstWhere('custom_field', $key);
                    if($key == 'produtos') {
                        
                        $fieldOptions = json_decode($this->getCustomField($field->custom_field_id), true);
                        
                        $valueOptionProducts = '';
                        
                        if(isset($fieldOptions['fieldOptions'])) {
                            foreach ($fieldOptions['fieldOptions'] as $fieldOption) {
                                if(in_array($fieldOption['value'], $value)) {
                                    $valueOptionProducts .= '||'. $fieldOption['value'];
                                    $value = array_diff($value, [$fieldOption['value']]);
                                }
                            }
                        }
                        if(count($value) > 0) {
                            $this->createCustomOption($field->custom_field_id, $value);
                        }
                        foreach ($value as $valueOption) {
                            $valueOptionProducts .= '||'. $valueOption;
                        }

                        if(!empty($valueOptionProducts)) {
                            $valueOptionProducts .= '||';
                            $sentCustom[]   = [$contact['contact']['id'], $field->custom_field_id, $valueOptionProducts];
                            $returnCustom[] = $this->setCustomFieldValue($contact['contact']['id'], $field->custom_field_id, $valueOptionProducts);
                        }
                    } else {
                        $sentCustom[]   = [$contact['contact']['id'], $field->custom_field_id, $value];
                        $returnCustom[] = $this->setCustomFieldValue($contact['contact']['id'], $field->custom_field_id, $value);
                    }
                }

                // adicionar tags no contato
                $arrayApply = json_decode($event->add_tags, true);
                if(is_array($arrayApply)) {
                    foreach ($arrayApply as $key => $value) {
                        $tagsApply[] = $this->addTagContact($value['id'], $contact['contact']['id']);
                    }
                }
                // remover tags no contato
                $tagsContact      = $this->getTagsContact($contact['contact']['id']);
                $tagsContact      = json_decode($tagsContact, true);
                $arrayTagsContact = [];
                foreach ($tagsContact['contactTags'] as $key => $tag) {
                    $arrayTagsContact[$tag['tag']] = $tag['id'];
                }
                $tagsRemove  = [];
                $arrayRemove = json_decode($event->remove_tags, true);
                if(is_array($arrayRemove)) {
                    foreach ($arrayRemove as $key => $value) {
                        $contactTagId = $arrayTagsContact[$value['id']] ?? 0;

                        if ($contactTagId)
                            $tagsRemove[] = $this->removeTagContact($contactTagId);
                    }
                }

                if(!empty($event->add_list)) {
                    $idAddList = json_decode($event->add_list, true);
                    if(!empty($idAddList['id'])) {
                        $addList = $this->updateContactList($idAddList['id'], $contact['contact']['id'], 1);
                    }
                }
                if(!empty($event->remove_list)) {
                    $idRemoveList = json_decode($event->remove_list, true);
                    if(!empty($idAddList['id'])) {
                        $removeList = $this->updateContactList($idRemoveList['id'], $contact['contact']['id'], 0);
                    }
                }
                $return            = ['add' => $tagsApply ?? null, 'remove' => $tagsRemove ?? null, 'listAdd' => $addList ?? null, 'listRemove' => $removeList ?? null];
                $sentStatus = 2;
            } else {
                $sentStatus = 1;
            }
            // salvar envio
            $activecampaignSentModel = new ActivecampaignSent;
            $activecampaignSentModel->create(
                [
                    'data'                          => json_encode(['contact' => $data, 'tags_add' => $arrayApply ?? null, 'tags_remove' => $arrayRemove ?? null, 'customs' => $sentCustom ?? null]),
                    'response'                      => json_encode(['contact' => $contact, 'tags' => $return ?? null, 'customs' => $returnCustom ?? null]),
                    'sent_status'                   => $sentStatus,
                    'instance_id'                   => $instanceId,
                    'instance'                      => $instance,
                    'event_sale'                    => $event->event_sale,
                    'activecampaign_integration_id' => $this->integrationId,
                ]
            );
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * @param  string $name
     * @return json
     */
    public function createCustomField($name, $type = 'text')
    {
        $data = [
            "type"  => $type,
            "title" => $name,
        ];
        // return['field']['id']...
        return $this->sendDataActiveCampaign(['field' => $data], 'fields', 'POST');
    }

    /**
     * @param  int $fieldId
     * @param  string $name
     * @return json
     */
    public function updateCustomField($fieldId, $name)
    {
        $data = [
            "type"  => "text",
            "title" => $name,
        ];

        return $this->sendDataActiveCampaign(['field' => $data], 'fields/' . $fieldId, 'PUT');
    }

    /**
     * @return json
     */
    public function getCustomFields()
    {
        $fields = $this->sendDataActiveCampaign(null, 'fields?limit=100', 'GET');
        $fields = json_decode($fields, true);
        $total = 0;
        if(isset($fields['meta']['total']) && !empty($fields['meta']['total'])) {
            $total = $fields['meta']['total'];
        }
        $pages  = ($total > 0) ? ceil($total/100) : 0;
        $return = $fields;

        for ($i=1; $i < $pages; $i++) { 
            $fields = $this->sendDataActiveCampaign(null, 'fields?limit=100&offset=' . ($i*100), 'GET');
            $fields = json_decode($fields, true);
            $return = array_merge_recursive($return, $fields);
        }
        return $return;
    }

    public function getCustomField($id)
    {
        return $this->sendDataActiveCampaign(null, 'fields/'.$id, 'GET');
    }

    public function createCustomOption($fieldId, $dataFields)
    {
        $data = [];
        foreach ($dataFields as $value) {
            $data[] = [
                "field" => $fieldId,
                "label" => $value,
                "value" => $value,
            ];
        }

        return $this->sendDataActiveCampaign(['fieldOptions' => $data], 'fieldOption/bulk', 'POST');
    }

    /**
     * @param int $contactId
     * @param int $fieldId
     * @param string $value
     * @return json
     */
    public function setCustomFieldValue($contactId, $fieldId, $value)
    {
        $data = [
            "contact" => $contactId,
            "field"   => $fieldId,
            "value"   => $value
        ];
        return $this->sendDataActiveCampaign(['fieldValue' => $data], 'fieldValues', 'POST');
    }

    /**
     * @param  int $fieldId
     * @param  int $relationId
     * @return json
     */
    public function createCustomFieldRelation($fieldId, $relationId)
    {
        $data = [
            "field" => $fieldId,
            "relid" => $relationId, // 0 - exibe o campo no contato no Painel do ActiveCampaign
        ];
        return $this->sendDataActiveCampaign(['fieldRel' => $data], 'fieldRels', 'POST');
    }

    /**
     * @param  int $fieldId
     * @return json
     */
    public function getRelationsCustomField($fieldId)
    {
        return $this->sendDataActiveCampaign(null, 'fields/'.$fieldId.'/relations', 'GET');
    }

    /**
     * @param  int $fieldId
     * @return json
     */
    public function deleteCustomField($fieldId)
    {
        return $this->sendDataActiveCampaign(null, 'fields/' . $fieldId, 'DELETE');
    }

    /**
     * @param  int $listId
     * @return json
     */
    public function getContactsByList($listId, $limit, $offset)
    {
        return $this->sendDataActiveCampaign(null, 'contacts/?listid=' . $listId . '&status=1&limit='.$limit.'&offset='.$offset, 'GET');
    }

    /**
     * @param  int $contactId
     * @return json
     */
    public function getContactById($contactId)
    {
        return $this->sendDataActiveCampaign(null, 'contacts/' . $contactId , 'GET');
    }

    /**
     * @param int $integrationId
     */
    public function createCustomFieldsDefault($integrationId)
    {
        $fieldsDefault = [
            'url_boleto',
            'projeto_nome',
            'link_carrinho_abandonado',
            'codigo_pedido',
            'produtos',
            'sub_total',
            'frete',
            'codigo_rastreio',
            'link_rastreamento',
        ];

        $fieldsCreate = $fieldsDefault;

        $customnFieldsActive = $this->getCustomFields();
        if(isset($customnFieldsActive['fields'])) {

            foreach ($customnFieldsActive['fields'] as $value) {
                if(in_array($value['title'], $fieldsCreate)) {
                    $fieldsCreate = array_diff($fieldsCreate, [$value['title']]);
                    ActivecampaignCustom::create([
                        'custom_field'                  => $value['title'],
                        'custom_field_id'               => $value['id'],
                        'activecampaign_integration_id' => $integrationId
                    ]);
                }
            }
        }

        foreach ($fieldsCreate as $value) {
            $newField = json_decode($this->createCustomField($value), true);
            if(isset($newField['field']['id'])) {

                ActivecampaignCustom::create([
                    'custom_field'                  => $value,
                    'custom_field_id'               => $newField['field']['id'],
                    'activecampaign_integration_id' => $integrationId
                ]);

                $this->createCustomFieldRelation($newField['field']['id'], 0);
            }
        }
    }
}