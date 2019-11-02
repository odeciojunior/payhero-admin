<?php


namespace Modules\Activecampaign\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\ActivecampaignIntegration;
use Modules\Core\Entities\ActivecampaignEvent;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ProjectService;
use Modules\ActiveCampaign\Transformers\ActivecampaignResource;
use Modules\ActiveCampaign\Transformers\ActivecampaignEventResource;
use Modules\Projects\Transformers\ProjectsSelectResource;
use Vinkla\Hashids\Facades\Hashids;

class ActiveCampaignEventApiController extends Controller
{

    /**
     * @return JsonResponse
     */
    public function index(){
        try{
            $request = Request::capture();
            $data = $request->all();

            $id = Hashids::decode($data['integration']);

            $activecampaignIntegrationModel = new ActivecampaignIntegration();

            $activecampaignIntegration = $activecampaignIntegrationModel->where('user_id', auth()->id())->with('events.product', 'events.plan')->where('id',$id)->first();

            $events = $activecampaignIntegration->events ?? collect([]);
            return ActivecampaignEventResource::collection($events);
        }
        catch(Exception $e){
            dd($e);
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * @param $id
     * @return ActivecampaignResource
     */
    public function show($id){

        // $activecampaignIntegrationModel = new ActivecampaignIntegration();
        // $activecampaignIntegration      = $activecampaignIntegrationModel->find(current(Hashids::decode($id)));

        // return new ActivecampaignResource($activecampaignIntegration);
        
        $activecampaignEventModel = new ActivecampaignEvent();
        $activecampaignEvent      = $activecampaignEventModel->with('product', 'plan')->find(current(Hashids::decode($id)));

        return new ActivecampaignEventResource($activecampaignEvent);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request) {

        try {
            $data                           = $request->all();
            $activecampaignIntegrationModel = new ActivecampaignIntegration();

            $projectId = current(Hashids::decode($data['project_id']));
            if (!empty($projectId)) {
                $integration = $activecampaignIntegrationModel->where('project_id', $projectId)->first();
                if ($integration) {
                    return response()->json([
                        'message' => 'Projeto já integrado',
                    ], 400);
                }
                if (empty($data['api_url'])) {
                    $data['api_url'] = 0;
                }
                if (empty($data['api_key'])) {
                    $data['api_key'] = 0;
                }

                $integrationCreated = $activecampaignIntegrationModel->create([
                    'api_url'    => $data['api_url'],
                    'api_key'    => $data['api_key'],
                    'project_id' => $projectId,
                    'user_id'    => auth()->user()->id,
                ]);

                if ($integrationCreated) {
                    return response()->json([
                        'message' => 'Integração criada com sucesso!'
                    ], 200);
                } else {

                    return response()->json([
                        'message' => 'Ocorreu um erro ao realizar a integração',
                    ], 400);
                }
            } else {

                return response()->json([
                    'message' => 'Ocorreu um erro ao realizar a integração',
                ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao realizar integração ActiveCampaignController - store');
            report($e);

            return response()->json([
                'message' => 'Ocorreu um erro ao realizar a integração',
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
                $activecampaignIntegrationModel = new ActivecampaignIntegration();
                $projectService                 = new ProjectService();

                $projects = $projectService->getMyProjects();

                $projectId   = current(Hashids::decode($id));
                $integration = $activecampaignIntegrationModel->where('project_id', $projectId)->first();

                if ($integration) {
                    return response()->json(['projects' => $projects, 'integration' => $integration]);
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
            Log::warning('Erro ao tentar acessar tela editar Integração ActiveCampaign (ActiveCampaignController - edit)');
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
        $activecampaignIntegrationModel = new ActivecampaignIntegration();
        $data                           = $request->all();
        $integrationId                  = current(Hashids::decode($id));
        $activecampaignIntegration      = $activecampaignIntegrationModel->find($integrationId);
        if (empty($data['api_url'])) {
            $data['api_url'] = 0;
        }
        if (empty($data['api_key'])) {
            $data['api_key'] = 0;
        }

        $integrationUpdated = $activecampaignIntegration->update([
            'api_url' => $data['api_url'],
            'api_key' => $data['api_key'],
        ]);
        if ($integrationUpdated) {
            return response()->json([
                'message' => 'Integração atualizada com sucesso!',
            ], 200);
        }

        return response()->json([
            'message' => 'Ocorreu um erro ao atualizar a integração',
        ], 400);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $integrationId                  = current(Hashids::decode($id));
            $activecampaignIntegrationModel = new ActivecampaignIntegration();
            $integration                    = $activecampaignIntegrationModel->find($integrationId);
            $integrationDeleted             = $integration->delete();
            if ($integrationDeleted) {
                return response()->json([
                    'message' => 'Integração Removida com sucesso!',
                ], 200);
            }

            return response()->json([
                'message' => 'Erro ao tentar remover Integração',
            ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar remover Integração ActiveCampaign (ActiveCampaignController - destroy)');
            report($e);

            return response()->json([
                'message' => 'Ocorreu um erro ao tentar remover, tente novamente mais tarde!',
            ], 400);
        }
    }
}
