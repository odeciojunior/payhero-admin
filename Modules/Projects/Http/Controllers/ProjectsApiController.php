<?php

namespace Modules\Projects\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\Facades\Image;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\PixelConfig;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\AmazonFileService;
use Modules\Core\Services\CacheService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\ProjectService;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\SmsService;
use Modules\Core\Services\TaskService;
use Modules\Projects\Http\Requests\ProjectStoreRequest;
use Modules\Projects\Http\Requests\ProjectUpdateRequest;
use Modules\Projects\Http\Requests\ProjectsSettingsUpdateRequest;
use Modules\Projects\Transformers\ProjectsResource;
use Modules\Projects\Transformers\UserProjectResource;
use Modules\Shopify\Transformers\ShopifyIntegrationsResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ProjectsApiController
 * @package Modules\Projects\Http\Controllers
 */
class ProjectsApiController extends Controller
{
    public function index(Request $request)
    {
        try {

            $user = auth()->user();

            $hasCompany = Company::where('user_id', $user->account_owner_id)->exists();

            if ($hasCompany) {
                $projectModel = new Project();
                $projectService = new ProjectService();
                $pagination = $request->input('select') ?? false;
                $affiliation = true;

                if (!empty($request->input('affiliate')) && $request->input('affiliate') == 'false') {
                    $affiliation = false;
                }

                if (!$pagination) {
                    activity()->on($projectModel)->tap(
                        function (Activity $activity) {
                            $activity->log_name = 'visualization';
                        }
                    )->log('Visualizou tela todos os projetos');
                }

                if (!empty($request->input('status')) && $request->input('status') == 'active') {
                    $projectStatus = [$projectModel->present()->getStatus('active')];
                } else {
                    if ($user->deleted_project_filter) {
                        $projectStatus = [
                            $projectModel->present()->getStatus('active'),
                            $projectModel->present()->getStatus('disabled'),
                        ];
                    } else {
                        $projectStatus = [$projectModel->present()->getStatus('active')];
                    }
                }

                return $projectService->getUserProjects($pagination, $projectStatus, $affiliation);
            } else {
                return response()->json([
                    'data' => [],
                    'no_company' => true,
                    'message' => 'Nenhuma empresa cadastrada!'
                ]);
            }

        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao tentar acessar projetos'], 400);
        }
    }

    public function create(): JsonResponse
    {
        try {
            activity()->tap(
                function (Activity $activity) {
                    $activity->log_name = 'visualization';
                }
            )->log('Visualizou tela criar projeto');

            $user = auth()->user();
            $companies = Company::where('user_id',$user->account_owner_id)->get();

            return response()->json(CompaniesSelectResource::collection($companies));
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao carregar empresas'], 400);
        }
    }

