<?php

namespace Modules\ProjectUpsellConfig\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ProjectUpsellConfig;
use Modules\Core\Entities\ProjectUpsellRule;
use Modules\Core\Services\InstallmentsService;
use Modules\ProjectUpsellConfig\Transformers\PreviewUpsellResource;
use Modules\ProjectUpsellConfig\Transformers\ProjectUpsellConfigResource;
use Vinkla\Hashids\Facades\Hashids;

class ProjectUpsellConfigApiController extends Controller
{
    /**
     * @param $projectId
     * @return JsonResponse|ProjectUpsellConfigResource
     */
    public function show($projectId)
    {
        $projectId = current(Hashids::decode($projectId));
        $projectUpsellConfig = new ProjectUpsellConfig();
        if ($projectId) {
            $upsellConfig = $projectUpsellConfig->where('project_id', $projectId)->first();

            return new ProjectUpsellConfigResource($upsellConfig);
        } else {
            return response()->json([
                'message' => 'Projeto não encontrado',
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $projectId
     * @return JsonResponse|Response
     */
    public function update(Request $request, $projectId)
    {
        $projectId = current(Hashids::decode($projectId));
        $projectUpsellConfig = new ProjectUpsellConfig();
        $data = $request->all();
        if ($projectId) {
            $upsellConfig = $projectUpsellConfig->where('project_id', $projectId)->first();

            $upsellConfigUpdated = $upsellConfig->update([
                'header' => $data['header'],
                'title' => $data['title'],
                'description' => $data['description'],
                'countdown_time' => $data['countdown_time'],
                'countdown_flag' => !empty($data['countdown_flag']) ? true : false,
            ]);
            if ($upsellConfigUpdated) {
                return response()->json(['message' => 'Configuração do upsell atualizado com sucesso!'], 200);
            } else {
                return response()->json([
                    'message' => 'Erro ao atualizar configurações do upsell',
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'Erro ao atualizar configurações do upsell',
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|PreviewUpsellResource
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function previewUpsell(Request $request)
    {
        try {
            $projectModel = new Project();
            $projectUpsellConfigModel = new ProjectUpsellConfig();
            $projectUpsellRuleModel = new ProjectUpsellRule();
            $planModel = new Plan();

            $data = $request->all();
            $projectId = current(Hashids::decode($data['project_id']));

            if ($projectId) {
                $project = $projectModel->find($projectId);
                $upsellConfig = $projectUpsellConfigModel->where('project_id', $projectId)->first();
                $upsellRule = $projectUpsellRuleModel->where('project_id', $projectId)->first();

                $offerOnPlans = json_decode($upsellRule->offer_on_plans);

                $upsellPlansArray = [];

                $upsellPlans = $planModel::with(['productsPlans.product'])
                    ->whereIn('id', $offerOnPlans)
                    ->get();

                foreach ($upsellPlans as $plan) {

                    $productArray = [];

                    $productsPlans = $plan->productsPlans;

                    foreach ($productsPlans as $productPlan) {
                        //se o plano tiver um unico produto, oferecer variantes
                        if ($productsPlans->count() == 1 && $productPlan->product->variants->count()) {
                            $products = $productPlan->product->variants;
                            foreach ($products as $product) {
                                $variant = (object)[
                                    'name' => $product->name,
                                    'description' => $product->description,
                                    'amount' => $productPlan->amount,
                                    'photo' => $product->photo
                                ];
                                if ($product->shopify_variant_id) {
                                    $productArray[$product->shopify_variant_id] = $variant;
                                } else {
                                    $productArray[] = $variant;
                                }
                            }
                        } else {
                            $product = $productPlan->product;
                            $productArray = [
                                (object)[
                                    'name' => $product->name,
                                    'description' => $product->description,
                                    'amount' => $productPlan->amount,
                                    'photo' => $product->photo
                                ]
                            ];
                        }

                        if (isset($upsellPlansArray[$plan->id])) {
                            $upsellPlansArray[$plan->id]->products[] = $productArray;
                        } else {

                            //discount
                            $originalPrice = preg_replace("/[^0-9]/", "", $plan->price);
                            $discount = is_numeric($upsellRule->discount) && $upsellRule->discount <= 100 ? $upsellRule->discount : 0;
                            if ($discount) {
                                $price = $originalPrice - intval($originalPrice * $discount / 100);
                            } else {
                                $price = $originalPrice;
                            }

                            $installments = array_reverse(InstallmentsService::getInstallments($project, $price) ?? []);

                            $upsellPlansArray[$plan->id] = (object)[
                                'rule' => Hashids::encode($upsellRule->id),
                                'name' => $plan->name,
                                'code' => $plan->code,
                                'price' => number_format($price / 100, 2, ',', '.'),
                                'original_price' => number_format($originalPrice / 100, 2, ',', '.'),
                                'discount' => $discount,
                                'installments' => $installments,
                                'products' => [$productArray],
                            ];
                        }
                    }
                }

                $upsellConfig->plans = array_values($upsellPlansArray);

                return new PreviewUpsellResource($upsellConfig);
            } else {
                return response()->json([
                    'message' => 'Projeto não encontrado',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao exibir visualização',
            ], 400);
        }
    }
}
