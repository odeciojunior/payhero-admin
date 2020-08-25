<?php

namespace Modules\Register\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;
use Modules\Core\Entities\UserInformation;
use Modules\Core\Events\UserRegisteredEvent;
use Modules\Core\Services\SmsService;
use Modules\Register\Http\Requests\ValidateCnpjRequest;
use Modules\Register\Http\Requests\ValidateCpfRequest;
use Modules\Register\Http\Requests\ValidateEmailRequest;
use Modules\Register\Http\Requests\ValidatePhoneNumberRequest;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\UserNotification;
use Modules\Core\Services\BankService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\UserService;
use Modules\Core\Entities\Invitation;
use Modules\Core\Services\SendgridService;
use Modules\Register\Http\Requests\RegisterRequest;

/**
 * Class RegisterApiController
 * @package Modules\Register\Http\Controllers
 */
class RegisterApiController extends Controller
{
    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function store(RegisterRequest $request)
    {
        try {
            $requestData           = $request->validated();
            $userModel             = new User();
            $inviteModel           = new Invitation();
            $companyModel          = new Company();
            $userNotificationModel = new UserNotification();
            $userInformationModel   = new UserInformation();

            $parameter = $requestData['parameter'] ?? 'nw2usr3cfx';

            $withoutInvite = false;
            if ($parameter == 'nw2usr3cfx') {
                $withoutInvite = true;
            } else if (strlen($parameter) > 15) {
                $inviteId = substr($parameter, 0, 15);
                $inviteId = Hashids::decode($inviteId);
                $invite   = $inviteModel->where('email_invited', $requestData['email'])->where('id', $inviteId)
                                        ->first();
                if (!isset($invite->id) || (isset($invite->id) && $invite->status != 2)) {
                    return response()->json(['success' => 'false', 'message' => 'Convite inválido!']);
                }
            } else {
                $companyId = Hashids::decode($parameter);
                $company   = $companyModel->where('id', $companyId)->first();
                if (isset($company->id)) {
                    $invitesSent    = $inviteModel->where('invite', $company->user_id)->count();
                    $companyService = new CompanyService();
                    if (!$companyService->isDocumentValidated($company->id)) {
                        return response()->json(['success' => 'false', 'message' => 'Convite indisponivel!']);
                    }

                    if ($invitesSent >= $company->user->invites_amount) {
                        return response()->json(
                            ['success' => 'false', 'message' => 'Convite indisponivel, limite atingido!']
                        );
                    }
                } else {
                    return response()->json(['success' => 'false', 'message' => 'Link convite inválido']);
                }
            }

            if (!stristr($requestData['date_birth'], '-')) {
                $requestData['date_birth'] = null;
            }

            $requestData['password']                            = bcrypt($requestData['password']);
            $requestData['percentage_rate']                     = '5.9';
            $requestData['transaction_rate']                    = '1.00';
            $requestData['balance']                             = '0';
            $requestData['credit_card_antecipation_money_days'] = '30';
            $requestData['boleto_antecipation_money_days']      = '2';
            $requestData['antecipation_tax']                    = '0';
            $requestData['percentage_antecipable']              = '100';
            $requestData['invites_amount']                      = 1;
            $requestData['boleto_release_money_days']           = 0;

            $user = $userModel->create($requestData);

            $user->update(['account_owner_id' => $user->id]);

            $user->assignRole('account_owner');

            $streetCompany       = $requestData['street_company'] ?? null;
            $numberCompany       = $requestData['number_company'] ?? null;
            $neighborhoodCompany = $requestData['neighborhood_company'] ?? null;
            $complementCompany   = $requestData['complement_company'] ?? null;
            $stateCompany        = $requestData['state_company'] ?? null;
            $cityCompany         = $requestData['city_company'] ?? null;
            $supportEmail        = $requestData['support_email'] ?? null;
            $supportPhone        = $requestData['support_telephone'] ?? null;

            $companyModel->create(
                [
                    'user_id'           => $user->account_owner_id,
                    'fantasy_name'      => ($requestData['company_type'] == $companyModel->present()
                                                                                         ->getCompanyType('physical person')) ? $user->name : $requestData['fantasy_name'],
                    'company_document'  => ($requestData['company_type'] == $companyModel->present()
                                                                                         ->getCompanyType(
                                                                                             'physical person'
                                                                                         )) ? $requestData['document'] : $requestData['company_document'],
                    'company_type'      => $requestData['company_type'],
                    'support_email'     => $supportEmail,
                    'support_telephone' => $supportPhone,
                    'street'            => $streetCompany,
                    'number'            => $numberCompany,
                    'neighborhood'      => $neighborhoodCompany,
                    'complement'        => $complementCompany,
                    'state'             => $stateCompany,
                    'city'              => $cityCompany,
                    'bank'              => $requestData['bank'],
                    'agency'            => $requestData['agency'],
                    'agency_digit'      => $requestData['agency_digit'],
                    'account'           => $requestData['account'],
                    'account_digit'     => $requestData['account_digit'],
                ]
            );

            if (!empty($user)) {
                $user->load(["userNotification"]);
                $userNotification = $user->userNotification ?? collect();
                if ($userNotification->isEmpty()) {
                    $userNotificationModel->create(
                        [
                            "user_id" => $user->id,
                        ]
                    );
                }
                $userInformationModel->create(
                    [
                        "user_id"         => $user->id,
                        "document_type"   => 1,
                        "document_number" => $user->document,
                    ]
                );
            }

            auth()->loginUsingId($user->id, true);

            if ($withoutInvite == false) {
                if (!isset($invite)) {
                    $invite = $inviteModel->where('email_invited', $requestData['email'])->first();
                }
                // $company = $companyModel->find(current(Hashids::decode($requestData['parameter'])));

                if ($invite) {
                    $invite->update(
                        [
                            'user_invited'    => $user->account_owner_id,
                            'status'          => '1',
                            'register_date'   => Carbon::now()->format('Y-m-d'),
                            'expiration_date' => Carbon::now()->addMonths(12)->format('Y-m-d'),
                            'email_invited'   => $requestData['email'],
                        ]
                    );

                    if (empty($invite->invite) && isset($company->id)) {
                        $invite->update(
                            [
                                'invite' => $company->user_id,
                            ]
                        );
                    }
                } else {
                    if ($company) {
                        $inviteModel->create(
                            [
                                'invite'          => $company->user_id,
                                'user_invited'    => $user->account_owner_id,
                                'status'          => '1',
                                'company_id'      => $company->id,
                                'register_date'   => Carbon::now()->format('Y-m-d'),
                                'expiration_date' => Carbon::now()->addMonths(12)->format('Y-m-d'),
                                'email_invited'   => $requestData['email'],
                            ]
                        );
                    }
                }
            }

            /**
             * Event Send Welcome Email
             */
            $dataEmail = [
                'domainName' => $requestData['domainName'] ?? 'cloudfox.net',
                'clientName' => $requestData['name'] ?? '',
                'clientEmail' => $requestData['email'],
                'templateId' => 'd-267dbdcbcc5a454e94a5ae3ffb704505',
                'bodyEmail' => $requestData,
            ];

            event(new UserRegisteredEvent($dataEmail));

            return response()->json(
                [
                    'success'      => 'true',
                    'access_token' => auth()->user()
                                            ->createToken("Laravel Password Grant Client")->accessToken,
                ]
            );
        } catch (Exception $ex) {
            report($ex);

            return response()->json(['success' => 'false', 'message' => 'revise os dados informados']);
        }
    }

