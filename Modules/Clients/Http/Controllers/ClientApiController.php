<?php

namespace Modules\Clients\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Clients\Transformers\ClientResource;
use Modules\Core\Entities\Client;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ClientApiController
 * @package Modules\Clients\Http\Controllers
 */
class ClientApiController extends Controller
{
    /**
     * Show the specified resource.
     * @param int $id
     * @return ClientResource
     */
    public function show($id)
    {
        try {

            if (!empty($id)) {

                $clientModel = new Client();

                $client = $clientModel->find(current(Hashids::decode($id)));

                if (!empty($client)) {
                    return new ClientResource($client);
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro, cliente não encontrado',
                                            ], 400);
                }
            } else {
                // Hash invalido
                return response()->json([
                                            'message' => 'Ocorreu um erro, cliente não encontrado',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar cliente, (ClientApiController - show)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, cliente não encontrado',
                                    ], 400);
        }
    }
}
