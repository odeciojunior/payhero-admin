<?php

namespace Modules\HotZapp\Http\Controllers;

use App\Entities\HotZappIntegration;
use App\Entities\Project;
use App\Entities\UserProject;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

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
            $Integration             = $hotzappIntegrationModel->where('link', $data['link'])->first();
            if ($Integration) {
                return response()->json([
                                            'message' => 'Projeto já integrado',
                                        ], 400);
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
        } catch (Exception $e) {
            dd($e);
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
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('hotzapp::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
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
