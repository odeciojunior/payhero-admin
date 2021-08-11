<?php

namespace Modules\Customers\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Customer;
use Modules\Customers\Transformers\CustomerResource;
use Vinkla\Hashids\Facades\Hashids;

class CustomersApiController extends Controller
{

    public function show($id)
    {
        try {
            if (empty($id)) {
                return response()->json([
                    'message' => 'Ocorreu um erro, cliente não encontrado',
                ], 400);
            }

            $customer = Customer::find(hashids_decode($id));

            if (!empty($customer)) {
                return new CustomerResource($customer);
            }

            return response()->json([
                'message' => 'Ocorreu um erro, cliente não encontrado',
            ], 400);
        } catch (Exception $e) {
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
                } else {
                    if ($data['name'] == 'client-email') {
                        $column = 'email';
                    } else {
                        return response()->json([
                            'message' => 'Os dados informados são inválidos',
                        ], 400);
                    }
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
