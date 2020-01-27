<?php

namespace Modules\Clients\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Clients\Transformers\ClientResource;
use Modules\Core\Entities\Customer;
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
     * @return JsonResponse|ClientResource
     */
    public function show($id)
    {
        try {

            if (!empty($id)) {

                $clientModel = new Customer();

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

    public function update(Request $request)
    {
        try {

            $clientModel = new Customer();

            $data = $request->all();

            $id = current(Hashids::decode($data['id'] ?? ''));

            if (!empty($id && !empty($data['name']) && !empty($data['value']))) {

                if ($data['name'] == 'client-telephone') {
                    $column = 'telephone';
                } elseif ($data['name'] == 'client-email') {
                    $column = 'email';
                } else {
                    return response()->json([
                        'message' => 'Os dados informados são inválidos',
                    ], 400);
                }

                $client = $clientModel->find($id);

                if (!empty($client)) {
                    $client->$column = $data['value'];
                    $client->save();
                    return response()->json(['message' => 'Dados do cliente alterados com sucesso!']);
                } else {
                    return response()->json(['message' => 'Cliente não encontrado!'], 400);
                }

            } else {
                return response()->json(['message' => 'Os dados informados são inválidos!'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao atualizar cliente, (ClientApiController - update)');
            report($e);

            return response()->json(['message' => 'Erro ao alterar dados do cliente!'], 400);
        }
    }
}
