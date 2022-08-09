<?php

namespace Modules\Customers\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Sale;
use Modules\Customers\Transformers\CustomerResource;
use Modules\Customers\Transformers\FraudsterCustomerResource;
use Vinkla\Hashids\Facades\Hashids;

class CustomersApiController extends Controller
{
    public function show($id, $saleId = null)
    {
        $sale = Sale::find(
            current(
                hashids()
                    ->connection("sale_id")
                    ->decode($saleId)
            )
        );

        try {
            if (empty($id)) {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro, cliente não encontrado",
                    ],
                    400
                );
            }
            $customer = Customer::find(hashids_decode($id));

            if (empty($customer)) {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro, cliente não encontrado",
                    ],
                    400
                );
            }
            if ($sale && in_array($sale->status, [Sale::STATUS_IN_REVIEW, Sale::STATUS_CANCELED_ANTIFRAUD])) {
                return new FraudsterCustomerResource($customer);
            }
            return new CustomerResource($customer);
        } catch (Exception $e) {
            Log::warning("Erro ao buscar cliente, (CustomersApiController - show)");
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro, cliente não encontrado",
                ],
                400
            );
        }
    }

    public function update(Request $request)
    {
        try {
            $clientModel = new Customer();

            $data = $request->all();

            $id = current(Hashids::decode($data["id"] ?? ""));

            if (!empty($id && !empty($data["name"]) && !empty($data["value"]))) {
                if ($data["name"] == "client-telephone") {
                    $column = "telephone";
                } else {
                    if ($data["name"] == "client-email") {
                        $column = "email";
                    } else {
                        return response()->json(
                            [
                                "message" => "Os dados informados são inválidos",
                            ],
                            400
                        );
                    }
                }

                $client = $clientModel->find($id);

                if (!empty($client)) {
                    $client->$column = $data["value"];
                    $client->save();

                    return response()->json(["message" => "Dados do cliente alterados com sucesso!"]);
                } else {
                    return response()->json(["message" => "Cliente não encontrado!"], 400);
                }
            } else {
                return response()->json(["message" => "Os dados informados são inválidos!"], 400);
            }
        } catch (Exception $e) {
            Log::warning("Erro ao atualizar cliente, (CustomersApiController - update)");
            report($e);

            return response()->json(["message" => "Erro ao alterar dados do cliente!"], 400);
        }
    }
}
