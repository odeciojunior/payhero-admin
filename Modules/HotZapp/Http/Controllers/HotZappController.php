<?php

namespace Modules\HotZapp\Http\Controllers;

use App\Entities\HotZappIntegration;
use App\Entities\Project;
use App\Entities\UserProject;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class HotZappController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $hotzappIntegrationModel = new HotzappIntegration();
        $projectModel            = new Project();
        $userProjectModel        = new UserProject();

        $projects            = [];
        $projectsIntegrated  = [];
        $userProjects        = $userProjectModel->where('user', auth()->user()->id)->with('projectId')->get();
        $hotzappIntegrations = $hotzappIntegrationModel->where('user_id', auth()->user()->id)->get();
        foreach ($userProjects as $userProject) {
            $projects[] = $userProject->projectId;
        }

        foreach ($hotzappIntegrations as $hotzappIntegration) {
            $project = $projectModel->find($hotzappIntegration->project_id);
            if ($project) {
                $projectsIntegrated[] = $project;
            }
        }

        return view('hotzapp::index', ['projects' => $projects, 'projectsIntegrated' => $projectsIntegrated]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('hotzapp::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $data                    = $request->all();
            $hotzappIntegrationModel = new HotzappIntegration();
            $integration             = $hotzappIntegrationModel->where('user_id', auth()->user()->id)->first();
            if ($integration) {
                return response()->json([
                                            'message' => 'Projeto já integrado',
                                        ], 400);
            }
            if (empty($data['boleto_generated'])) {
                $data['boleto_generated'] = 0;
            }
            if (empty($data['boleto_paid'])) {
                $data['boleto_paid'] = 0;
            }
            if (empty($data['credit_card_paid'])) {

                $data['credit_card_paid'] = 0;
            }
            if (empty($data['credit_card_refused'])) {

                $data['credit_card_refused'] = 0;
            }

            $integrationCreated = $hotzappIntegrationModel->create([
                                                                       'link'                => $data['link'],
                                                                       'boleto_generated'    => $data['boleto_generated'],
                                                                       'boleto_paid'         => $data['boleto_paid'],
                                                                       'credit_card_refused' => $data['credit_card_refused'],
                                                                       'credit_card_paid'    => $data['credit_card_paid'],
                                                                       'project_id'          => $data['project_id'],
                                                                       'user_id'             => auth()->user()->id,
                                                                   ]);
            if ($integrationCreated) {
                return response()->json([
                                            'message' => 'Integração criada com sucesso!',
                                        ], 200);
            }

            return response()->json([
                                        'message' => 'Ocorreu um erro ao realizar a integração',
                                    ], 400);
        } catch
        (Exception $e) {
            Log::warning('Erro ao realizar integração  HotZappController - store');
            report($e);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('hotzapp::show');
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        try {
            if (isset($id)) {
                $hotzappIntegrationModel = new HotzappIntegration();
                $userProjectModel        = new UserProject();
                $projects                = [];

                $projectId    = current(Hashids::decode($id));
                $integration  = $hotzappIntegrationModel->where('project_id', $projectId)->first();
                $userProjects = $userProjectModel->where('user', auth()->user()->id)->with('projectId')->get();
                foreach ($userProjects as $userProject) {
                    $projects[] = $userProject->projectId;
                }

                if ($integration) {
                    return view('hotzapp::edit', ['projects' => $projects, 'integration' => $integration]);
                }
            }

            return response()->json([
                                        'message' => 'Erro',
                                    ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar Integração HotZapp (HotZappController - edit)');
            report($e);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $hotzappIntegrationModel = new HotzappIntegration();
        $data                    = $request->all();
        $integrationId           = current(Hashids::decode($id));
        $hotzappIntegration      = $hotzappIntegrationModel->find($integrationId);
        if (empty($data['boleto_generated'])) {
            $data['boleto_generated'] = 0;
        }
        if (empty($data['boleto_paid'])) {
            $data['boleto_paid'] = 0;
        }
        if (empty($data['credit_card_paid'])) {

            $data['credit_card_paid'] = 0;
        }
        if (empty($data['credit_card_refused'])) {

            $data['credit_card_refused'] = 0;
        }
        $integrationUpdated = $hotzappIntegration->update([
                                                              'link'                => $data['link'],
                                                              'boleto_generated'    => $data['boleto_generated'],
                                                              'boleto_paid'         => $data['boleto_paid'],
                                                              'credit_card_refused' => $data['credit_card_refused'],
                                                              'credit_card_paid'    => $data['credit_card_paid'],
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
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
