<?php

namespace Modules\Webhooks\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class WebhooksController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view("webhooks::index");
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    // public function create()
    // {
    //     return view("webhooks::create");
    // }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    // public function show($id)
    // {
    //     return view("webhooks::show");
    // }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    // public function edit($id)
    // {
    //     return view("webhooks::edit");
    // }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    // public function update(Request $request, $id)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    // public function destroy($id)
    // {
    //     //
    // }
}