    /**
     * @param ValidateCpfRequest $request
     * @return JsonResponse
     */
    public function verifyCpf(ValidateCpfRequest $request)
    {
        $data        = $request->all();
        $userService = new UserService();
        $cpf         = $userService->verifyCpf($data['document']);
        if ($cpf) {
            return response()->json(
                [
                    'cpf_exist' => 'true',
                    'message'   => 'Esse CPF já está cadastrado na plataforma',
                ]
            );
        } else {
            return response()->json(
                [
                    'cpf_exist' => 'false',
                ]
            );
        }
    }

    /**
     * @param ValidateCnpjRequest $request
     * @return JsonResponse
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function verifyCnpj(ValidateCnpjRequest $request)
    {
        $data           = $request->all();
        $companyService = new CompanyService();
        $cnpj           = $companyService->verifyCnpj($data['company_document']);
        if ($cnpj) {
            return response()->json(
                [
                    'cnpj_exist' => 'true',
                    'message'    => 'Esse CNPJ já está cadastrado na plataforma',
                ]
            );
        } else {
            return response()->json(
                [
                    'cnpj_exist' => 'false',
                ]
            );
        }
    }

    /**
     * @param ValidateEmailRequest $request
     * @return JsonResponse
     */
    public function verifyEmail(ValidateEmailRequest  $request)
    {
        $data      = $request->all();
        $userModel = new User();

        $user = $userModel->where('email', 'like', '%' . $data['email'] . '%')->first();
        if (!empty($user)) {
            return response()->json(
                [
                    'email_exist' => 'true',
                    'message'     => 'Esse Email já está cadastrado na plataforma',
                ]
            );
        } else {
            return response()->json(
                [
                    'email_exist' => 'false',
                ]
            );
        }
    }

