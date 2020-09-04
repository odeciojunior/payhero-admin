<?php

namespace Modules\Register\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Entities\UserInformation;
use Modules\Core\Events\UserRegisteredEvent;
use Modules\Core\Services\SmsService;
use Modules\Register\Http\Requests\ValidateCnpjRequest;
use Modules\Register\Http\Requests\ValidateCpfRequest;
use Modules\Register\Http\Requests\ValidateEmailRequest;
use Modules\Register\Http\Requests\ValidatePhoneNumberRequest;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\UserNotification;
use Modules\Core\Services\BankService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\UserService;
use Modules\Core\Services\SendgridService;
use Modules\Register\Http\Requests\RegisterRequest;

/**
 * Class RegisterApiController
 * @package Modules\Register\Http\Controllers
 */
class RegisterApiController extends Controller
{

    const PERCENTAGE_RATE = '5.9';
    const TRANSACTION_RATE = '1.00';
    const BALANCE = '0';
    const CREDIT_CARD_ANTECIPATION_MONEY_DAYS = '30';
    const BOLETO_ANTECIPATION_MONEY_DAYS = '2';
    const ANTECIPATION_TAX = '0';
    const PERCENTAGE_ANTECIPABLE = '100';
    const INVITES_AMOUNT = 1;
    const BOLETO_RELEASE_MONEY_DAYS = 0;

    /**
     * @param RegisterRequest $request
     * @param User $userModel
     * @param Company $companyModel
     * @param UserNotification $userNotificationModel
     * @param UserInformation $userInformationModel
     * @return JsonResponse
     */
    public function store(RegisterRequest $request,
                          User $userModel,
                          Company $companyModel,
                          UserNotification $userNotificationModel,
                          UserInformation $userInformationModel)
    {

        try {

            $requestData = $this->createAndPassDefaultValuesToRequest($request);
            $user = $this->createUserAndAssignRole($requestData, $userModel);
            $this->createCompanyToUser($requestData, $companyModel, $user);

            if (!empty($user)) {
                $this->sendUserNotification($user, $userNotificationModel, $userInformationModel);
            }

            if (env('APP_ENV') == 'local')
                $sdrive = Storage::disk('local');
            else
                $sdrive = Storage::disk('s3');

            if ($files = $sdrive->allFiles('uploads/register/user/pedro/' . $user->document . '/private/documents')) {
                if (!app(RegisterController::class)->uploudDocumentsRegistered($files)) {
                    return response()->json(
                        [
                            'success' => 'false',
                            'message' => 'Não foi Possivel enviar os arquivos ao servidor, favor verificar os arquivos
                                          no Perfil do usuário e da empresa',
                        ]
                    );
                }
            }

            $this->sendWelcomeEmail($requestData);

            return response()->json(
                [
                    'success'       => 'true',
                    'message'       => 'Arquivos Enviado com Sucesso',
                    'access_token'  => auth()->user()->createToken("Laravel Password Grant Client")->accessToken,
                ]
            );

        } catch (Exception $ex) {
            report($ex);

            return response()->json(['success' => 'false', 'message' => 'revise os dados informados'], 403);
        }
    }

