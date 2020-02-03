<?php

namespace Modules\Affiliates\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Affiliates\Http\Requests\AffiliateStoreRequest;
use Modules\Affiliates\Transformers\ProjectAffiliateResource;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\AffiliateLink;
use Modules\Core\Entities\AffiliateRequest;
use Modules\Core\Entities\Project;
use Modules\Core\Services\AffiliateService;
use Modules\Projects\Transformers\ProjectsResource;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Affiliates\Transformers\AffiliateResource;
use Modules\Affiliates\Transformers\AffiliateRequestResource;
use Illuminate\Support\Facades\Gate;

class AffiliatesApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('affiliates::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('affiliates::create');
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
                $projectModel = new Project();
                $project      = $projectModel->find($projectId);
                if ($data['type'] == 'affiliate') {
                    $affiliateModel   = new Affiliate();
                    $affiliateService = new AffiliateService();
                    $affiliate        = $affiliateModel->create([
                                                                    'user_id'     => auth()->user()->account_owner_id,
                                                                    'project_id'  => $project->id,
                                                                    'company_id'  => $companyId,
                                                                    'percentage'  => $project->percentage_affiliates,
                                                                    'status_enum' => $affiliateModel->present()
                                                                                                    ->getStatus('approved'),
                                                                ]);
                    $affiliateLink    = $affiliateService->createAffiliateLink($affiliate->id, $project->id);
                    if ($affiliateLink) {
                        return response()->json([
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
                        return response()->json([
                                                    'message' => 'Solicitação de afiliação criada com sucesso!',
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
            $project = $projectModel->with('usersProjects')->find($projectId);

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
                $affiliate = Affiliate::find($affiliateId);

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
            // $data = [
            //     "percentage"  => $request->input(),
            //     "status_enum" => ,
            // ];
            $update = Affiliate::find($affiliateId)->update($data);
            if ($update) {
                return response()->json([
                                            'message' => 'Afiliado atualizado com sucesso!',
                                        ], 400);
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
            $affiliate   = Affiliate::find($affiliateId);

            // if (Gate::denies('destroy', [$affiliate])) {
            //     return response()->json([
            //             'message' => 'Sem permissão',
            //         ],Response::HTTP_FORBIDDEN
            //     );
            // }

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

    public function getAffiliates($projectId)
    {
        try {

            $projectId = current(Hashids::decode($projectId));

            $affiliates = Affiliate::with('user')->where('project_id', $projectId)->get();

            return response()->json(['data' => AffiliateResource::collection($affiliates)], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu um erro'], 400);
        }
    }

    public function getAffiliateRequests($projectId)
    {
        try {

            $projectId = current(Hashids::decode($projectId));

            $affiliates = AffiliateRequest::with('user')->where('project_id', $projectId)->get();

            return response()->json(['data' => AffiliateRequestResource::collection($affiliates)], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu um erro'], 400);
        }
    }
}
