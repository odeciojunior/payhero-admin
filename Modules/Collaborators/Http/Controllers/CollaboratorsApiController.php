<?php

namespace Modules\Collaborators\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Collaborators\Transformers\CollaboratorsResource;
use Modules\Core\Entities\User;

class CollaboratorsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        try {

            $userModel = new User();
            $user      = $userModel->where([['account_owner', auth()->user()->id], ['id', '!=', auth()->user()->id]]);

            return CollaboratorsResource::collection($user->orderBy('id', 'DESC')->paginate(10));
        } catch (Exception $e) {
            Log::warning('Erro ao tentar listar colaboradores (CollaboratorsApiController - index)');
            report($e);

            return response()->json(
                [
                    'message' => 'Erro ao tentar listar colaboradores ',
                ], 400
            );
        }
    }

    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('collaborators::show');
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