    /**
     * @param ValidateCpfRequest $request
     * @return JsonResponse
     */
    public function verifyCpf(ValidateCpfRequest $request)
    {
        $data = $request->validated();
        $userService = new UserService();

        $isValidCpf = $userService->verifyIsValidCPF($data['document']);

        if (!$isValidCpf) {

            return response()->json(
                [
                    'cpf_exist' => 'false',
                    'message' => 'Cpf com formato inválido',
                ], 403
            );

        }

        $cpf = $userService->verifyCpf($data['document']);
        if ($cpf) {
            return response()->json(
                [
                    'cpf_exist' => 'true',
                    'message' => 'Esse CPF já está cadastrado na plataforma',
                ], 403
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

        $data = $request->validated();
        $companyService = new CompanyService();

        $isAValidCNPJ = $companyService->verifyIsValidCNPJ($data['company_document']);

        if (!$isAValidCNPJ) {
            return response()->json(
                [
                    'cnpj_exist' => 'FALSE',
                    'message' => 'CNPJ com formato inválido',
                ], 403
            );
        }

        $cnpj = $companyService->verifyCnpj($data['company_document']);

        if ($cnpj) {
            return response()->json(
                [
                    'cnpj_exist' => 'true',
                    'message' => 'Esse CNPJ já está cadastrado na plataforma',
                ], 403
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
    public function verifyEmail(ValidateEmailRequest $request)
    {
        $data = $request->validated();
        $userModel = new User();

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {

            return response()->json(
                [
                    'email_exist' => 'false',
                    'message' => 'Email com formato inválido',
                ], 403
            );

        }

        if ($data['email'] == 'kim@mail.com' || $userModel->where('email', $data['email'])->count()) {

            return response()->json(
                [
                    'email_exist' => 'true',
                    'message' => 'Esse Email já está cadastrado na plataforma',
                ], 403
            );

        }

        return response()->json(
            [
                'email_exist' => 'false',
            ], 200
        );

    }

    /**
     * Verifica o tipo de documento para usar respectiovo método de uploud
     * @param Request $request
     *
     * [Remover]
     */
    public function uploudDocumentTo(Request $request)
    {
        $dataForm = Validator::make($request->all(), [
            // Usuário
            'personal_document' => 'required|image|mimes:jpeg,jpg,png,doc,pdf',
            'address_document' => 'required|image|mimes:jpeg,jpg,png,doc,pdf',

            // Empresa
            'bank_document' => 'required|image|mimes:jpeg,jpg,png,doc,pdf',
            'company_address_document' => 'required|image|mimes:jpeg,jpg,png,doc,pdf',
            'contract_document' => 'required|image|mimes:jpeg,jpg,png,doc,pdf',
        ], [
            'personal_document.mimes' => 'Arquivo com formato inválido',
            'personal_document.required' => 'Precisamos do arquivo Para continuar',
        ])->validate();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadDocuments(Request $request)
    {
        try {
            $dataForm = Validator::make($request->all(), [
                'fileToUpload'  => 'required|image|mimes:jpeg,jpg,png,doc,pdf',
                'document'      => 'required',
            ], [
                'fileToUpload.mimes'     => 'O arquivo esta com formato inválido',
                'fileToUpload.required'  => 'Precisamos do arquivo para continuar',
            ])->validate();

            if (env('APP_ENV') == 'local')
                $sdrive = Storage::disk('local');
            else
                $sdrive = Storage::disk('s3');

            $document = $request->file('fileToUploud');
            $documentType = $document->getClientOriginalName() ?? '';

            $sdrive->putFileAs('uploads/register/user/pedro/'. $dataForm['document'] .'/private/documents',
                $document ,
                $documentType,
            'public');

            if(empty($documentType)) {
                return response()->json(['message' => 'Não foi possivel enviar o arquivo.'], 400);
            }


            return response()->json(
                [
                    'message' => 'Arquivo enviado com sucesso.',
                ],
                200
            );
        } catch (Exception $e) {
            Log::warning('RegisterApiController uploadDocuments');
            report($e);

            return response()->json(['message' => 'Não foi possivel enviar o arquivo.'], 400);
        }
    }

    /**
     * @param ValidateEmailRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function sendEmailCode(ValidateEmailRequest $request)
    {

        $data = $request->validated();
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
            $data = $request->validated();
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
    public function sendCellphoneCode(ValidatePhoneNumberRequest $request)
    {
        try {
            $data = $request->validated();
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
            $data = $request->validated();
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
        $banks = $bankService->getBanks('brazil');

        return response(
            [
                'banks' => $banks,
            ],
            200
        );
    }

    /**
     * @param $request
     * @return mixed
     */
    private function createAndPassDefaultValuesToRequest($request)
    {
        $requestData = $request->validated();

        $requestData['percentage_rate'] = self::PERCENTAGE_RATE;
        $requestData['transaction_rate'] = self::TRANSACTION_RATE;
        $requestData['balance'] = self::BALANCE;
        $requestData['credit_card_antecipation_money_days'] = self::CREDIT_CARD_ANTECIPATION_MONEY_DAYS;
        $requestData['boleto_antecipation_money_days'] = self::BOLETO_ANTECIPATION_MONEY_DAYS;
        $requestData['antecipation_tax'] = self::ANTECIPATION_TAX;
        $requestData['percentage_antecipable'] = self::PERCENTAGE_ANTECIPABLE;
        $requestData['invites_amount'] = self::INVITES_AMOUNT;
        $requestData['boleto_release_money_days'] = self::BOLETO_RELEASE_MONEY_DAYS;

        if (!stristr($requestData['date_birth'], '-')) {
            $requestData['date_birth'] = null;
        }

        $requestData['password'] = bcrypt($requestData['password']);

        return $requestData;
    }

    /**
     * @param $requestData
     * @param User $userModel
     * @return mixed
     */
    private function createUserAndAssignRole($requestData, User $userModel)
    {

        $user = $userModel->create($requestData);
        $user->update(['account_owner_id' => $user->id]);
        $user->assignRole('account_owner');
        return $user;

    }

    /**
     * @param $requestData
     * @param Company $companyModel
     * @param User $user
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    private function createCompanyToUser($requestData, Company $companyModel, User $user): void
    {

        $streetCompany = $requestData['street_company'] ?? null;
        $numberCompany = $requestData['number_company'] ?? null;
        $neighborhoodCompany = $requestData['neighborhood_company'] ?? null;
        $complementCompany = $requestData['complement_company'] ?? null;
        $stateCompany = $requestData['state_company'] ?? null;
        $cityCompany = $requestData['city_company'] ?? null;
        $supportEmail = $requestData['support_email'] ?? null;
        $supportPhone = $requestData['support_telephone'] ?? null;

        $companyModel->create(
            [
                'user_id' => $user->account_owner_id,
                'fantasy_name' => ($requestData['company_type'] == $companyModel->present()
                        ->getCompanyType('physical person')) ? $user->name : $requestData['fantasy_name'],
                'company_document' => ($requestData['company_type'] == $companyModel->present()
                        ->getCompanyType(
                            'physical person'
                        )) ? $requestData['document'] : $requestData['company_document'],
                'company_type' => $requestData['company_type'],
                'support_email' => $supportEmail,
                'support_telephone' => $supportPhone,
                'street' => $streetCompany,
                'number' => $numberCompany,
                'neighborhood' => $neighborhoodCompany,
                'complement' => $complementCompany,
                'state' => $stateCompany,
                'city' => $cityCompany,
                'bank' => $requestData['bank'],
                'agency' => $requestData['agency'],
                'agency_digit' => $requestData['agency_digit'],
                'account' => $requestData['account'],
                'account_digit' => $requestData['account_digit'],
            ]
        );
    }

    /**
     * @param User $user
     * @param UserNotification $userNotificationModel
     * @param UserInformation $userInformationModel
     */
    private function sendUserNotification(User $user, UserNotification $userNotificationModel, UserInformation $userInformationModel): void
    {
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
                "user_id" => $user->id,
                "document_type" => 1,
                "document_number" => $user->document,
            ]
        );

    }

    /**
     * @param $requestData
     */
    private function sendWelcomeEmail($requestData)
    {
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
    }
}