    public function store(ProjectStoreRequest $request): JsonResponse
    {
        try {
            $requestValidated = $request->validated();

            $projectModel = new Project();
            $userProjectModel = new UserProject();
            $shippingModel = new Shipping();
            $amazonFileService = app(AmazonFileService::class);

            if (empty($requestValidated)) {
                return response()->json(['message' => 'Erro ao tentar salvar projeto'], 400);
            }

            $requestValidated['company'] = Hashids::decode($requestValidated['company'])[0];

            $project = $projectModel->create(
                [
                    'name' => $requestValidated['name'],
                    'description' => $requestValidated['description'],
                    'installments_amount' => 12,
                    'installments_interest_free' => 1,
                    'visibility' => 'private',
                    'automatic_affiliation' => 0,
                    'boleto' => 1,
                    'status' => $projectModel->present()->getStatus('active'),
                    'checkout_type' => 2, // checkout de 1 passo
                    'notazz_configs' => json_encode(
                        [
                            'cost_currency_type' => 1,
                            'update_cost_shopify' => 1,
                        ]
                    )
                ]
            );

            if (empty($project)) {
                return response()->json(['message' => 'Erro ao tentar salvar projeto'], 400);
            }

            PixelConfig::create(['project_id' => $project->id]);

            $shipping = $shippingModel->create(
                [
                    'project_id' => $project->id,
                    'name' => 'Frete gratis',
                    'information' => 'de 15 até 30 dias',
                    'value' => '0,00',
                    'type' => 'static',
                    'type_enum' => $shippingModel->present()->getTypeEnum('static'),
                    'status' => '1',
                    'pre_selected' => '1',
                    'apply_on_plans' => '["all"]',
                    'not_apply_on_plans' => '[]'
                ]
            );

            if (empty($shipping)) {
                $project->delete();

                return response()->json(['message' => 'Erro ao tentar salvar projeto'], 400);
            }

            $photo = $request->file('photo');
            if ($photo != null) {
                try {
                    $img = Image::make($photo->getPathname());
                    $img->save($photo->getPathname());

                    $amazonPath = $amazonFileService
                        ->uploadFile("uploads/user/" . Hashids::encode(auth()->user()->account_owner_id) . '/public/projects/' . Hashids::encode($project->id) . '/main',
                            $photo);
                    $project->update(['photo' => $amazonPath]);
                } catch (Exception $e) {
                    report($e);
                }
            }

            $userProject = $userProjectModel->create(
                [
                    'user_id' => auth()->user()->account_owner_id,
                    'project_id' => $project->id,
                    'company_id' => $requestValidated['company'],
                    'type' => 'producer',
                    'type_enum' => $userProjectModel->present()
                        ->getTypeEnum('producer'),
                    'access_permission' => 1,
                    'edit_permission' => 1,
                    'status' => 'active',
                    'status_flag' => $userProjectModel->present()
                        ->getStatusFlag('active'),
                ]
            );

            if (empty($userProject)) {
                if (!empty($amazonPath)) {
                    $amazonPath->deleteFile($project->photo);
                }
                $shipping->delete();
                $project->delete();

                return response()->json(['message' => 'Erro ao tentar salvar projeto'], 400);
            }

            $projectNotificationService = new ProjectNotificationService();
            $projectService = new ProjectService();

            $projectNotificationService->createProjectNotificationDefault($project->id);
            $projectService->createUpsellConfig($project->id);

            TaskService::setCompletedTask(auth()->user(), Task::find(Task::TASK_CREATE_FIRST_STORE));

            return response()->json(['message' => 'Projeto salvo com sucesso']);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao tentar salvar projeto'], 400);
        }
    }

    public function edit($id): JsonResponse
    {
        try {
            $user = User::with('companies')->find(auth()->user()->account_owner_id);

            $project = Project::with(
                [
                    'usersProjects',
                    'usersProjects.company' =>
                        function ($query) use ($user) {
                            $query->where('user_id', $user->account_owner_id);
                        }
                ]
            )->find(hashids_decode($id));

            activity()->on((new Project()))->tap(
                function (Activity $activity) use ($id) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                }
            )->log('Visualizou tela editar configurações do projeto ' . $project->name);

            $userProject = UserProject::where('user_id', $user->account_owner_id)
                ->where('project_id', hashids_decode($id))->first();
            $userProject = new UserProjectResource($userProject);

            $shopifyIntegrations = ShopifyIntegration::where('user_id', $user->account_owner_id)
                ->where('project_id', hashids_decode($id))->get();
            $shopifyIntegrations = ShopifyIntegrationsResource::collection($shopifyIntegrations);

            $companies = CompaniesSelectResource::collection($user->companies);

            if (Gate::allows('edit', [$project])) {
                $project = new ProjectsResource($project);

                return response()->json(compact('companies', 'project', 'userProject', 'shopifyIntegrations'));
            }
            return response()->json(['message' => 'Erro ao carregar configurações do projeto'], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Erro ao carregar configurações do projeto',
                ],
                400
            );
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $projectModel = new Project();
            $projectId = current(Hashids::decode($id));

            activity()->on($projectModel)->tap(
                function (Activity $activity) use ($projectId) {
                    $activity->log_name = 'deleted';
                    $activity->subject_id = $projectId;
                }
            )->log('deleted');

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
            report($e);

