<?php

namespace Modules\Integrations\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

/**
 * Class IntegrationsController
 * @package Modules\Integrations\Http\Controllers
 */
class IntegrationsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('integrations::index');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    //    public function show($id)
    //    {
    //        return view('integrations::show');
    //    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    //    public function store(Request $request)
    //    {
    //        //
    //    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    //    public function edit($id)
    //    {
    //        return view('integrations::edit');
    //    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    //    public function create()
    //    {
    //        return view('integrations::create');
    //    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    //    public function update(Request $request, $id)
    //    {
    //        //
    //    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    //    public function destroy($id)
    //    {
    //        //
    //    }
}
