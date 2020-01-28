<?php

namespace Modules\Customer\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Customers\Transformers\CustomerResource;
use Modules\Core\Entities\Customer;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ClientApiController
 * @package Modules\Customers\Http\Controllers
 */
class CustomersApiController extends Controller
{
    /**
     * Show the specified resource.
     * @param int $id
     * @return JsonResponse|CustomerResource
     */
    public function show($id)
    {
        try {

            if (!empty($id)) {

                $customerModel = new Customer();

                $customer = $customerModel->find(current(Hashids::decode($id)));

                if (!empty($customer)) {
                    return new CustomerResource($customer);
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
                } else if ($data['name'] == 'client-email') {
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
