<?php

namespace Modules\Affiliates\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Project;
use Modules\Projects\Transformers\ProjectsResource;
use Vinkla\Hashids\Facades\Hashids;

class AffiliatesApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('affiliates::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('affiliates::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        dd('store');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $projectModel = new Project();
        $projectId    = current(Hashids::decode($id));
        if ($projectId) {
            $project = $projectModel->find($projectId);

            return new ProjectsResource($project);
        }

        return response()->json([
                                    'message' => 'Projeto não encontrado',
                                ], 400);
        //        return view('affiliates::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('affiliates::edit');
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
