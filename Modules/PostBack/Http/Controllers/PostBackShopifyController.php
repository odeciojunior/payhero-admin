<?php

namespace Modules\PostBack\Http\Controllers;

use App\Entities\Plan;
use App\Entities\Product;
use App\Entities\Project;
use Illuminate\Http\Request;
use App\Entities\ProductPlan;
use App\Entities\UserProject;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class PostBackShopifyController extends Controller
{
    public function postBackListener(Request $request)
    {
        $dados = $request->all();

        $project = Project::find(Hashids::decode($request->project_id)[0]);

        if (!$project) {
            Log::write('info', 'projeto não encontrado no retorno do shopify, projeto = ' . $project->id);

            return 'error';
        } else {
            Log::write('info', 'retorno do shopify (webhook products) referente ao projeto ' . $project->id);
            Log::write('info', 'dados : ' . print_r($dados, true) );
        }

        foreach ($dados['variants'] as $variant) {

            $plan = Plan::where([
                                    ['shopify_variant_id', $variant['id']],
                                    ['project', $project->id],
                                ])->first();

            $description = '';
            try {
                $description = $variant['option1'];
                if ($description == 'Default Title') {
                    $description = '';
                }
                if ($variant['option2'] != '') {
                    $description .= ' - ' . $$variant['option2'];
                }
                if ($variant['option3'] != '') {
                    $description .= ' - ' . $$variant['option3'];
                }
            } catch (\Exception $e) {
                //report($e);
            }

            if ($plan) {
                $plan->update([
                                  'name'        => substr($dados['title'], 0, 100),
                                  'price'       => $variant['price'],
                                  'description' => $description,
                                  'code'        => Hashids::encode($plan->id),
                              ]);
            } else {
                $userProject = UserProject::where([
                                                      ['project', $project['id']],
                                                      ['type', 'producer'],
                                                  ])->first();

                $product = Product::create([
                                               'user'        => $userProject->user,
                                               'name'        => substr($dados['title'], 0, 100),
                                               'description' => $description,
                                               'guarantee'   => '0',
                                               'available'   => true,
                                               'amount'      => '0',
                                               'format'      => 1,
                                               'category'    => 11,
                                               'cost'        => '',
                                               'shopify'     => '1'
                                           ]);

                $plan = Plan::create([
                                         'shopify_id'                 => $dados['id'],
                                         'shopify_variant_id'         => $variant['id'],
                                         'project'                    => $project['id'],
                                         'name'                       => substr($dados['title'], 0, 100),
                                         'description'                => $description,
                                         'price'                      => $variant['price'],
                                         'status'                     => '1',
                                     ]);
                $plan->update([
                    'code' => Hashids::encode($plan->id)
                ]);

                if (count($dados['variants']) > 1) {
                    foreach ($dados['images'] as $image) {

                        foreach ($image['variant_ids'] as $variantId) {
                            if ($variantId == $variant['id']) {

                                try{
                                    if ($image['src'] != '') {
                                        $product->update([
                                                            'photo' => $image['src'],
                                                        ]);
                                    } else {
                                        $product->update([
                                                            'photo' => $dados['image']['src'],
                                                        ]);
                                    }
                                } catch (\Exception $e) {
                                    Log::warning('erro ao adicionar imagem ao produto no webhook do shopify');
                                    report($e);
                                }
                            }
                        }
                    }
                } else {
                    try{
                        $product->update([
                                            'photo' => $dados['image']['src'],
                                        ]);
                    }
                    catch (\Exception $e) {
                        Log::warning('erro ao adicionar imagem ao produto no webhook do shopify');
                        report($e);
                    }
                }

                ProductPlan::create([
                                        'product' => $product->id,
                                        'plan'    => $plan->id,
                                        'amount'  => '1',
                                    ]);
            }
        }

        return 'success';
    }

}
