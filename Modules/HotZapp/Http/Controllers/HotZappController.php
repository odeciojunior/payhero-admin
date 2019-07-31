<?php

namespace Modules\HotZapp\Http\Controllers;

use App\Entities\Company;
use App\Entities\HotzappIntegration;
use App\Entities\UserProject;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class HotZappController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $companyModel            = new Company();
        $hotzappIntegrationModel = new HotzappIntegration();
        $UserProjectModel        = new UserProject();
        $companies               = $companyModel->where('user_id', auth()->user()->id)
                                                ->get();
        $hotzappIntegrations     = $hotzappIntegrationModel->where('user_id', auth()->user()->id)->get();
        $projects                = [];

        foreach ($hotzappIntegrations as $hotzappIntegration) {
            $userProjects = $UserProjectModel->where('user', $hotzappIntegration->user_id)->with('projectId')->get();
            if ($userProjects->count() > 0) {
                foreach ($userProjects as $userProject) {
                    $projects[] = $userProject->projectId;
                }
            }
        }

        return view('hotzapp::index', ['companies' => $companies, 'projects' => $projects]);
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
        $data                    = $request->all();
        $hotzappIntegrationModel = new HotzappIntegration();
        $Integration             = $hotzappIntegrationModel->where('link', $data['link'])->first();

        if ($Integration) {
            return response()->json([
                                        'message' => 'Projeto já integrado',
                                    ], 400);
        }

        $integrationCreated = $hotzappIntegrationModel->create([
                                                                   'company'     => $data['company'],
                                                                   'user_id'     => auth()->user()->id,
                                                                   'description' => $data['description'],
                                                                   'link'        => $data['link'],
                                                               ]);
        if ($integrationCreated) {
            return response()->json([
                                        'message' => 'Integração criada com sucesso!',
                                    ], 200);
        }

        return response()->json([
                                    'message' => 'Ocorreu um erro ao realizar a integração',
                                ], 400);
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
