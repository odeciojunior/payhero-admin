<?php

namespace Modules\DemoAccount\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Pixel;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Project;
use Modules\Core\Services\PixelService;
use Modules\Pixels\Http\Controllers\PixelsApiController;
use Modules\Pixels\Transformers\PixelEditResource;
use Modules\Pixels\Transformers\PixelsResource;
use Vinkla\Hashids\Facades\Hashids;

class PixelsApiDemoController extends PixelsApiController
{
    public function index($projectId)
    {
        try {
            if (empty($projectId)) {
                return response()->json(['message' => __('controller.error.generic')], 400);
            }

            $project = Project::find(hashids_decode($projectId));

            $affiliate = Affiliate::where('project_id', $project->id)
                ->where('user_id', auth()->user()->account_owner_id)
                ->first();

            $affiliateId = $affiliate->id ?? null;

            $pixels = Pixel::where('project_id', $project->id)
                ->where('affiliate_id', $affiliateId)
                ->orderBy('id', 'DESC');

            return PixelsResource::collection($pixels->paginate(5));
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => __('controller.error.generic')], 400);
        }
    }

    public function show($projectId, $id): JsonResponse
    {
        try {
            if (empty($id) || empty($projectId)) {
                return response()->json('Pixel nao encontrado', 400);
            }

            $pixelModel = new Pixel();
            $projectModel = new Project();

            $pixel = $pixelModel->find(current(Hashids::decode($id)));

            if (empty($pixel)) {
                return response()->json('Pixel nao encontrado', 400);
            }

            $project = $projectModel->find(current(Hashids::decode($projectId)));
            $affiliateId = (!empty($pixel->affiliate_id)) ? $pixel->affiliate_id : 0;
            $pixel->platform_enum = Lang::get('definitions.enum.pixel.platform.' . $pixel->platform);

            $pixel->makeHidden(['id', 'project_id', 'campaing_id']);

            return response()->json($pixel);

        } catch (Exception $e) {
            report($e);

            return response()->json('Erro ao buscar pixel', 400);
        }
    }

    public function edit($projectId, $id)
    {
        try {
            if (empty($projectId) || empty($id)) {
                return response()->json(__('controller.error.generic'), 400);
            }

            $pixel = Pixel::find(hashids_decode($id));

            if (empty($pixel)) {
                return response()->json(__('controller.error.generic'), 400);
            }

            $applyPlanArray = [];
            
            if (!empty($pixel->apply_on_plans)) {
                $applyPlanDecoded = json_decode($pixel->apply_on_plans);
                if (in_array('all', $applyPlanDecoded)) {
                    $applyPlanArray[] = [
                        'id' => 'all',
                        'name' => 'Todos os Planos',
                        'description' => '',
                    ];
                } else {
                    foreach ($applyPlanDecoded as $key => $value) {
                        $plan = Plan::select(
                            'plans.id',
                            'plans.name',
                            'plans.description',
                            DB::raw(
                                '(select sum(if(p.shopify_id is not null and p.shopify_id = plans.shopify_id, 1, 0)) from plans p where p.deleted_at is null) as variants'
                            )
                        )->find($value);
                        
                        if (!empty($plan)) {
                            $applyPlanArray[] = [
                                'id' => Hashids::encode($plan->id),
                                'name' => $plan->name,
                                'description' => $plan->variants ? $plan->variants . ' variantes' : $plan->description,
                            ];
                        }
                    }
                }
            }

            $pixel->event_select = '';
            if ($pixel->platform == 'google_adwords') {
                foreach (PixelService::EVENTS as $EVENT) {
                    if ($pixel->$EVENT == true) {
                        $pixel->event_select = $EVENT;
                    }
                }
            }

            $pixel->apply_on_plans = $applyPlanArray;

            $pixel->makeHidden(['id', 'project_id', 'campaing_id']);

            return new PixelEditResource($pixel);

        } catch (Exception $e) {
            report($e);

            return response()->json(__('controller.error.generic'), 400);
        }
    }
}
