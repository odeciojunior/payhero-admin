<?php

namespace Modules\Affiliates\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Affiliates\Transformers\AffiliateLinkCollection;
use Modules\Affiliates\Transformers\AffiliateLinkResource;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\AffiliateLink;
use Modules\Core\Entities\Project;
use Vinkla\Hashids\Facades\Hashids;

class AffiliateLinksApiController extends Controller
{
    /**
     *
     */
    public function index(Request $request)
    {
        try {

            $projectId = current(Hashids::decode($request->input('projectId')));
            $userId    = auth()->user()->getAccountOwnerId();

            $links = AffiliateLink::whereHas('affiliate', function($q) use ($userId, $projectId) {
                $q->where('user_id', $userId)->where('project_id', $projectId);
            })->with('affiliate.project.domains', 'plan');

            if (!empty($request->input('plan'))) {
                $links = $links->whereHas('plan',
                function($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->input('plan') . '%')
                    ->orWhere('price', 'like', '%'. str_replace(array('R', '$', ' ', '.', ','), array('', '', '', '', '.'),$request->input('plan')). '%')
                    ->orWhere('description', 'like', '%' . $request->input('plan') . '%');
                });

                $union = AffiliateLink::whereHas("affiliate", function ($q) use ($userId, $projectId) {
                    $q->where("user_id", $userId)->where("project_id", $projectId);
                })->with("affiliate.project.domains", "plan");
                $union->where("affiliate_links.name", "like", "%" . $request->input("plan") . "%");
                $links->union($union);
            }

            $links = $links->whereHas("affiliate.project.domains", function ($query) {
                $query->where("status", 3);
            });

            return new AffiliateLinkCollection($links->paginate(5));
        } catch (Exception $e) {
            return response()->json(["message" => "Ocorreu um erro"], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $affiliateId = current(Hashids::decode($request->input("affiliate")));
            $link = $request->input("link-affiliate");
            $name = $request->input("link-affiliate-name");

            $request->validate(["link-affiliate" => "required|max:254"]);
            if (!empty($affiliateId) && !empty($link) && !empty($name)) {

                $affiliate = Affiliate::with(['project.domains' => function($query) {
                                        $query->where('status', 3);
                                      }])
                                      ->where('id', $affiliateId)
                                      ->where('user_id', auth()->user()->getAccountOwnerId())
                                      ->first();

                $domain = $affiliate->project->domains->first()->name;
                if (strpos($link, $domain) === false) {
                    return response()->json(
                        ["message" => "O link deve estar dentro do domínio cadastrado na loja"],
                        400
                    );
                }

                if (!empty($affiliate->id)) {
                    $affiliateLink = AffiliateLink::create([
                        "link" => $link,
                        "name" => $name,
                        "affiliate_id" => $affiliateId,
                    ]);
                    if ($affiliateLink) {
                        $affiliateLink->update([
                            "parameter" => Hashids::connection("affiliate")->encode($affiliateLink->id),
                        ]);
                        return response()->json(["message" => "Link criado com sucesso"], 200);
                    }
                }
            }

            return response()->json(["message" => "Ocorreu um erro ao criar o Link"], 400);
        } catch (Exception $e) {
            Log::warning("Erro ao criar o link afiliado (AffiliateLinksApiController - store)");
            report($e);
            return response()->json(["message" => "Ocorreu um erro ao criar o link"], 400);
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
                if($link->affiliate->user_id == auth()->user()->getAccountOwnerId()) {
                    return new AffiliateLinkResource($link);
                }
            }

            return response()->json(["message" => "Link não encontrado"], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro"], 400);
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
            $request->validate(["link" => "required|max:254"]);
            $linkId = current(Hashids::decode($id));
            $link = $request->input("link");
            $name = $request->input("name");

            if (!empty($linkId) && !empty($link) && !empty($name)) {

                $linkAffiliate = AffiliateLink::with('affiliate')->find($linkId);
                if($linkAffiliate->affiliate->user_id == auth()->user()->getAccountOwnerId()) {
                    $project = Project::with(['domains' => function($query) {
                                        $query->where('status', 3);
                                    }])
                                    ->find($linkAffiliate->affiliate->project_id);

                    $domain = $project->domains->first()->name;
                    if (strpos($link, $domain) === false) {
                        return response()->json(["message" => "Link inválido"], 400);
                    }

                    $update = $linkAffiliate->update(["link" => $link, "name" => $name]);
                    if ($update) {
                        return response()->json(["message" => "Link atualizado com sucesso!"], 200);
                    }
                }
            }

            return response()->json(["message" => "Erro ao atualizar link!"], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Erro ao atualizar link!"], 400);
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
            $linkId = current(Hashids::decode($id));
            $linkAffiliate = AffiliateLink::with("affiliate")->find($linkId);

            if($linkAffiliate->affiliate->user_id == auth()->user()->getAccountOwnerId()) {
                $deleted = $linkAffiliate->delete();
                if ($deleted) {
                    return response()->json(["message" => "Link removido com sucesso!"], 200);
                }
            }

            return response()->json(["message" => "Erro ao remover link"], 400);
        } catch (Exception $e) {
            Log::warning("Erro ao remover Link (AffiliateLinksApiController - destroy)");
            report($e);

            return response()->json(["message" => "Erro ao remover link!"], 400);
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
                if($link->affiliate->user_id == auth()->user()->getAccountOwnerId()) {
                    return new AffiliateLinkResource($link);
                }
            }

            return response()->json(["message" => "Link não encontrado"], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro"], 400);
        }
    }
}
