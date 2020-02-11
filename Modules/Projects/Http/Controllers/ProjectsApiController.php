<?php

namespace Modules\Projects\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\ProjectService;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\SmsService;
use Modules\Projects\Http\Requests\ProjectStoreRequest;
use Modules\Projects\Http\Requests\ProjectUpdateRequest;
use Modules\Projects\Transformers\ProjectsResource;
use Modules\Projects\Transformers\UserProjectResource;
use Modules\Shopify\Transformers\ShopifyIntegrationsResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Services\ProjectNotificationService;

/**
 * Class ProjectsApiController
 * @package Modules\Projects\Http\Controllers
 */
class ProjectsApiController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $projectModel   = new Project();
            $projectService = new ProjectService();
            $pagination     = $request->input('select') ?? false;

            if (!$pagination) {
                activity()->on($projectModel)->tap(function(Activity $activity) {
                    $activity->log_name = 'visualization';
                })->log('Visualizou tela todos os projetos');
            }

            if (!empty($request->input('status')) && $request->input('status') == 'active') {
                $projectStatus = [$projectModel->present()->getStatus('active')];
            } else {
                $projectStatus = [
                    $projectModel->present()->getStatus('active'), $projectModel->present()->getStatus('disabled'),
                ];
            }

            return $projectService->getUserProjects($pagination, $projectStatus, true);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar pagina de projetos (ProjectsController - index)');
            report($e);

            return response()->json(['message' => 'Erro ao tentar acessar projetos'], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function create()
    {
        try {
            activity()->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela criar projeto');

            $user = auth()->user()->load('companies');

            return response()->json(CompaniesSelectResource::collection($user->companies));
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar pagina de criar Projeto (ProjectController - create)');
            report($e);

            return response()->json(['message' => 'Erro ao carregar empresas'], 400);
        }
    }

    /**
     * @param ProjectStoreRequest $request
     * @return JsonResponse
     */
    public function store(ProjectStoreRequest $request)
    {
        try {
            $requestValidated = $request->validated();

            $projectModel        = new Project();
            $userProjectModel    = new UserProject();
            $shippingModel       = new Shipping();
            $digitalOceanService = app(DigitalOceanFileService::class);

            if (!empty($requestValidated)) {
                $requestValidated['company'] = Hashids::decode($requestValidated['company'])[0];

                $project = $projectModel->create([
                                                     'name'                       => $requestValidated['name'],
                                                     'description'                => $requestValidated['description'],
                                                     'installments_amount'        => 12,
                                                     'installments_interest_free' => 1,
                                                     'visibility'                 => 'private',
                                                     'automatic_affiliation'      => 0,
                                                     'boleto'                     => 1,
                                                     'status'                     => $projectModel->present()->getStatus('active'),
                                                     'checkout_type'              => 2 // checkout de 1 passo
                                                 ]);
                if (!empty($project)) {
                    $shipping = $shippingModel->create([
                                                           'project_id'   => $project->id,
                                                           'name'         => 'Frete gratis',
                                                           'information'  => 'de 15 até 30 dias',
                                                           'value'        => '0,00',
                                                           'type'         => 'static',
                                                           'type_enum'    => $shippingModel->present()->getTypeEnum('static'),
                                                           'status'       => '1',
                                                           'pre_selected' => '1',
                                                       ]);

                    if (!empty($shipping)) {
                        $photo = $request->file('photo-main');
                        if ($photo != null) {
                            try {
                                $img = Image::make($photo->getPathname());
                                $img->crop($requestValidated['photo_w'], $requestValidated['photo_h'], $requestValidated['photo_x1'], $requestValidated['photo_y1']);
                                $img->save($photo->getPathname());

                                $digitalOceanPath = $digitalOceanService
                                    ->uploadFile("uploads/user/" . Hashids::encode(auth()->user()->account_owner_id) . '/public/projects/' . Hashids::encode($project->id) . '/main', $photo);
                                $project->update(['photo' => $digitalOceanPath]);
                            } catch (Exception $e) {
                                Log::warning('Erro ao tentar salvar foto projeto - ProjectsController - store');
                                report($e);
                            }
                        }

                        $userProject = $userProjectModel->create([
                                                                     'user_id'           => auth()->user()->account_owner_id,
                                                                     'project_id'        => $project->id,
                                                                     'company_id'        => $requestValidated['company'],
                                                                     'type'              => 'producer',
                                                                     'type_enum'         => $userProjectModel->present()->getTypeEnum('producer'),
                                                                     'access_permission' => 1,
                                                                     'edit_permission'   => 1,
                                                                     'status'            => 'active',
                                                                     'status_flag'       => $userProjectModel->present()->getStatusFlag('active'),
                                                                 ]);

                        $projectNotificationService = new ProjectNotificationService();

                        if (!empty($userProject)) {
                            $projectNotificationService->createProjectNotificationDefault($project->id);
                            return response()->json(['message', 'Projeto salvo com sucesso']);
                        } else {
                            $digitalOceanPath->deleteFile($project->photo);
                            $shipping->delete();
                            $project->delete();

                            return response()->json(['message', 'Erro ao tentar salvar projeto'], 400);
                        }
                    } else {
                        $project->delete();

                        return response()->json(['message', 'Erro ao tentar salvar projeto'], 400);
                    }
                } else {
                    return response()->json(['message', 'Erro ao tentar salvar projeto'], 400);
                }
            } else {
                return response()->json(['message', 'Erro ao tentar salvar projeto'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar salvar projeto - ProjectsController -store');
            report($e);

            return response()->json(['message', 'Erro ao tentar salvar projeto'], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        try {
            $projectModel = new Project();

            if (isset($id)) {
                $userProjectModel        = new UserProject();
                $shopifyIntegrationModel = new ShopifyIntegration();

                $user = auth()->user()->load('companies');

                $idProject = current(Hashids::decode($id));
                $project   = $projectModel->find($idProject);

                activity()->on($projectModel)->tap(function(Activity $activity) use ($id) {
                    $activity->log_name   = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                })->log('Visualizou tela editar configurações do projeto ' . $project->name);

                $userProject = $userProjectModel->where('user_id', $user->account_owner_id)
                                                ->where('project_id', $idProject)->first();
                $userProject = new UserProjectResource($userProject);

                $shopifyIntegrations = $shopifyIntegrationModel->where('user_id', $user->account_owner_id)
                                                               ->where('project_id', $idProject)->get();
                $shopifyIntegrations = ShopifyIntegrationsResource::collection($shopifyIntegrations);

                $companies = CompaniesSelectResource::collection($user->companies);

                if (Gate::allows('edit', [$project])) {
                    $project = new ProjectsResource($project);

                    return response()->json(compact('companies', 'project', 'userProject', 'shopifyIntegrations'));
                } else {
                    return response()->json(['message' => 'Erro ao carregar configuraçoes do projeto'], 400);
                }
            }

            return response()->json([
                                        'message' => 'Erro ao carregar configuracoes do projeto',
                                    ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao carregar configuracoes do projeto (ProjectsApiController - edit)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao carregar configuracoes do projeto',
                                    ], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $projectModel = new Project();
            $projectId    = current(Hashids::decode($id));

            activity()->on($projectModel)->tap(function(Activity $activity) use ($projectId) {
                $activity->log_name   = 'deleted';
                $activity->subject_id = $projectId;
            })->log('deleted');

            $project = $projectModel->where('id', $projectId)->first();

            if (Gate::allows('destroy', [$project])) {

                $projectService = new ProjectService();

                if ($projectId) {
                    //n tem venda
                    if ($projectService->delete($projectId)) {
                        //projeto removido
                        return response()->json('success', 200);
                    } else {
                        //erro ao remover projeto
                        return response()->json('error', 400);
                    }
                } else {
                    return response()->json('Projeto não encontrado', 400);
                }
            } else {
                return response()->json('Sem permissão para remover projeto', 403);
            }
        } catch (Exception $e) {
            Log::warning('ProjectController - delete - Erro ao deletar project');
            report($e);

            return response()->json('Erro ao remover o projeto, tente novamente mais tarde', 400);
        }
    }

    /**
     * @param ProjectUpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ProjectUpdateRequest $request, $id)
    {
        try {

            $requestValidated    = $request->validated();
            $projectModel        = new Project();
            $userProjectModel    = new UserProject();
            $digitalOceanService = app(DigitalOceanFileService::class);

            if ($requestValidated) {

                $project = $projectModel->find(current(Hashids::decode($id)));

                if (Gate::allows('update', [$project])) {

                    if ($requestValidated['installments_amount'] < $requestValidated['installments_interest_free']) {
                        $requestValidated['installments_interest_free'] = $requestValidated['installments_amount'];
                    }

                    $requestValidated['status']          = 1;

                    $requestValidated['invoice_description'] = FoxUtils::removeAccents($requestValidated['invoice_description']);

                    $requestValidated['cost_currency_type'] = $project->present()
                                                                      ->getCurrencyCost($requestValidated['cost_currency_type']);

                    $projectUpdate  = $project->fill($requestValidated)->save();
                    $projectChanges = $project->getChanges();
                    if (isset($projectChanges["support_phone"])) {
                        $project->fill(["support_phone_verified" => false])->save();
                    }
                    if (isset($projectChanges["contact"])) {
                        $project->fill(["contact_verified" => false])->save();
                    }
                    if ($projectUpdate) {
                        try {
                            $projectPhoto = $request->file('photo');
                            if ($projectPhoto != null) {
                                $digitalOceanService->deleteFile($project->photo);
                                $img = Image::make($projectPhoto->getPathname());
                                $img->crop($requestValidated['photo_w'], $requestValidated['photo_h'], $requestValidated['photo_x1'], $requestValidated['photo_y1']);
                                $img->resize(300, 300);
                                $img->save($projectPhoto->getPathname());

                                $digitalOceanPath = $digitalOceanService
                                    ->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->account_owner_id) . '/public/projects/' . Hashids::encode($project->id) . '/main', $projectPhoto);
                                $project->update([
                                                     'photo' => $digitalOceanPath,
                                                 ]);
                            }

                            $projectLogo = $request->file('logo');
                            if ($projectLogo != null) {

                                $digitalOceanService->deleteFile($project->logo);
                                $img = Image::make($projectLogo->getPathname());

                                $img->resize(null, 300, function($constraint) {
                                    $constraint->aspectRatio();
                                });

                                $img->save($projectLogo->getPathname());

                                $digitalOceanPathLogo = $digitalOceanService
                                    ->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->account_owner_id) . '/public/projects/' . Hashids::encode($project->id) . '/logo', $projectLogo);

                                $project->update([
                                                     'logo' => $digitalOceanPathLogo,
                                                 ]);
                            }
                        } catch (Exception $e) {
                            Log::warning('ProjectController - update - Erro ao enviar foto');
                            report($e);

                            return response()->json(['message', 'Erro ao atualizar projeto'], 400);
                        }

                        $userProject = $userProjectModel->where([
                                                                    ['user_id', auth()->user()->account_owner_id],
                                                                    ['project_id', $project->id],
                                                                ])->first();
                        if (!empty($requestValidated['company_id'])) {
                            $requestValidated['company_id'] = current(Hashids::decode($requestValidated['company_id']));
                            if ($userProject->company_id != $requestValidated['company_id']) {
                                $userProject->update(['company_id' => $requestValidated['company_id']]);
                            }
                        }

                        //ATUALIZA STATUS E VALOR DA RECOBRANÇA POR FALTA DE SALDO
                        if (isset($projectChanges["discount_recovery_status"])) {
                            $project->update([
                                                 'discount_recovery_status' => $requestValidated['discount_recovery_status'],
                                                 'discount_recovery_value'  => $requestValidated['discount_recovery_value'],
                                             ]);
                        } else {
                            $project->update([
                                                 'discount_recovery_status' => 0,
                                             ]);
                        }

                        return response()->json(['message' => 'Projeto atualizado!'], 200);
                    }

                    return response()->json(['message', 'Erro ao atualizar projeto'], 400);
                } else {
                    return response()->json(['message' => 'Sem permissão para atualizar o projeto'], 403);
                }
            }

            return response()->json(['message', 'Erro ao atualizar projeto'], 400);
        } catch (Exception $e) {
            Log::warning('ProjectController - update - Erro ao atualizar project');
            report($e);

            return response()->json(['message', 'Erro ao atualizar projeto'], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse|ProjectsResource
     */
    public function show($id)
    {
        try {
            $projectModel = new Project();
            $userId = auth()->user()->account_owner_id;
            if ($id) {

                $project = $projectModel->where('id', current(Hashids::decode($id)))
                                        ->where('status', $projectModel->present()->getStatus('active'))
                                        ->with(['affiliates' => function($query) use ($userId) {
                                            $query->where('user_id', $userId)->where('status_enum', 3);
                                        }])
                                        ->first();

                activity()->on($projectModel)->tap(function(Activity $activity) use ($id) {
                    $activity->log_name   = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                })->log('Visualizou o projeto ' . $project->name);

                if (Gate::allows('show', [$project])) {
                    return new ProjectsResource($project);
                } else {
                    return response()->json(['message' => 'Erro ao exibir detalhes do projeto'], 400);
                }
            } else {
                return response()->json(['message' => 'Erro ao exibir detalhes do projeto'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar detalhes do projeto (ProjectsController - show)');
            report($e);

            return response()->json(['message' => 'Erro ao exibir detalhes do projeto'], 400);
        }
    }

    /**
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getProjects()
    {
        try {

            $projectService = new ProjectService();
            $projectModel   = new Project();

            $projectStatus = [
                $projectModel->present()->getStatus('active'),
            ];

            return $projectService->getUserProjects(true, $projectStatus, true);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados empresas (ProjectsApiController - getProjects)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, ao buscar dados das empresas',
                                    ], 400);
        }
    }

    /**
     * @param $projectId
     * @param Request $request
     * @return JsonResponse
     */
    public function verifySupportphone($projectId, Request $request)
    {
        try {
            $projectModel = new Project();

            $data         = $request->all();
            $supportPhone = $data["support_phone"] ?? null;
            if (FoxUtils::isEmpty($supportPhone)) {
                return response()->json(
                    [
                        'message' => 'Telefone não pode ser vazio!',
                    ], 400);
            }
            $project = $projectModel->find(Hashids::decode($projectId))->first();

            activity()->on($projectModel)->tap(function(Activity $activity) use ($projectId) {
                $activity->log_name   = 'visualization';
                $activity->subject_id = current(Hashids::decode($projectId));
            })
                      ->log('Visualizou tela envio de código para verificação de telefone contato do projeto ' . $project->name);

            if ($supportPhone != $project->support_phone) {
                $project->support_phone = $supportPhone;
                $project->save();
            }

            $verifyCode = random_int(100000, 999999);

            $message    = "Código de verificação CloudFox - " . $verifyCode;
            $smsService = new SmsService();
            $smsService->sendSms($supportPhone, $message);

            return response()->json(
                [
                    "message" => "Mensagem enviada com sucesso!",

                ], 200)
                             ->withCookie("supportphoneverifycode_" . Hashids::encode(auth()->id()) . $projectId, $verifyCode, 15);
        } catch (Exception $ex) {
            report($ex);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro ao enviar sms para o telefone informado!',
                ], 400);
        }
    }

    /**
     * @param $projectId
     * @param Request $request
     * @return JsonResponse
     */
    public function matchSupportphoneVerifyCode($projectId, Request $request)
    {
        try {
            $projectModel = new Project();
            $project      = $projectModel->where("id", current(Hashids::decode($projectId)))->first();

            activity()->on($projectModel)->tap(function(Activity $activity) use ($projectId) {
                $activity->log_name   = 'updated';
                $activity->subject_id = current(Hashids::decode($projectId));
            })->log('Validação código telefone de contato do projeto ' . $project->name);

            $data       = $request->all();
            $verifyCode = $data["verifyCode"] ?? null;
            if (empty($verifyCode)) {
                return response()->json(
                    [
                        'message' => 'Código de verificação não pode ser vazio!',
                    ], 400);
            }
            $cookie = Cookie::get("supportphoneverifycode_" . Hashids::encode(auth()->id()) . $projectId);
            if ($verifyCode != $cookie) {
                return response()->json(
                    [
                        'message' => 'Código de verificação inválido!',
                    ], 400);
            }

            $project->update(["support_phone_verified" => true]);

            return response()->json(
                [
                    "message" => "Telefone verificado com sucesso!",
                ], 200)
                             ->withCookie(Cookie::forget("supportphoneverifycode_" . Hashids::encode(auth()->id())) . $projectId);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro ao verificar código!',
                ], 400);
        }
    }

    /**
     * @param $projectId
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyContact($projectId, Request $request)
    {
        try {
            $projectModel = new Project();
            $project      = $projectModel->find(Hashids::decode($projectId))->first();

            activity()->on($projectModel)->tap(function(Activity $activity) use ($projectId) {
                $activity->log_name   = 'visualization';
                $activity->subject_id = current(Hashids::decode($projectId));
            })->log('Visualizou tela envio de codigo para verificação email contato do projeto: ' . $project->name);

            $data    = $request->all();
            $contact = $data["contact"] ?? null;
            if (FoxUtils::isEmpty($contact)) {
                return response()->json(
                    [
                        'message' => 'Email não pode ser vazio!',
                    ], 400);
            }

            if ($contact != $project->contact) {
                $project->contact = $contact;
                $project->save();
            }

            $verifyCode = random_int(100000, 999999);

            $data = [
                "verify_code" => $verifyCode,
            ];

            /** @var SendgridService $sendgridService */
            $sendgridService = app(SendgridService::class);
            $sendgridService->sendEmail(
                'noreply@cloudfox.net', 'cloudfox', $contact, auth()->user()->name, "d-5f8d7ae156a2438ca4e8e5adbeb4c5ac", $data
            );

            return response()->json(
                [
                    "message" => "Email enviado com sucesso!",

                ], 200)
                             ->withCookie("contactverifycode_" . Hashids::encode(auth()->id()) . $projectId, $verifyCode, 15);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, ao enviar o email com o código!',
                ], 400);
        }
    }

    /**
     * @param $projectId
     * @param Request $request
     * @return JsonResponse
     */
    public function matchContactVerifyCode($projectId, Request $request)
    {
        try {
            $projectModel = new Project();
            $project      = $projectModel->where("id", current(Hashids::decode($projectId)))->first();

            activity()->on($projectModel)->tap(function(Activity $activity) use ($projectId) {
                $activity->log_name   = 'updated';
                $activity->subject_id = current(Hashids::decode($projectId));
            })->log('Validação código email de contato do projeto: ' . $project->name);

            $data       = $request->all();
            $verifyCode = $data["verifyCode"] ?? null;
            if (empty($verifyCode)) {
                return response()->json(
                    [
                        'message' => 'Código de verificação não pode ser vazio!',
                    ], 400);
            }
            $cookie = Cookie::get("contactverifycode_" . Hashids::encode(auth()->id()) . $projectId);
            if ($verifyCode != $cookie) {
                return response()->json(
                    [
                        'message' => 'Código de verificação inválido!',
                    ], 400);
            }

            $project->update(["contact_verified" => true]);

            return response()->json(
                [
                    "message" => "Email verificado com sucesso!",
                ], 200)
                             ->withCookie(Cookie::forget("contactverifycode_" . Hashids::encode(auth()->id())) . $projectId);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro ao verificar código!',
                ], 400);
        }
    }
}

