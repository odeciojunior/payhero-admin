<?php

namespace Modules\Affiliates\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Affiliates\Http\Requests\AffiliateStoreRequest;
use Modules\Affiliates\Http\Requests\AffiliateUpdateRequest;
use Modules\Affiliates\Transformers\ProjectAffiliateResource;
use Modules\Affiliates\Transformers\AffiliateLinkResource;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\AffiliateLink;
use Modules\Core\Entities\AffiliateRequest;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\UserProject;
use Modules\Core\Events\AffiliateEvent;
use Modules\Core\Events\AffiliateRequestEvent;
use Modules\Core\Events\EvaluateAffiliateRequestEvent;
use Modules\Core\Services\AffiliateService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\UserService;
use Modules\Projects\Transformers\ProjectsResource;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Affiliates\Transformers\AffiliateResource;
use Modules\Affiliates\Transformers\AffiliateRequestResource;
use Illuminate\Support\Facades\Gate;

class AffiliateLinksApiController extends Controller
{
    /**
     *
     */
    public function index(Request $request)
    {
        try {

            $projectId = current(Hashids::decode($request->input('projectId')));
            $userId    = auth()->user()->account_owner_id;

            $links = AffiliateLink::whereHas('affiliate', function($q) use ($userId, $projectId) {
                $q->where('user_id', $userId)
                  ->where('project_id', $projectId);
            })
                                  ->with('affiliate.project.domains', 'plan');
            if (!empty($request->input('plan'))) {
                $links = $links->whereHas('plan', function($q2) use ($request) {
                    $q2->where('name', 'like', '%' . $request->input('plan') . '%');
                });
            }

            return AffiliateLinkResource::collection($links->paginate(5));
        } catch (Exception $e) {

            return response()->json(['message' => 'Ocorreu um erro'], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $affiliateId = current(Hashids::decode($request->input('affiliate')));
            $link = $request->input('link-affiliate');

            if (!empty($affiliateId) && !empty($link)) {

                $affiliate = Affiliate::with(['project.domains' => function($query) {
                                        $query->where('status', 3);
                                      }])
                                      ->where('id', $affiliateId)
                                      ->where('user_id', auth()->user()->account_owner_id)
                                      ->first();

                $domain = $affiliate->project->domains->first()->name;
                if(strpos($link, $domain) === false) {
                    return response()->json(['message' => 'Link inválido'], 400);
                }

                if (!empty($affiliate->id)) {
                    $affiliateLink = AffiliateLink::create([
                                                                'link'         => $link,
                                                                'affiliate_id' => $affiliateId,
                                                        ]);
                    if($affiliateLink) {
                        $affiliateLink->update(['parameter' => Hashids::connection('affiliate')->encode($affiliateLink->id)]);
                        return response()->json(['message' => 'Link criado com sucesso'], 200);
                    }
                }
            }

            return response()->json(['message' => 'Ocorreu um erro ao criar o Link'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao criar o link afiliado (AffiliateLinksApiController - store)');
            report($e);
            return response()->json(['message' => 'Ocorreu um erro ao criar o link'], 400);
        }
    }
    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        try {
            $linkId = current(Hashids::decode($id));
            if ($linkId) {
                $link = AffiliateLink::with('affiliate')->find($linkId);
                if($link->affiliate->user_id == auth()->user()->account_owner_id) {
                    return new AffiliateLinkResource($link);
                }
            }

            return response()->json(['message' => 'Link não encontrado'], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro'], 400);
        }
    }
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $linkId = current(Hashids::decode($id));
            $link = $request->input('link');

            if (!empty($linkId) && !empty($link)) {

                $linkAffiliate = AffiliateLink::with('affiliate')->find($linkId);
                if($linkAffiliate->affiliate->user_id == auth()->user()->account_owner_id) {
                    $project = Project::with(['domains' => function($query) {
                                        $query->where('status', 3);
                                    }])
                                    ->find($linkAffiliate->affiliate->project_id);

                    $domain = $project->domains->first()->name;
                    if(strpos($link, $domain) === false) {
                        return response()->json(['message' => 'Link inválido'], 400);
                    }

                    $update = $linkAffiliate->update(['link' => $link]);
                    if ($update) {
                        return response()->json(['message' => 'Link atualizado com sucesso!'], 200);
                    }
                }
            }

            return response()->json(['message' => 'Erro ao atualizar link!'], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao atualizar link!'], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $linkId        = current(Hashids::decode($id));
            $linkAffiliate = AffiliateLink::with('affiliate')->find($linkId);

            if($linkAffiliate->affiliate->user_id == auth()->user()->account_owner_id) {
                $deleted = $linkAffiliate->delete();
                if ($deleted) {
                    return response()->json(['message' => 'Link removido com sucesso!'], 200);
                }
            }

            return response()->json(['message' => 'Erro ao remover link'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao remover Link (AffiliateLinksApiController - destroy)');
            report($e);

            return response()->json(['message' => 'Erro ao remover link!'], 400);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        try {
            $linkId = current(Hashids::decode($id));
            if ($linkId) {
                $link = AffiliateLink::with('affiliate')->find($linkId);
                if($link->affiliate->user_id == auth()->user()->account_owner_id) {
                    return new AffiliateLinkResource($link);
                }
            }

            return response()->json(['message' => 'Link não encontrado'], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro'], 400);
        }
    }
}
