<?php

namespace Modules\Affiliates\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Project;
use Vinkla\Hashids\Facades\Hashids;

class AffiliatesController extends Controller
{
    /**
     * @param $projectId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function index($projectId)
    {
        $projectModel = new Project();
        $projectId    = current(Hashids::decode($projectId));
        $project      = $projectModel->where('id', $projectId)->where('status','!=', $projectModel->present()->getStatus('disabled'))->first();
        if ($project) {
            return view('affiliates::index');
        } else {
            return view('errors.404');
        }
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
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('affiliates::show');
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

    public function projectAffiliates()
    {
        return view('affiliates::projectaffiliates');
    }
}
