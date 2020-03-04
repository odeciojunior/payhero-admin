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

class AffiliatesApiController extends Controller
{
    /**
     *
     */
    public function index()
    {

    }

    /**
     * @param AffiliateStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AffiliateStoreRequest $request)
    {
        try {
            $data      = $request->validated();
            $projectId = current(Hashids::decode($data['project_id']));
            $companyId = current(Hashids::decode($data['company_id']));
            if ($projectId && $companyId) {
                $companyService = new CompanyService();
                $userService    = new UserService();
                if (!$companyService->isDocumentValidated($companyId)) {
                    return response()->json([
                                                'message' => 'Para se afiliar os documentos da empresa precisam estar aprovados!',
                                            ], 400);
                }
                if (!$userService->isDocumentValidated()) {
                    return response()->json([
                                                'message' => 'Para se afiliar os seus documentos pessoais precisam estar aprovados!',
                                            ], 400);
                }
                $projectModel = new Project();
                $project      = $projectModel->find($projectId);
                if ($data['type'] == 'affiliate') {
                    $affiliateModel   = new Affiliate();
                    $affiliateService = new AffiliateService();
                    $affiliate        = $affiliateModel->create([
                                                                    'user_id'     => auth()->user()->account_owner_id,
                                                                    'project_id'  => $project->id,
                                                                    'company_id'  => $companyId,
                                                                    'percentage'  => $project->percentage_affiliates ?? 20,
                                                                    'status_enum' => $affiliateModel->present()
                                                                                                    ->getStatus('active'),
                                                                ]);
                    $affiliateLink    = $affiliateService->createAffiliateLinks($affiliate->id, $project->id);
                    if ($affiliateLink) {
                        event(new AffiliateEvent($affiliate));

                        return response()->json([
                                                    'type'    => 'affiliate',
                                                    'message' => 'Afiliação criada com sucesso!',
                                                ], 200);
                    } else {
                        return response()->json([
                                                    'message' => 'Ocorreu um erro ao criar afiliação!',
                                                ], 400);
                    }
                } else {
                    $affiliateRequestModel = new AffiliateRequest();
                    $affiliateRequest      = $affiliateRequestModel->create([
                                                                                'user_id'    => auth()->user()->account_owner_id,
                                                                                'project_id' => $project->id,
                                                                                'company_id' => $companyId,
                                                                                'status'     => $affiliateRequestModel->present()
                                                                                                                      ->getStatus('pending'),
                                                                            ]);
                    if ($affiliateRequest) {
                        event(new AffiliateRequestEvent($affiliateRequest));

                        return response()->json([
                                                    'type'    => 'affiliate_request',
                                                    'message' => 'Solicitação enviada com sucesso!',
                                                ], 200);
                    } else {
                        return response()->json([
                                                    'message' => 'Ocorreu um erro ao solicitar afiliação!',
                                                ], 400);
                    }
                }
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro ao criar a afiliação',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao criar a afiliação (AffiliatesApiController - store)');
            report($e);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $projectModel = new Project();
        $projectId    = current(Hashids::decode($id));
        if ($projectId) {
            $project = $projectModel->with('usersProjects.user')->find($projectId);

            return new ProjectAffiliateResource($project);
        }

        return response()->json([
                                    'message' => 'Projeto não encontrado',
                                ], 400);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        // return view('affiliates::edit');
        try {
            $affiliateId = current(Hashids::decode($id));
            if ($affiliateId) {
                $affiliate = Affiliate::with('user', 'company')->find($affiliateId);

                return new AffiliateResource($affiliate);
            }

            return response()->json(['message' => 'Afiliado não encontrado'], 400);
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
    public function update(AffiliateUpdateRequest $request, $id)
    {
        try {
            $data        = $request->validated();
            $affiliateId = current(Hashids::decode($id));
            $data['percentage'] = preg_replace( '/[^0-9]/', '', $data['percentage']);
            $update = Affiliate::find($affiliateId)->update($data);
            if ($update) {
                return response()->json([
                                            'message' => 'Afiliado atualizado com sucesso!',
                                        ], 200);
            }

            return response()->json([
                                        'message' => 'Ocorreu um erro ao atualizar afiliado!',
                                    ], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao atualizar afiliado!',
                                    ], 400);
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
            $affiliateId = current(Hashids::decode($id));
            $affiliate   = Affiliate::with('affiliateLinks')->find($affiliateId);

            if (!empty($affiliate->affiliateLinks) && $affiliate->affiliateLinks->isNotEmpty()) {
                foreach ($affiliate->affiliateLinks as $affiliateLink) {
                    $affiliateLink->delete();
                }
            }
            $affiliateDeleted = $affiliate->delete();

            if ($affiliateDeleted) {
                return response()->json([
                                            'message' => 'Afiliado removido com sucesso!',
                                        ], 200);
            }

            return response()->json([
                                        'message' => 'Erro ao tentar remover Afiliado',
                                    ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar remover Afiliado (AffiliatesApiController - destroy)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao tentar remover, tente novamente mais tarde!',
                                    ], 400);
        }
    }

    public function getAffiliates()
    {
        try {
            $userProjectModel = new UserProject();
            $affiliateModel   = new Affiliate();
            $userId           = auth()->user()->account_owner_id;
            $userProjects     = $userProjectModel->where('user_id', $userId)->pluck('project_id');

            $affiliates = $affiliateModel->with('user', 'company', 'project')->whereIn('project_id', $userProjects);

            return AffiliateResource::collection($affiliates->orderBy('id', 'DESC')->paginate(5));
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu um erro'], 400);
        }
    }

    public function getAffiliateRequests()
    {
        try {
            $userProjectModel = new UserProject();
            $affiliateRequest = new AffiliateRequest();
            $userId           = auth()->user()->account_owner_id;
            $userProjects     = $userProjectModel->where('user_id', $userId)->pluck('project_id');

            $affiliatesRequest = $affiliateRequest->with('user', 'company', 'project')->whereIn('project_id', $userProjects);

            return AffiliateRequestResource::collection($affiliatesRequest->orderBy('id', 'DESC')->paginate(5));
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu um erro'], 400);
        }
    }

    public function evaluateAffiliateRequest(Request $request)
    {
        try {

            $userId             = auth()->user()->account_owner_id;
            $status             = $request->input('status');
            $affiliateRequestId = $request->input('affiliate');
            $affiliateRequestId = current(Hashids::decode($affiliateRequestId));

            $affiliateRequest = AffiliateRequest::where('id', $affiliateRequestId)
                                                ->wherehas('project.users', function($q) use ($userId) {
                                                    $q->where('users.id', $userId);
                                                })
                                                ->where('status', '<>', 3)
                                                ->first();
            if (!empty($affiliateRequest->id)) {

                if ($status == 3 && (int) $affiliateRequest->status != 3) {
                    $project          = Project::find($affiliateRequest->project_id);
                    $affiliateService = new AffiliateService();
                    $affiliateModel   = new Affiliate();
                    $affiliate        = $affiliateModel->create([
                                                              'user_id'     => $affiliateRequest->user_id,
                                                              'project_id'  => $project->id,
                                                              'company_id'  => $affiliateRequest->company_id,
                                                              'percentage'  => $project->percentage_affiliates ?? 20,
                                                              'status_enum' => $affiliateModel->present()->getStatus('active'),
                                                          ]);
                    $affiliateRequest->update(['status' => $status]);

                    $affiliateLink = $affiliateService->createAffiliateLinks($affiliate->id, $project->id);
                    if ($affiliateLink) {
                        event(new EvaluateAffiliateRequestEvent($affiliateRequest));
                        $affiliateRequest->delete();
                        return response()->json([
                                                    'message' => 'Afiliação criada com sucesso!',
                                                ], 200);
                    } else {
                        return response()->json([
                                                    'message' => 'Ocorreu um erro ao criar afiliação!',
                                                ], 400);
                    }
                } else if (in_array($status, [2, 4])) {
                    $update = $affiliateRequest->update(['status' => $status]);
                    if ($update) {
                        event(new EvaluateAffiliateRequestEvent($affiliateRequest));
                        $affiliateRequest->delete();

                        return response()->json([
                                                    'message' => 'Solicitação de afiliação avaliada com sucesso!',
                                                ], 200);
                    }
                }
            }

            return response()->json([
                                        'message' => 'Ocorreu um erro ao avaliar solicitação de afiliação!',
                                    ], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao avaliar solicitação de afiliação!',
                                    ], 400);
        }
    }

    public function getAffiliateLinks($projectId, Request $request)
    {
        try {

            $projectId = current(Hashids::decode($projectId));
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

    public function updateConfigAffiliate(Request $request, $id)
    {
        try {
            $data        = $request->only(['suport_contact', 'suport_phone']);
            $affiliateId = current(Hashids::decode($id));
            $update = Affiliate::find($affiliateId)->update($data);
            if ($update) {
                return response()->json([
                                            'message' => 'Configuração salva com sucesso!',
                                        ], 200);
            }

            return response()->json([
                                        'message' => 'Ocorreu um erro ao salvar!',
                                    ], 400);
        } catch (Exception $e) {
            report($e);
            return response()->json([
                                        'message' => 'Ocorreu um erro ao salvar!',
                                    ], 400);
        }
    }
}