    /**
     * @param ValidateEmailRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function sendEmailCode(ValidateEmailRequest $request) {

        $data = $request->all();
        $email = $data["email"] ?? null;

        $verifyCode = random_int(100000, 999999);
        $data = [
            "verify_code" => $verifyCode,
        ];

        /** @var SendgridService $sendgridService */
        $sendgridService = app(SendgridService::class);

        if ($sendgridService->sendEmail(
            'noreply@cloudfox.net',
            'cloudfox',
            $email,
            '$data[\'firstname\']',
            "d-5f8d7ae156a2438ca4e8e5adbeb4c5ac",
            $data
        )) {
                return response()->json(
                    [
                        "sent" => true,
                        "message" => "Email enviado com sucesso!",
                    ],
                    200
                )
                    ->withCookie("emailverifycode", $verifyCode, 15);
            }

        return response()->json(
            [
                "message" => "Erro ao enviar email, tente novamente mais tarde!",
            ],
            400
        );
    }

    /**
     * @param ValidateEmailRequest $request
     * @return JsonResponse
     */
    public function matchEmailVerifyCode(ValidateEmailRequest $request)
    {
        try {
            $data = $request->all();
            $verifyCode = $data["verifyCode"] ?? null;

            $cookie = Cookie::get("emailverifycode");
            if ($verifyCode != $cookie) {
                return response()->json(
                    [
                        'message' => 'Código de verificação inválido!',
                    ],
                    400
                );
            }

            return response()->json(
                [
                    'checked' => true,
                    "message" => "Email verificado com sucesso!",
                ],
                200
            )
                ->withCookie(Cookie::forget("emailverifycode"));
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro'
                ],
                403);
        }
    }

    /**
     * @param ValidatePhoneNumberRequest $request
     * @return JsonResponse
     */
    public function sendCellphoneCode(ValidatePhoneNumberRequest  $request)
    {
        try {
            $data = $request->all();
            $cellphone = $data["cellphone"] ?? null;
            if (FoxUtils::isEmpty($cellphone)) {
                return response()->json(
                    [
                        'message' => 'Telefone não pode ser vazio!',
                    ],
                    400
                );
            }

            $verifyCode = random_int(100000, 999999);

            $cellphone = preg_replace("/[^0-9]/", "", $cellphone);

            $message = "Código de verificação CloudFox - " . $verifyCode;
            $smsService = new SmsService();
            $smsService->sendSms($cellphone, $message, ' ', 1);

            return response()->json(
                [
                    "sent" => true,
                    "message" => "Mensagem enviada com sucesso!",
                ],
                200
            )
                ->withCookie("cellphoneverifycode", $verifyCode, 15);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro'], 403);
        }
    }

    /**
     * @param ValidatePhoneNumberRequest $request
     * @return JsonResponse
     */
    public function matchCellphoneVerifyCode(ValidatePhoneNumberRequest $request)
    {
        try {
            $data = $request->all();
            $verifyCode = $data["verifyCode"] ?? null;

            $cookie = Cookie::get("cellphoneverifycode");

            if ($verifyCode != $cookie) {
                return response()->json(
                    [
                        'message' => 'Código de verificação inválido!',
                    ],
                    400
                );
            }

            return response()->json(
                [
                    'checked' => true,
                    "message" => "Telefone verificado com sucesso!",
                ],
                200
            )
                ->withCookie(Cookie::forget("cellphoneverifycode"));
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro'], 403);
        }
    }

    /**
     * @return Application|ResponseFactory|Response
     */
    public function getBanks()
    {
        $bankService = new BankService();
        $banks       = $bankService->getBanks('brazil');

        return response(
            [
                'banks' => $banks,
            ],
            200
        );
    }
}
