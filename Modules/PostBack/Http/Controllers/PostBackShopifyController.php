<?php

namespace Modules\PostBack\Http\Controllers;

use Hashids\Hashids;
use App\Entities\Plan;
use App\Entities\Product;
use App\Entities\Project;
use Illuminate\Http\Request;
use App\Entities\ProductPlan;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class PostBackShopifyController extends Controller
{
    public function postBackListener(Request $request)
    {
        $dados = $request->all();
        Log::write('info', 'retorno do shopify ' . print_r($dados, true) );
        // return 'success';

        $project = Project::find(Hashids::decode($request->id_projeto)->first());

        if (!$project) {
            Log::write('info', 'projeto não encontrado no retorno do shopify, projeto = ' . $project->id);

            return 'error';
        } else {
            Log::write('info', 'retorno do shopify, projeto = ' . $project->id);
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
                report($e);
            }

            if ($plan) {
                $plan->update([
                                  'name'        => substr($dados['title'], 0, 100),
                                  'price'       => $variant['price'],
                                  'description' => $description,
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
                                           ]);

                $newCode = false;

                while ($newCode == false) {
                    $code = $this->randString(3) . rand(100, 999);
                    $plan = Plan::where('code', $code)->first();
                    if ($plan == null) {
                        $newCode = true;
                    }
                }

                $plan = Plan::create([
                                         'shopify_id'                 => $dados['id'],
                                         'shopify_variant_id'         => $variant['id'],
                                         'company'                    => $userProject->company,
                                         'project'                    => $project['id'],
                                         'name'                       => substr($dados['title'], 0, 100),
                                         'description'                => $description,
                                         'code'                       => $code,
                                         'price'                      => $variant['price'],
                                         'status'                     => '1',
                                         'carrier'                    => '2',
                                         'installments_amount'        => '12',
                                         'installments_interest_free' => '1',
                                     ]);

                if (count($dados['variants']) > 1) {
                    foreach ($dados['images'] as $image) {

                        foreach ($image['variant_ids'] as $variantId) {
                            if ($variantId == $variant['id']) {

                                if ($image['src'] != '') {
                                    $product->update([
                                                         'photo' => $image->getSrc(),
                                                     ]);

                                    $plan->update([
                                                      'photo' => $image->getSrc(),
                                                  ]);
                                } else {
                                    $plan->update([
                                                      'photo' => $dados['image']['src'],
                                                  ]);
                                    $product->update([
                                                         'photo' => $dados['image']['src'],
                                                     ]);
                                }
                            }
                        }
                    }
                } else {
                    $plan->update([
                                      'photo' => $dados['image']['src'],
                                  ]);

                    $product->update([
                                         'photo' => $dados['image']['src'],
                                     ]);
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