            return response()->json('Erro ao remover o projeto, tente novamente mais tarde', 400);
        }
    }

    public function update(ProjectUpdateRequest $request, $id): JsonResponse
    {
        try {
            $requestValidated = $request->validated();
            $projectModel = new Project();
            $userProjectModel = new UserProject();
            $amazonFileService = app(AmazonFileService::class);

            if (!$requestValidated) {
                return response()->json(['message' => 'Erro ao atualizar projeto'], 400);
            }

            $projectId = current(Hashids::decode($id));

            CacheService::forget(CacheService::CHECKOUT_PROJECT, $projectId);
            CacheService::forgetContainsUnique(CacheService::SHIPPING_RULES, $projectId);

            $project = $projectModel->find($projectId);

            if (!Gate::allows('update', [$project])) {
                return response()->json(['message' => 'Sem permissão para atualizar o projeto'], 403);
            }

            if ($requestValidated['installments_amount'] < $requestValidated['installments_interest_free']) {
                $requestValidated['installments_interest_free'] = $requestValidated['installments_amount'];
            }

            $requestValidated['status'] = 1;

            $requestValidated['invoice_description'] = FoxUtils::removeAccents(
                $requestValidated['invoice_description']
            );

            // $requestValidated['cost_currency_type'] = $project->present()->getCurrencyCost($requestValidated['cost_currency_type']);

            if (isset($requestValidated['finalizing_purchase_config_toogle']) && !empty($requestValidated['finalizing_purchase_config_toogle'])) {
                $array = [
                    'toogle' => $requestValidated['finalizing_purchase_config_toogle'],
                    'text' => $requestValidated['finalizing_purchase_config_text'],
                    'min_value' => $requestValidated['finalizing_purchase_config_min_value']
                ];

                $requestValidated['finalizing_purchase_configs'] = json_encode($array);
            } else {
                $requestValidated['finalizing_purchase_configs'] = null;
            }


            if (isset($requestValidated['checkout_notification_config_toogle']) && !empty($requestValidated['checkout_notification_config_toogle'])) {
                $messages = [];

                if (isset($requestValidated['checkout_notification_config_messages']) && !empty($requestValidated['checkout_notification_config_messages'])) {
                    foreach ($requestValidated['checkout_notification_config_messages'] as $config_message_key => $config_message_value) {
                        $messages[$config_message_key] = config(
                                'arrays.checkout_notification_config_messages'
                            )[$config_message_key] . '//' . $requestValidated['checkout_notification_config_messages_min_value'][$config_message_key];
                    }
                }

                $array = [
                    'toogle' => $requestValidated['checkout_notification_config_toogle'],
                    'time' => $requestValidated['checkout_notification_config_time'],
                    'mobile' => $requestValidated['checkout_notification_mobile'],
                    'messages' => $messages,
                ];

                $requestValidated['checkout_notification_configs'] = json_encode($array);
            } else {
                $requestValidated['checkout_notification_configs'] = null;
            }

            if (!empty($requestValidated['custom_message_switch'])) {
                $requestValidated['custom_message_configs'] = [
                    'active'=>true,
                    'title'=>$requestValidated['custom_message_title'],
                    'message'=>$requestValidated['custom_message_content']
                ];
            }else{
                $requestValidated['custom_message_configs'] = [
                    'active'=>false
                ];
            }

            $projectUpdate = $project->fill($requestValidated)->save();
            $projectChanges = $project->getChanges();
            if (isset($projectChanges["support_phone"])) {
                $project->fill(["support_phone_verified" => false])->save();
            }
            if (isset($projectChanges["contact"])) {
                $project->fill(["contact_verified" => false])->save();
            }
            if (!$projectUpdate) {
                return response()->json(['message' => 'Erro ao atualizar projeto'], 400);
            }

            try {
                $projectPhoto = $request->file('photo');
                if ($projectPhoto != null) {
                    $amazonFileService->deleteFile($project->photo);
                    $img = Image::make($projectPhoto->getPathname());
                    if (
                        !empty($requestValidated['photo_w']) && !empty($requestValidated['photo_h'])
                        && !empty($requestValidated['photo_x1']) && !empty($requestValidated['photo_y1'])
                    ) {
                        $img->crop(
                            $requestValidated['photo_w'],
                            $requestValidated['photo_h'],
                            $requestValidated['photo_x1'],
                            $requestValidated['photo_y1']
                        );
                    }
                    $img->resize(300, 300);
                    $img->save($projectPhoto->getPathname());

                    $amazonPath = $amazonFileService
                        ->uploadFile(
                            'uploads/user/' . Hashids::encode(
                                auth()->user()->account_owner_id
                            ) . '/public/projects/' . Hashids::encode($project->id) . '/main',
                            $projectPhoto
                        );
                    $project->update(
                        [
                            'photo' => $amazonPath,
                        ]
                    );
                }

                $projectLogo = $request->file('logo');
                if ($projectLogo != null) {
                    $amazonFileService->deleteFile($project->logo);
                    $img = Image::make($projectLogo->getPathname());

                    $img->resize(
                        null,
                        300,
                        function ($constraint) {
                            $constraint->aspectRatio();
                        }
                    );

                    $img->save($projectLogo->getPathname());

                    $amazonPathLogo = $amazonFileService
                        ->uploadFile(
                            'uploads/user/' . Hashids::encode(
                                auth()->user()->account_owner_id
                            ) . '/public/projects/' . Hashids::encode($project->id) . '/logo',
                            $projectLogo
                        );

                    $project->update(
                        [
                            'logo' => $amazonPathLogo,
                        ]
                    );
                }
            } catch (Exception $e) {
                report($e);

                return response()->json(['message' => 'Erro ao atualizar projeto'], 400);
            }

            $userProject = $userProjectModel->where(
                [
                    ['user_id', auth()->user()->account_owner_id],
                    ['project_id', $project->id],
                ]
            )->first();
            if (!empty($requestValidated['company_id'])) {
                $requestValidated['company_id'] = current(Hashids::decode($requestValidated['company_id']));

                if ($userProject->company_id != $requestValidated['company_id']) {
                    $old_company = $userProject->company;
                    $userProject->update(['company_id' => $requestValidated['company_id']]);
                    $new_company = Company::find($requestValidated['company_id']);

                    if ($old_company->has_pix_key != $new_company->has_pix_key) {
                        $boo_pix = $new_company->has_pix_key;
                        foreach ($new_company->usersProjects as $userProject) {
                            $project = $userProject->project;
                            $project->pix = $boo_pix;
                            $project->save();
                        }
                    }
                }
            }

            if (!empty($requestValidated['pix']) && $userProject->project->pix != $requestValidated['pix']) {
                $project = $userProject->project;
                $project->pix = $requestValidated['pix'];
                $project->save();
            }

            //ATUALIZA STATUS E VALOR DA RECOBRANÇA POR FALTA DE SALDO
            if (isset($projectChanges["discount_recovery_status"])) {
                $project->update(
                    [
                        'discount_recovery_status' => $requestValidated['discount_recovery_status'],
                        'discount_recovery_value' => $requestValidated['discount_recovery_value'],
                    ]
                );
            } else {
                $project->update(
                    [
                        'discount_recovery_status' => 0,
                    ]
                );
            }

            return response()->json(['message' => 'Projeto atualizado!'], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao atualizar projeto'], 400);
        }
    }

    public function updateSettings(ProjectsSettingsUpdateRequest $request, $id){
        return $request->all();
        try {
            $requestValidated = $request->validated();
            $projectModel = new Project();
            $userProjectModel = new UserProject();
            $amazonFileService = app(AmazonFileService::class);

            if (!$requestValidated) {
                return response()->json(['message' => 'Erro ao atualizar projeto'], 400);
            }

            $projectId = current(Hashids::decode($id));
            $project = $projectModel->find($projectId);

            if (!Gate::allows('update', [$project])) {
                return response()->json(['message' => 'Sem permissão para atualizar o projeto'], 403);
            }

            $requestValidated['status'] = 1;

            $projectUpdate = $project->update($requestValidated);
            if (!$projectUpdate) {
                return response()->json(['message' => 'Erro ao atualizar projeto'], 400);
            }

            return response()->json(['message' => 'Projeto atualizado!'], 200);

        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao atualizar projeto'], 400);
        }
    }

    public function show($id)
    {
        try {
            if (empty($id)) {
                return response()->json(['message' => 'Erro ao exibir detalhes do projeto'], 400);
            }

            $userId = auth()->user()->account_owner_id;
            $id = hashids_decode($id);

            $project = Project::where('id', $id)
                ->where('status', Project::STATUS_ACTIVE)
                ->with(
                    [
                        'affiliates' => function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        },
                        'usersProjects.company'
                    ]
                )->first();

            if (empty($project)) {
                return response()->json(['message' => 'Projeto não encontrado!'], 400);
            }

            $project->chargeback_count = Sale::where('project_id', $project->id)
                ->where('status', Sale::STATUS_CHARGEBACK)
                ->count();

            $project->without_tracking = Sale::where('project_id', $project->id)
                ->where('has_valid_tracking', false)
                ->whereNotNull('delivery_id')
                ->where('status', Sale::STATUS_APPROVED)
                ->count();

            $project->approved_sales = Sale::where('project_id', $project->id)
                ->where('status', Sale::STATUS_APPROVED)
                ->count();

            $project->approved_sales_value = Transaction::where('user_id', auth()->user()->account_owner_id)
                ->whereHas(
                    'sale',
                    function ($query) use ($project) {
                        $query->where('status', Sale::STATUS_APPROVED);
                        $query->where('project_id', $project->id);
                    }
                )
                ->sum('value');

            $project->open_tickets = Sale::where('project_id', $project->id)
                ->whereHas(
                    'tickets',
                    function ($query) {
                        $query->where('ticket_status_enum', Ticket::STATUS_OPEN);
                    }
                )
                ->count();

            $producer = User::whereHas(
                'usersProjects',
                function ($query) use ($project) {
                    $query->where('project_id', $project->id)
                        ->where('type_enum', UserProject::TYPE_PRODUCER_ENUM);
                }
            )->first();

            $project->producer = $producer->name ?? '';

            if (Gate::allows('show', [$project])) {
                activity()->on((new Project()))->tap(
                    function (Activity $activity) use ($id) {
                        $activity->log_name = 'visualization';
                        $activity->subject_id = $id;
                    }
                )->log('Visualizou o projeto ' . $project->name);

                return new ProjectsResource($project);
            }
            return response()->json(['message' => 'Erro ao exibir detalhes do projeto'], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao exibir detalhes do projeto'], 400);
        }
    }

    public function getProjects()
    {
        try {
            $projectService = new ProjectService();
            $projectModel = new Project();

            $projectStatus = [
                $projectModel->present()->getStatus('active'),
            ];

            return $projectService->getUserProjects(true, $projectStatus, true);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro, ao buscar dados das empresas'], 400);
        }
    }

    public function verifySupportphone($projectId, Request $request): JsonResponse
    {
        try {
            $projectModel = new Project();

            $data = $request->all();
            $supportPhone = $data["support_phone"] ?? null;
            if (FoxUtils::isEmpty($supportPhone)) {
                return response()->json(
                    [
                        'message' => 'Telefone não pode ser vazio!',
                    ],
                    400
                );
            }
            $project = $projectModel->find(Hashids::decode($projectId))->first();

            activity()->on($projectModel)->tap(
                function (Activity $activity) use ($projectId) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = current(Hashids::decode($projectId));
                }
            )
                ->log(
                    'Visualizou tela envio de código para verificação de telefone contato do projeto ' . $project->name
                );

            if ($supportPhone != $project->support_phone) {
                $project->support_phone = $supportPhone;
                $project->save();
            }

            $verifyCode = random_int(100000, 999999);

            $message = "Código de verificação CloudFox - " . $verifyCode;
            $smsService = new SmsService();
            $smsService->sendSms($supportPhone, $message, '', 'aws-sns');

            return response()->json(
                [
                    "message" => "Mensagem enviada com sucesso!",

                ],
                200
            )
                ->withCookie("supportphoneverifycode_" . Hashids::encode(auth()->id()) . $projectId, $verifyCode, 15);
        } catch (Exception $ex) {
            report($ex);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro ao enviar sms para o telefone informado!',
                ],
                400
            );
        }
    }

    public function matchSupportphoneVerifyCode($projectId, Request $request): JsonResponse
    {
        try {
            $projectModel = new Project();
            $project = $projectModel->where("id", current(Hashids::decode($projectId)))->first();

            activity()->on($projectModel)->tap(
                function (Activity $activity) use ($projectId) {
                    $activity->log_name = 'updated';
                    $activity->subject_id = current(Hashids::decode($projectId));
                }
            )->log('Validação código telefone de contato do projeto ' . $project->name);

            $data = $request->all();
            $verifyCode = $data["verifyCode"] ?? null;
            if (empty($verifyCode)) {
                return response()->json(
                    [
                        'message' => 'Código de verificação não pode ser vazio!',
                    ],
                    400
                );
            }
            $cookie = Cookie::get("supportphoneverifycode_" . Hashids::encode(auth()->id()) . $projectId);
            if ($verifyCode != $cookie) {
                return response()->json(
                    [
                        'message' => 'Código de verificação inválido!',
                    ],
                    400
                );
            }

            $project->update(["support_phone_verified" => true]);

            return response()->json(
                [
                    "message" => "Telefone verificado com sucesso!",
                ],
                200
            )
                ->withCookie(Cookie::forget("supportphoneverifycode_" . Hashids::encode(auth()->id())) . $projectId);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro ao verificar código!',
                ],
                400
            );
        }
    }

    public function verifyContact($projectId, Request $request): JsonResponse
    {
        try {
            $projectModel = new Project();
            $project = $projectModel->find(Hashids::decode($projectId))->first();

            activity()->on($projectModel)->tap(
                function (Activity $activity) use ($projectId) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = current(Hashids::decode($projectId));
                }
            )->log('Visualizou tela envio de codigo para verificação email contato do projeto: ' . $project->name);

            $data = $request->all();
            $contact = $data["contact"] ?? null;
            if (FoxUtils::isEmpty($contact)) {
                return response()->json(
                    [
                        'message' => 'Email não pode ser vazio!',
                    ],
                    400
                );
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
                'help@cloudfox.net',
                'cloudfox',
                $contact,
                auth()->user()->name,
                "d-5f8d7ae156a2438ca4e8e5adbeb4c5ac",
                $data
            );

            return response()->json(
                [
                    "message" => "Email enviado com sucesso!",

                ],
                200
            )
                ->withCookie("contactverifycode_" . Hashids::encode(auth()->id()) . $projectId, $verifyCode, 15);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, ao enviar o email com o código!',
                ],
                400
            );
        }
    }

    public function matchContactVerifyCode($projectId, Request $request): JsonResponse
    {
        try {
            $projectModel = new Project();
            $project = $projectModel->where("id", current(Hashids::decode($projectId)))->first();

            activity()->on($projectModel)->tap(
                function (Activity $activity) use ($projectId) {
                    $activity->log_name = 'updated';
                    $activity->subject_id = current(Hashids::decode($projectId));
                }
            )->log('Validação código email de contato do projeto: ' . $project->name);

            $data = $request->all();
            $verifyCode = $data["verifyCode"] ?? null;
            if (empty($verifyCode)) {
                return response()->json(
                    [
                        'message' => 'Código de verificação não pode ser vazio!',
                    ],
                    400
                );
            }
            $cookie = Cookie::get("contactverifycode_" . Hashids::encode(auth()->id()) . $projectId);
            if ($verifyCode != $cookie) {
                return response()->json(
                    [
                        'message' => 'Código de verificação inválido!',
                    ],
                    400
                );
            }

            $project->update(["contact_verified" => true]);

            return response()->json(
                [
                    "message" => "Email verificado com sucesso!",
                ],
                200
            )
                ->withCookie(Cookie::forget("contactverifycode_" . Hashids::encode(auth()->id())) . $projectId);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro ao verificar código!',
                ],
                400
            );
        }
    }

    public function updateOrder(Request $request): JsonResponse
    {
        try {
            $orders = $request->input('order');
            $page = $request->page ?? 1;
            $paginate = $request->paginate ?? 100;
            $initOrder = ($page * $paginate) - $paginate + 1;

            $projectIds = [];

            foreach ($orders as $order) {
                $projectIds[] = current(Hashids::decode($order));
            }

            $projects = UserProject::whereIn('project_id', collect($projectIds))
                ->where('user_id', auth()->user()->account_owner_id)
                ->get();

            $affiliates = Affiliate::whereIn('project_id', collect($projectIds))
                ->where('user_id', auth()->user()->account_owner_id)
                ->get();

            foreach ($projectIds as $value) {
                $project = $projects->firstWhere('project_id', $value);
                if (isset($project->id)) {
                    $project->update(['order_priority' => $initOrder]);
                } else {
                    $affiliate = $affiliates->firstWhere('project_id', $value);
                    if (isset($affiliate->id)) {
                        $affiliate->update(['order_priority' => $initOrder]);
                    }
                }
                $initOrder++;
            }

            return response()->json(['message' => 'Ordenação atualizada com sucesso'], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao atualizar ordenação'], 400);
        }
    }

    public function updateConfig(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $user = auth()->user();
            $updated = $user->update(
                [
                    'deleted_project_filter' => $data['deleted_project_filter'],
                ]
            );
            if ($updated) {
                return response()->json(['message' => 'Configuração atualizada com sucesso'], 200);
            } else {
                return response()->json(['message' => 'Erro ao atualizar configuração'], 400);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao atualizar configuração'], 400);
        }
    }
}
