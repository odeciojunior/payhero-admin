<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ActivecampaignIntegration;
use Modules\Core\Entities\ActivecampaignSent;
use Vinkla\Hashids\Facades\Hashids;

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
        // $this->apiKey        = 'teste';
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
        return $this->sendDataActiveCampaign('', 'tags', 'GET');
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
        return $this->sendDataActiveCampaign('', 'lists', 'GET');
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
     * @param  $saleId
     * @param  $eventSale
     * @param  $name
     * @param  $phone
     * @param  $email
     * @param  $projectId
     */
    public function execute($saleId, $eventSale, $name, $phone, $email, $projectId)
    {
        try {
            $activecampaignIntegration = new ActivecampaignIntegration;
            $integration = $activecampaignIntegration->where('project_id', $projectId)->with('events' => function($query) use($eventSale) {
                $query->where('event_sale', $eventSale);
            })->first();

            if(!empty($integration->events[0]->id)) {
                $this->setAccess($integration->api_url, $integration->api_key, $integration->id);

                $data = [
                    'firstName' => $name,
                    'phone'     => $phone,
                    'email'     => $email,
                    'lastName'  => '',
                ];

                return $this->sendContact($data, $integration->events[0]);
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
     */
    public function sendContact($data, $event, $saleId)
    {
        try {

            $contact = $this->createOrUpdateContact($data);
            $contact = json_decode($contact, true);
            if (isset($contact['contact']['id'])) {
                // adicionar tags no contato
                $arrayApply = explode(',', $event->add_tags);
                foreach ($arrayApply as $key => $value) {
                    $tagsApply[] = $this->addTagContact($value, $contact['contact']['id']);
                }
                // remover tags no contato
                $tagsContact      = $this->getTagsContact($contact['contact']['id']);
                $tagsContact      = json_decode($tagsContact, true);
                $arrayTagsContact = [];
                foreach ($tagsContact['contactTags'] as $key => $tag) {
                    $arrayTagsContact[$tag['tag']] = $tag['id'];
                }
                $tagsRemove  = [];
                $arrayRemove = explode(',', $event->remove_tags);
                foreach ($arrayRemove as $key => $value) {
                    $contactTagId = $arrayTagsContact[$value] ?? 0;

                    if ($contactTagId)
                        $tagsRemove[] = $this->removeTagContact($contactTagId);
                }

                if(!empty($event->add_list)) {
                    $addList = $this->updateContactList($event->add_list, $contact['contact']['id'], 1);
                }
                if(!empty($event->remove_list)) {
                    $removeList = $this->updateContactList($event->remove_list, $contact['contact']['id'], 0);
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
                    'data'                          => json_encode(['contact' => $data, 'tags_add' => $arrayApply ?? null, 'tags_remove' => $arrayRemove ?? null]),
                    'response'                      => json_encode(['contact' => $contact, 'tags' => $return ?? null]),
                    'sent_status'                   => $sentStatus,
                    'sale_id'                       => $saleId,
                    'event_sale'                    => $eventSale,
                    'activecampaign_integration_id' => $this->integrationId,
                ]
            );
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }
}