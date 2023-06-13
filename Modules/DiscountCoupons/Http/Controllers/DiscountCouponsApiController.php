<?php

namespace Modules\DiscountCoupons\Http\Controllers;

use Exception;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\DiscountCoupon;
use Modules\Core\Entities\Project;
use Modules\DiscountCoupons\Http\Requests\DiscountCouponsStoreRequest;
use Modules\DiscountCoupons\Http\Requests\DiscountCouponsUpdateRequest;
use Modules\DiscountCoupons\Transformers\DiscountCouponsResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Request;
use Modules\Core\Entities\Plan;

/**
 * Class DiscountCouponsApiController
 * @package Modules\DiscountCoupons\Http\Controllers
 */
class DiscountCouponsApiController extends Controller
{
    /**
     * @param $projectId
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request, $projectId)
    {
        try {
            $discountCouponsModel = new DiscountCoupon();
            $projectModel = new Project();

            if (isset($projectId)) {
                $project = $projectModel->find(current(Hashids::decode($projectId)));

                activity()
                    ->on($discountCouponsModel)
                    ->tap(function (Activity $activity) {
                        $activity->log_name = "visualization";
                    })
                    ->log("Visualizou tela todos os cupons para o projeto " . $project->name);

                if (Gate::allows("edit", [$project])) {
                    $projectId = $project->id;
                    $coupons = $discountCouponsModel->whereHas("project", function ($query) use ($projectId) {
                        $query->where("project_id", $projectId);
                    })->where("recovery_flag", false);

                    if (!empty($request["name"])) {
                        $coupons = $coupons
                            ->where("name", "like", "%" . $request["name"] . "%")
                            ->whereOr("code", "like", "%" . $request["name"] . "%");
                    }

                    return DiscountCouponsResource::collection($coupons->orderBy("id", "DESC")->paginate(5));
                } else {
                    return response()->json(
                        [
                            "message" => "Sem permissão para acessar os coupon",
                        ],
                        400
                    );
                }
            } else {
                return response()->json(
                    [
                        "message" => "Erro ao listar dados de cupons",
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            Log::warning("Erro ao buscar cupons (DiscountCouponsController - index)");
            report($e);

            return response()->json(
                [
                    "message" => "Erro ao listar dados de cupons",
                ],
                400
            );
        }
    }

    /**
     * @param DiscountCouponsStoreRequest $request
     * @param $projectId
     * @return JsonResponse
     */
    public function store(DiscountCouponsStoreRequest $request, $projectId)
    {
        try {
            dd("aqui");
            if (isset($projectId)) {
                $requestData = $request->validated();

                dd($requestData);

                $requestData["project_id"] = current(Hashids::decode($projectId));
                $requestData["value"] = preg_replace("/[^0-9]/", "", $requestData["value"]);
                $requestData["rule_value"] = preg_replace("/[^0-9]/", "", $requestData["rule_value"]);

                if (empty($requestData["rule_value"])) {
                    $requestData["rule_value"] = 0;
                }

                if ($requestData["value"] == 0) {
                    return response()->json(
                        [
                            "message" => "O valor do cupom deve ser maior do que zero.",
                        ],
                        \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST
                    );
                }

                $project = Project::find($requestData["project_id"]);

                if (!Gate::allows("edit", [$project])) {
                    return response()->json(
                        [
                            "message" => "Sem permissão para criar cupom neste projeto",
                        ],
                        \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST
                    );
                }

                if (empty($requestData["code"])) {
                    $requestData["code"] = "";
                } else {
                    //Check if coupon exists
                    $result = DiscountCoupon::where("project_id", $requestData["project_id"])
                        ->where("status", 1)
                        ->where("code", $requestData["code"])
                        ->get();

                    if (!empty($result)) {
                        foreach ($result as $couponsData) {
                            if (empty($couponsData->expires) || (!empty($couponsData->expires) && strtotime($couponsData->expires) >= time())) {
                                return response()->json(
                                    [
                                        "message" => 'Já existe um cupom de código "' . $request["code"] . '"!',
                                    ],
                                    \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST
                                );
                            }
                        }
                    }
                }

                if (empty($requestData["status"])) {
                    $requestData["status"] = 0;
                }

                if ($request["expires"]) {
                    $date_array = explode("/", $requestData["expires"]);
                    $date = date(
                        "Y-m-d H:i:s",
                        strtotime($date_array[2] . "-" . $date_array[1] . "-" . $date_array[0])
                    );

                    $requestData["expires"] = $date;
                }

                if ($request["nao_vence"]) {
                    $requestData["expires"] = null;
                }

                DiscountCoupon::create($requestData);

                return response()->json(
                    [
                        "message" => "Cupom criado com sucesso!",
                    ],
                    \Symfony\Component\HttpFoundation\Response::HTTP_OK
                );
            }

            return response()->json(
                [
                    "message" => "Erro ao tentar salvar cupom!",
                ],
                \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST
            );
        } catch (Exception $e) {
            Log::warning("Erro ao tentar cadastrar novo cupom de desconto (DiscountCouponsController - store)");
            report($e);

            return response()->json(
                [
                    "message" => "Erro ao tentar salvar cupom!",
                ],
                \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @param $projectId
     * @param $id
     * @return JsonResponse|DiscountCouponsResource
     */
    public function show($projectId, $id)
    {
        try {
            if (isset($projectId) && isset($id)) {
                $discountCouponsModel = new DiscountCoupon();
                $projectModel = new Project();

                $coupon = $discountCouponsModel->find(current(Hashids::decode($id)));
                $project = $projectModel->find(current(Hashids::decode($projectId)));

                activity()
                    ->on($projectModel)
                    ->tap(function (Activity $activity) use ($coupon) {
                        $activity->log_name = "visualization";
                        $activity->subject_id = $coupon->id;
                    })
                    ->log("Visualizou tela detalhes do cupon " . $coupon->name);

                if (Gate::allows("edit", [$project])) {
                    if ($coupon) {
                        return new DiscountCouponsResource($coupon);
                    } else {
                        return response()->json(["message" => "Erro ao buscar Cupom"], 400);
                    }
                } else {
                    return response()->json(
                        [
                            "message" => "Sem permissão para visualizar este cupom",
                        ],
                        400
                    );
                }
            }

            return response()->json(["message" => "Erro ao buscar Cupom"], 400);
        } catch (Exception $e) {
            Log::warning("Erro ao tentar buscar dados de um cupom (DiscountCouponsController - show)");
            report($e);

            return response()->json(["message" => "Erro ao buscar Cupom"], 400);
        }
    }

    /**
     * @param $projectId
     * @param $id
     * @return JsonResponse
     */
    public function edit($projectId, $id)
    {
        try {
            if (isset($projectId) && isset($id)) {
                $discountCouponsModel = new DiscountCoupon();
                $projectModel = new Project();

                $coupon = $discountCouponsModel->find(current(Hashids::decode($id)));
                $project = $projectModel->find(current(Hashids::decode($projectId)));

                activity()
                    ->on($projectModel)
                    ->tap(function (Activity $activity) use ($coupon) {
                        $activity->log_name = "visualization";
                        $activity->subject_id = $coupon->id;
                    })
                    ->log("Visualizou tela editar configuração do cupom " . $coupon->name);

                if (Gate::allows("edit", [$project])) {
                    if ($coupon) {
                        $coupon->makeHidden(["id", "project_id"]);

                        $coupon->rule_value = number_format($coupon->rule_value / 100, 2, ",", ".");
                        if ($coupon->type == 1) {
                            $coupon->value = number_format($coupon->value / 100, 2, ",", ".");
                        }

                        $expires = "";
                        if (!empty($coupon->expires)) {
                            $coupon->expires_date = date("d/m/Y", strtotime($coupon->expires));
                            $now = strtotime(date("Y-m-d"));
                            $_date = strtotime($coupon->expires);

                            $datediff = $_date - $now;
                            $expires = round($datediff / (60 * 60 * 24));
                            $coupon->expires_days = $expires;
                            if ($expires >= 0) {
                                $coupon->expires = "Vence em " . $expires . " dia" . ($expires > 1 ? "s" : "");
                            } else {
                                $coupon->expires = "";
                                $coupon->status = 0;
                            }
                        }

                        if (empty($coupon->plans)) {
                            $coupon->plans = "[]";
                        }

                        return response()->json($coupon, 200);
                    } else {
                        return response()->json(["message" => "Erro ao atualizar registro"], 400);
                    }
                } else {
                    return response()->json(
                        [
                            "message" => "Sem permissão para editar este registro",
                        ],
                        400
                    );
                }
            }

            return response()->json(["message" => "Erro ao buscar Cupom"], 400);
        } catch (Exception $e) {
            Log::warning("Erro ao tentar buscar dados para atualizar  (DescountCouponsController - edit)");
            report($e);

            return response()->json(["message" => "Erro ao atualizar registro"], 400);
        }
    }

    public function update(DiscountCouponsUpdateRequest $request, $projectId, $id)
    {
        try {
            if (isset($projectId) && isset($id)) {
                $requestValidated = $request->validated();

                $coupon = DiscountCoupon::find(current(Hashids::decode($id)));
                $project = Project::find(current(Hashids::decode($projectId)));

                if (!empty($requestValidated["value"])) {
                    $requestValidated["value"] = preg_replace("/[^0-9]/", "", $requestValidated["value"]);
                }
                if (!empty($requestValidated["rule_value"])) {
                    $requestValidated["rule_value"] = preg_replace("/[^0-9]/", "", $requestValidated["rule_value"]);
                }

                if (!Gate::allows("edit", [$project])) {
                    return response()->json(
                        [
                            "message" => "Sem permissão para atualizar este cupom",
                        ],
                        \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST
                    );
                }

                if ($request["set_status"] == 1) {
                    if ($request["status"] == 1) {
                        $requestValidated["status"] = 1;

                        if (!empty($request["code"])) {
                            $result = DiscountCoupon::where("project_id", current(Hashids::decode($projectId)))
                                ->where("id", "!=", $coupon->id)
                                ->where("status", 1)
                                ->where("code", $request["code"])
                                ->get();

                            if (!empty($result)) {
                                foreach ($result as $couponsData) {
                                    if (
                                        empty($couponsData->expires) ||
                                        (!empty($couponsData->expires) && strtotime($couponsData->expires) >= time())
                                    ) {
                                        return response()->json(
                                            [
                                                "message" => 'Já existe um cupom de código "' . $request["code"] . '"!',
                                            ],
                                            \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST
                                        );
                                    }
                                }
                            }
                        }
                    } else {
                        $requestValidated["status"] = 0;
                    }
                } else {
                    unset($requestValidated["status"]);
                }

                if ($request["expires"]) {
                    $date_array = explode("/", $requestValidated["expires"]);
                    $date = date(
                        "Y-m-d H:i:s",
                        strtotime($date_array[2] . "-" . $date_array[1] . "-" . $date_array[0])
                    );

                    $requestValidated["expires"] = $date;
                }
                if ($request["nao_vence"]) {
                    $requestValidated["expires"] = null;
                }
                if (empty($requestValidated["plans"])) {
                    unset($requestValidated["plans"]);
                }
                if (empty($requestValidated["progressive_rules"])) {
                    unset($requestValidated["progressive_rules"]);
                }
                if (empty($requestValidated["name"])) {
                    unset($requestValidated["name"]);
                }

                $coupon->update($requestValidated);

                return response()->json(
                    [
                        "message" => "Registro atualizado com sucesso",
                    ],
                    \Symfony\Component\HttpFoundation\Response::HTTP_OK
                );
            }

            return response()->json(
                [
                    "message" => "Erro ao atualizar registro",
                ],
                \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST
            );
        } catch (Exception $e) {
            Log::warning("Erro ao tentar atualizar desconto  (DescountCouponController - update)");
            report($e);

            return response()->json(["message" => "Erro ao atualizar registro"], 400);
        }
    }

    /**
     * @param $projectId
     * @param $id
     * @return JsonResponse
     */
    public function destroy($projectId, $id)
    {
        try {
            if (isset($id)) {
                $discountCouponsModel = new DiscountCoupon();
                $projectModel = new Project();

                $descountCoupon = $discountCouponsModel->find(current(Hashids::decode($id)));
                $project = $projectModel->find(current(Hashids::decode($projectId)));

                if (Gate::allows("edit", [$project])) {
                    $descountCoupon->delete();
                    if ($descountCoupon) {
                        return response()->json("Sucesso");
                    } else {
                        return response()->json("Erro");
                    }
                } else {
                    return response()->json(
                        [
                            "message" => "Sem permissão para remover este cupom",
                        ],
                        400
                    );
                }
            }

            return response()->json(["message" => "Erro ao excluir Cupom"], 400);
        } catch (Exception $e) {
            Log::warning("Erro ao tentar excluir cupom de desconto (DescountCouponController - destroy)");
            report($e);

            return response()->json(["message" => "Erro ao excluir Cupom"], 400);
        }
    }
}
