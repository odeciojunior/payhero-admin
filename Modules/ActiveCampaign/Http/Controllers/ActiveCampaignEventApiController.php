<?php

namespace Modules\Activecampaign\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\ActivecampaignIntegration;
use Modules\Core\Entities\ActivecampaignEvent;
use Modules\Core\Services\ActiveCampaignService;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ProjectService;
use Modules\ActiveCampaign\Transformers\ActivecampaignResource;
use Modules\ActiveCampaign\Transformers\ActivecampaignEventResource;
use Modules\Projects\Transformers\ProjectsSelectResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class ActiveCampaignEventApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {

            $request = Request::capture();
            $data    = $request->all();

            $id = Hashids::decode($data['integration']);

            $activecampaignIntegrationModel = new ActivecampaignIntegration();

            activity()->on($activecampaignIntegrationModel)->tap(function(Activity $activity) use ($data) {
                $activity->log_name   = 'visualization';
                $activity->subject_id = current(Hashids::decode($data['integration']));
            })->log('Visualizou tela todos os eventos ActiveCampaign');

            $activecampaignIntegration = $activecampaignIntegrationModel->where('user_id', auth()->id())->with('events')
                                                                        ->where('id', $id)->first();

            $events = $activecampaignIntegration->events ?? collect([]);
            foreach ($events as $key => $event) {
                $eventText         = $this->getEventsName([$event->event_sale]);
                $event->event_text = $eventText[0]['name'];
            }

            // dd( ActivecampaignEventResource::collection($events));
            return ActivecampaignEventResource::collection($events);
        } catch (Exception $e) {

            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * @param $id
     * @return ActivecampaignResource
     */
    public function show($id)
    {
        try {

            $activecampaignEventModel        = new ActivecampaignEvent();
            $activecampaignEvent             = $activecampaignEventModel->find(current(Hashids::decode($id)));
            $event                           = $this->getEventsName([$activecampaignEvent->event_sale]);
            $activecampaignEvent->event_text = $event[0]['name'];

            activity()->on($activecampaignEventModel)->tap(function(Activity $activity) use ($id, $event) {
                $activity->log_name   = 'visualization';
                $activity->subject_id = current(Hashids::decode($id));
            })->log('Visualizou evento ' . $event[0]['name'] . ' do ActiveCampaign');

            return new ActivecampaignEventResource($activecampaignEvent);
        } catch (Exception $e) {

            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        try {
            $data = $request->all();

            if (!empty($data['add_list'])) {
                $addList = explode(';', $data['add_list']);
                if (count($addList) > 1) {
                    $addList = json_encode(['id' => $addList[0], 'list' => $addList[1]]);
                } else {
                    $addList = null;
                }
            }

            if (!empty($data['remove_list'])) {
                $removeList = explode(';', $data['remove_list']);
                if (count($removeList) > 1) {
                    $removeList = json_encode(['id' => $removeList[0], 'list' => $removeList[1]]);
                } else {
                    $removeList = null;
                }
            }

            if (count($data['remove_tags'] ?? []) > 0) {
                $removeTags = [];
                foreach ($data['remove_tags'] as $key => $value) {
                    $tag = explode(';', $value);
                    if (count($tag) > 1) {
                        $removeTags[] = ['id' => $tag[0], 'tag' => $tag[1]];
                    }
                }
                if (count($removeTags) > 0) {
                    $removeTags = json_encode($removeTags);
                } else {
                    $removeTags = null;
                }
            }

            if (count($data['add_tags'] ?? []) > 0) {
                $addTags = [];
                foreach ($data['add_tags'] as $key => $value) {
                    $tag = explode(';', $value);
                    if (count($tag) > 1) {
                        $addTags[] = ['id' => $tag[0], 'tag' => $tag[1]];
                    }
                }
                if (count($addTags) > 0) {
                    $addTags = json_encode($addTags);
                } else {
                    $addTags = null;
                }
            }

            $activecampaignEventModel = new ActivecampaignEvent();

            $integrationId = current(Hashids::decode($data['integration_id']));

            if (!empty($integrationId)) {
                $event = $activecampaignEventModel->where('activecampaign_integration_id', $integrationId)
                                                  ->where('event_sale', $data['events'])->first();
                if ($event) {
                    return response()->json([
                                                'message' => 'Evento já cadastrado',
                                            ], 400);
                }

                $eventCreated = $activecampaignEventModel->create([
                                                                      'event_sale'                    => $data['events'],
                                                                      'add_tags'                      => $addTags ?? null,
                                                                      'remove_tags'                   => $removeTags ?? null,
                                                                      'add_list'                      => $addList ?? null,
                                                                      'remove_list'                   => $removeList ?? null,
                                                                      'activecampaign_integration_id' => $integrationId,
                                                                  ]);

                if ($eventCreated) {
                    return response()->json([
                                                'message' => 'Evento criado com sucesso!',
                                            ], 200);
                } else {

                    return response()->json([
                                                'message' => 'Ocorreu um erro ao salvar o evento',
                                            ], 400);
                }
            } else {

                return response()->json([
                                            'message' => 'Ocorreu um erro ao salvar o evento',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao salvar o evento ActiveCampaignEventController - store');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao salvar o evento',
                                    ], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        try {

            if (!empty($id)) {

                $activecampaignEventModel       = new ActivecampaignEvent();
                $activecampaignIntegrationModel = new ActivecampaignIntegration();
                $activeCampaignService          = new ActiveCampaignService();

                activity()->on($activecampaignIntegrationModel)->tap(function(Activity $activity) use ($id) {
                    $activity->log_name   = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                })->log('Visualizou tela editar evento do ActiveCampaign');

                $eventId = current(Hashids::decode($id));
                $event   = $activecampaignEventModel->where('id', $eventId)->first();
                if ($event) {
                    $integration = $activecampaignIntegrationModel->where('user_id', auth()->id())
                                                                  ->where('id', $event->activecampaign_integration_id)
                                                                  ->first();

                    if ($activeCampaignService->setAccess($integration->api_url, $integration->api_key, $integration->id)) {
                        $tags  = $activeCampaignService->getTags();
                        $lists = $activeCampaignService->getLists();
                    } else {
                        $tags  = null;
                        $lists = null;
                    }

                    $eventName         = $this->getEventsName([$event->event_sale]);
                    $event->event_text = $eventName[0]['name'];
                    $event             = new ActivecampaignEventResource($event);

                    return response()->json(['tags' => $tags, 'lists' => $lists, 'event' => $event], 200);

                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                            ], 400);
                }
            } else {

                return response()->json([
                                            'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar Evento ActiveCampaign (ActiveCampaignEventController - edit)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();

            if (!empty($data['add_list_edit'])) {
                $addList = explode(';', $data['add_list_edit']);
                if (count($addList) > 1) {
                    $addList = json_encode(['id' => $addList[0], 'list' => $addList[1]]);
                } else {
                    $addList = null;
                }
            }

            if (!empty($data['remove_list_edit'])) {
                $removeList = explode(';', $data['remove_list_edit']);
                if (count($removeList) > 1) {
                    $removeList = json_encode(['id' => $removeList[0], 'list' => $removeList[1]]);
                } else {
                    $removeList = null;
                }
            }

            if (count($data['remove_tags_edit'] ?? []) > 0) {
                $removeTags = [];
                foreach ($data['remove_tags_edit'] as $key => $value) {
                    $tag = explode(';', $value);
                    if (count($tag) > 1) {
                        $removeTags[] = ['id' => $tag[0], 'tag' => $tag[1]];
                    }
                }
                if (count($removeTags) > 0) {
                    $removeTags = json_encode($removeTags);
                } else {
                    $removeTags = null;
                }
            }

            if (count($data['add_tags_edit'] ?? []) > 0) {
                $addTags = [];
                foreach ($data['add_tags_edit'] as $key => $value) {
                    $tag = explode(';', $value);
                    if (count($tag) > 1) {
                        $addTags[] = ['id' => $tag[0], 'tag' => $tag[1]];
                    }
                }
                if (count($addTags) > 0) {
                    $addTags = json_encode($addTags);
                } else {
                    $addTags = null;
                }
            }

            $activecampaignEventModel = new ActivecampaignEvent();

            $eventId = current(Hashids::decode($data['event_id_edit']));

            $eventUpdate = $activecampaignEventModel->where('id', $eventId)->update([
                                                                                        'add_tags'    => $addTags ?? null,
                                                                                        'remove_tags' => $removeTags ?? null,
                                                                                        'add_list'    => $addList ?? null,
                                                                                        'remove_list' => $removeList ?? null,
                                                                                    ]);

            if ($eventUpdate) {
                return response()->json([
                                            'message' => 'Evento atualizado com sucesso!',
                                        ], 200);
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro ao atualizar o evento',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao atualizar o evento ActiveCampaignEventController - update');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao atualizar o evento',
                                    ], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $eventId                  = current(Hashids::decode($id));
            $activecampaignEventModel = new ActivecampaignEvent();
            $integration              = $activecampaignEventModel->find($eventId);
            $integrationDeleted       = $integration->delete();
            if ($integrationDeleted) {
                return response()->json([
                                            'message' => 'Evento Removido com sucesso!',
                                        ], 200);
            }

            return response()->json([
                                        'message' => 'Erro ao tentar remover evento',
                                    ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar remover Evento ActiveCampaign (ActiveCampaignEventController - destroy)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao tentar remover, tente novamente mais tarde!',
                                    ], 400);
        }
    }

    public function create()
    {

        try {
            $request = Request::capture();
            $data    = $request->all();

            $id = Hashids::decode($data['integration']);

            $activecampaignIntegrationModel = new ActivecampaignIntegration();

            $integration = $activecampaignIntegrationModel->where('user_id', auth()->id())->with('events')
                                                          ->where('id', $id)->first();

            activity()->on($activecampaignIntegrationModel)->tap(function(Activity $activity) use ($id) {
                $activity->log_name   = 'visualization';
                $activity->subject_id = current($id);
            })->log('Visualizou tela adicionar evento do ActiveCampaign');

            $activeCampaignService = new ActiveCampaignService();

            $eventsIntegration = $integration->events->pluck('event_sale')->toArray();
            $eventsDiff        = array_diff([1, 2, 3, 4, 5], $eventsIntegration);

            $events = $this->getEventsName($eventsDiff);
            $events = collect($events);
            if ($activeCampaignService->setAccess($integration->api_url, $integration->api_key, $integration->id)) {
                $tags  = $activeCampaignService->getTags();
                $lists = $activeCampaignService->getLists();
            } else {
                $tags  = null;
                $lists = null;
            }

            return response()->json(['tags' => $tags, 'lists' => $lists, 'events' => $events], 200);
        }
        catch(Exception $e){
            // dd($e);
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    private function getEventsName(array $arrayEvents)
    {
        $events = [];
        foreach ($arrayEvents as $key => $value) {
            switch ($value) {
                case 1:
                    $events[] = ['name' => 'Boleto gerado', 'id' => 1];
                    break;
                case 2:
                    $events[] = ['name' => 'Boleto pago', 'id' => 2];
                    break;
                case 3:
                    $events[] = ['name' => 'Cartão de crédito pago', 'id' => 3];
                    break;
                case 4:
                    $events[] = ['name' => 'Carrinho abandonado', 'id' => 4];
                    break;
                case 5:
                    $events[] = ['name' => 'Cartão de crédito recusado', 'id' => 5];
                    break;
            }
        }

        return $events;
    }
}
