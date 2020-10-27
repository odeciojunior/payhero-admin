<?php

namespace Modules\Register\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Facades\Agent;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Entities\UserInformation;
use Modules\Core\Entities\UserTerms;
use Modules\Core\Events\UserRegisteredEvent;
use Modules\Core\Services\IdwallService;
use Modules\Core\Services\IpService;
use Modules\Core\Services\SmsService;
use Modules\Register\Entities\RegistrationToken;
use Modules\Register\Http\Requests\ValidateCnpjRequest;
use Modules\Register\Http\Requests\ValidateCpfRequest;
use Modules\Register\Http\Requests\ValidateEmailRequest;
use Modules\Register\Http\Requests\ValidateEmailTokenRequest;
use Modules\Register\Http\Requests\ValidatePhoneNumberRequest;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\UserNotification;
use Modules\Core\Services\BankService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\UserService;
use Modules\Core\Services\SendgridService;
use Modules\Register\Http\Requests\RegisterRequest;
use Modules\Register\Http\Requests\ValidatePhoneNumberTokenRequest;
use Vinkla\Hashids\Facades\Hashids;

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
    const VERIFIED_EMAIL = 1;
    const VERIFIED_CELLPHONE = 1;

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

            \DB::beginTransaction();

            $requestData = $this->createAndPassDefaultValuesToRequest($request);
            $user = $this->createUserAndAssignRole($requestData, $userModel);
            $this->createCompanyToUser($requestData, $companyModel, $user);

            /**
             *  VALIDAÇÕES
             */

            if (!empty($requestData['parameter'])) {
                if ($invitation = $this->verifyInvitation($user,$requestData)) {
                    if ($invitation['success'] == 'false')
                        return response()->json(
                            [
                                'success' => 'false',
                                'message' => $invitation['message']
                            ]
                        );
                }
            }

            if (!$files = $this->verifyFiles($request['document'], $request['company_type'])) {
                return response()->json(
                    [
                        'success' => 'false',
                        'message' => 'Existem Arquivos Pendentes, favor preencher todos os campos com os arquivos.'
                    ] , 403
                );
            }

            if (!$this->verifyTokenValidate($user)) {
                return response()->json(
                    [
                        'success' => 'false',
                        'message' => 'Favor confirme seu email e/ou seu celular para podermos finalizar o cadastro.'
                    ], 400
                );
            }

            if (!$this->acceptedTerms($user)) {
                return response()->json(
                    [
                        'success' => 'false',
                        'message' => 'Ocorreu um erro, tente novamente!'
                    ], 400
                );
            }

            if (!empty($user)) {
                $this->sendUserNotification($user, $userNotificationModel, $userInformationModel);
            }

            if (!$this->uploadDocumentsRegistered($files, $user)) {
                return response()->json(
                    [
                        'success' => 'false',
                        'message' => 'Não foi possivel enviar os arquivos ao servidor.'
                    ], 400
                );
            }

            Storage::disk('s3')->deleteDirectory('uploads/register/user/' . $user->document);

            if (env('APP_ENV') == 'production') {
                return response()->json([
                    'success' => 'false',
                    'message' => 'No momento não é possível se cadastrar em nosso sistema, aguarde a liberação para o cadastro.',
                ], 403);
            } else {
                \DB::commit();
                $this->sendWelcomeEmail($requestData);

                return response()->json(
                    [
                        'success' => 'true',
                        'message' => 'Arquivos Enviado com Sucesso',
                        'access_token' => base64_encode(Crypt::encrypt($user->id)),
                    ], 200
                );
            }

        } catch (Exception $ex) {
            \DB::rollback();
            report($ex);
            return response()->json(['success' => 'false', 'message' => 'revise os dados informados',
                'mensagem_de_erro' => $ex->getMessage()], 403);
        }
    }

    /**
     * @param $user
     * @param $requestData
     * @return bool|string[]
     */
    public function verifyInvitation($user, $requestData)
    {

        $inviteModel = new Invitation();
        $companyModel = new Company();

        $parameter = $requestData['parameter'];
        $companyId = current(Hashids::decode($parameter));
        $company   = $companyModel->where('id', $companyId)->first();

        $withoutInvite = false;

        try {

            if (strlen($parameter) > 15) {

                $inviteId = substr($parameter, 0, 15);
                $inviteId = Hashids::decode($inviteId);
                $invite   = $inviteModel->where('email_invited', $requestData['email'])->where('id', $inviteId)
                    ->first();

                if (!isset($invite->id) || (isset($invite->id) && $invite->status != 2))
                    return [
                        'success' => 'false',
                        'message' => 'Convite inválido!'
                    ];

            } else {

                if (isset($company->id)) {
                    $companyService = new CompanyService();

                    if (!$companyService->isDocumentValidated($company->id)) {

                        return [
                            'success' => 'false',
                            'message' => 'Convite indisponivel!'
                        ];

                    }

                } else {

                    return [
                        'success' => 'false',
                        'message' => 'Registro sem convite'
                    ];
                }
            }

            if ($withoutInvite == false) {

                if (!isset($invite))
                    $invite = $inviteModel->where('email_invited', $requestData['email'])->first();

                if ($invite) {

                    $invite->update(
                        [
                            'user_invited'    => $user->account_owner_id,
                            'status'          => '1',
                            'register_date'   => Carbon::now()->format('Y-m-d'),
                            'expiration_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
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

            return [
                'success' => 'true',
                'message' => 'Link convite válido'
            ];

        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    /**
     * @param $document
     * @param $company_type
     * @return array|false
     * @throws PresenterException
     */
    public function verifyFiles($document, $company_type)
    {
        $companyModel = new Company();
        $companyPresent = $companyModel->present();
        $is_physical_person = $companyPresent->getCompanyType($company_type) == 'physical person' ? 1 : 2;

        $sDrive = Storage::disk('s3_documents');
        $documentCpf = preg_replace('/[^0-9]/', '', $document);
        $files = $sDrive->allFiles('uploads/register/user/' . $documentCpf . '/private/documents');

        if ($is_physical_person == 1 && !count($files) == 3) {
            return false;
        }

        if ($is_physical_person == 2 && !count($files) == 5) {
            return false;
        }

        return $files;
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
                    'message' => 'CPF com formato inválido',
                ], 403
            );

        }

        $cpf = $userService->verifyExistsCPF($data['document']);
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
     */
    public function verifyCnpj(ValidateCnpjRequest $request)
    {
        $requestData = current(preg_replace('/[^0-9]/', '', $request->validated()));
        $companyService = new CompanyService();
        $company = $companyService->getCompanyByIdwallCNPJ($requestData);

        if (empty($company)) {
            return response()->json(
                [
                    'message' => 'CNPJ inválido',
                ], 403
            );
        }

        if (empty($company['result']['cnpj']))  {
            return response()->json(
                [
                    'message' => 'CNPJ rejeitado pela Receita Federal',
                ], 403
            );
        }


        return response()->json(
            [
                'cnpj_exist' => 'false',
                'protocol' => $company['result']['numero'],
            ], 200
        );

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

        if ($userModel->where('email', $data['email'])->count()) {

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
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadDocuments(Request $request)
    {
        $dataForm = Validator::make($request->all(), [
            'fileToUpload' => 'required|max:10000|mimes:jpeg,jpg,png,pdf',
            'document_type' => 'required|in:USUARIO_DOCUMENTO,USUARIO_RESIDENCIA,EMPRESA_CCMEI,EMPRESA_EXTRATO,EMPRESA_RESIDENCIA',
            'document' => 'required',
        ], [
            'fileToUpload.required' => 'Precisamos do arquivo para continuar',
            'fileToUpload.mimes' => 'O arquivo esta com formato inválido',
            'document_type.required' => 'Precisamos do saber o tipo do documento',
            'document_type.in' => 'Tipo de documento Inválido',
            'document.required' => 'Precisamos do CPF para continuar',
            'fileToUpload.max' => 'Arquivo excede do tamanho de 10MB',
        ])->validate();

        try {
            $sDrive = Storage::disk('s3_documents');

            $document = $dataForm['fileToUpload'];
            $documentCpf = preg_replace('/[^0-9]/', '', $dataForm['document']);
            $documentRename = $dataForm['document_type'] . '.' . $document->extension();

            $sDrive->putFileAs('uploads/register/user/' . $documentCpf . '/private/documents',
                $document,
                $documentRename,
                'private');

            if (empty($documentRename)) {
                return response()->json(['message' => 'Não foi possivel enviar o arquivo.'], 400);
            }

            $urlPath = $sDrive->temporaryUrl(
                'uploads/register/user/' . $documentCpf . '/private/documents/' . $documentRename,
                now()->addHours(24)
            );
            return response()->json(
                [
                    'message' => 'Arquivo enviado com sucesso.',
                    'path' => $urlPath,
                    'fileName' => $document->getClientOriginalName(),
                    'fileType' => $document->extension()
                ],
                200
            );
        } catch (Exception $e) {
            Log::warning('RegisterApiController uploadDocuments' . $e);
            report($e);

            return response()->json([
                'message' => 'Não foi possivel enviar o arquivo.',
            ], 400);
        }
    }

    /**
     * @param array $files
     * @param User $user
     * @return bool
     */
    public function uploadDocumentsRegistered(array $files, User $user)
    {
        $companyModel = new Company();
        $userDocument = new UserDocument();
        $companyDocumentModel = new CompanyDocument();

        $sDrive = Storage::disk('s3_documents');
        $company = $companyModel->where('user_id', $user->id)->first();

        try {
            foreach ($files as $file) {
                $fileName = explode('/', $file)[6];
                $fileTypeName = preg_split('/[\/.]/', $file)[6];
                $fileType = $user->present()->getDocumentTypeRegistered($fileTypeName);

                if (!$sDrive->exists($file)) {
                    return false;
                }

                /**
                 * Uploud Usuário
                 */
                if (in_array($fileTypeName, ['USUARIO_RESIDENCIA', 'USUARIO_DOCUMENTO', 'USUARIO_EXTRATO'])) {
                    $amazonPathUser = 'uploads/user/' . Hashids::encode($user->id) . '/private/documents/' . $fileName;
                    if ($sDrive->exists($amazonPathUser)) {
                        $sDrive->delete($amazonPathUser);
                    }
                    $sDrive->move(
                        $file,
                        $amazonPathUser
                    );
                    $amazonPathUrlUser = $sDrive->url($amazonPathUser);

                    /**
                     * Salva status do documentos no Banco | (Usuário)
                     */
                    $userDocument->create(
                        [
                            'user_id' => $user->id,
                            'document_url' => $amazonPathUrlUser,
                            'document_type_enum' => $fileType,
                            'status' => $userDocument->present()->getTypeEnum('analyzing'),
                        ]
                    );

                    if ($fileType == $user->present()->getDocumentType('personal_document')) {
                        $user->update(
                            [
                                'personal_document_status' => $user->present()
                                    ->getPersonalDocumentStatus('analyzing'),
                            ]
                        );
                    }

                    if ($fileType == $user->present()->getDocumentType('address_document')) {
                        $user->update(
                            [
                                'address_document_status' => $user->present()
                                    ->getPersonalDocumentStatus('analyzing'),
                            ]
                        );
                    }
                }

                /**
                 * Uploud Empresa
                 */
                if (in_array($fileTypeName, ['EMPRESA_EXTRATO', 'EMPRESA_RESIDENCIA', 'EMPRESA_CCMEI'])) {
                    $amazonPathCompanies = 'uploads/user/' . $user->id . '/companies/' . $company->id . '/private/documents/' . $fileName;
                    $sDrive->move(
                        $file,
                        $amazonPathCompanies
                    );
                    $amazonPathUrlcompany = $sDrive->url($amazonPathCompanies);

                    /**
                     * Salva status do documentos no Banco | (Empresa)
                     */
                    $companyDocumentModel->create(
                        [
                            'company_id' => $company->id,
                            'document_url' => $amazonPathUrlcompany,
                            'document_type_enum' => $fileType,
                            'status' => $companyDocumentModel->present()->getTypeEnum('analyzing'),
                        ]
                    );

                    if ($fileType == $company->present()->getDocumentType('bank_document_status')) {
                        $company->update(
                            [
                                'bank_document_status' => $companyDocumentModel->present()
                                    ->getTypeEnum('analyzing'),
                            ]
                        );
                    }
                    if ($fileType == $company->present()->getDocumentType('address_document_status')) {
                        $company->update(
                            [
                                'address_document_status' => $companyDocumentModel->present()
                                    ->getTypeEnum('analyzing'),
                            ]
                        );
                    }
                    if ($fileType == $company->present()->getDocumentType('contract_document_status')) {
                        $company->update(
                            [
                                'contract_document_status' => $companyDocumentModel->present()
                                    ->getTypeEnum('analyzing'),
                            ]
                        );
                    }
                }
            }
        } catch (Exception $e) {
            Log::warning('RegisterApiController uploadDocumentsRegistered');

            report($e);
            return false;
        }
        return true;
    }

    /**
     * @param ValidateEmailRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function sendEmailCode(ValidateEmailRequest $request)
    {

        $data = $request->validated();

        $email = $data["email"];
        $document = $data["cpf"];

        $verifyIfExistCode = RegistrationToken::where('type', 'email')
            ->where('document', $document)
            ->where('type_data', $email)->first();

        if ($verifyIfExistCode && $verifyIfExistCode->validated) {

            return response()->json(
                [
                    "message" => "Email já se encontra validado",
                ],
                409
            );

        }

        $time_with_ten_minutes = Carbon::now()->addMinutes(10);

        if ($verifyIfExistCode) {
            $verifyIfExistCode->expiration = $time_with_ten_minutes;
            $verifyIfExistCode->save();
            $token = $verifyIfExistCode->token;
        } else {
            $registration_token = $this->createRegistrationTokenEmail($email, $document);
            $token = $registration_token->token;
        }

        $isSend = $this->sendRegistrationTokenEmail($email, $token);

        if ($isSend) {

            return response()->json(
                [
                    "message" => "Email enviado com sucesso!",
                ],
                200
            );

        }

        return response()->json(
            [
                "message" => "Erro ao enviar email, tente novamente mais tarde!",
            ],
            403
        );
    }

    /**
     * @param $email
     * @param $document
     * @return RegistrationToken
     * @throws Exception
     */
    private function createRegistrationTokenEmail($email, $document): RegistrationToken
    {
        $time_with_ten_minutes = Carbon::now()->addMinutes(10);

        $registration_token = new RegistrationToken();
        $registration_token->type = 'email';
        $registration_token->document = $document;
        $registration_token->type_data = $email;
        $registration_token->token = random_int(1000, 9999);
        $registration_token->number_wrong_attempts = 0;
        $registration_token->expiration = $time_with_ten_minutes;
        $registration_token->ip = \request()->ip();
        $registration_token->save();

        return $registration_token;
    }

    /**
     * @param $phone
     * @param $document
     * @return RegistrationToken
     * @throws Exception
     */
    private function createRegistrationTokenSms($phone, $document): RegistrationToken
    {

        $time_with_ten_minutes = Carbon::now()->addMinutes(10);

        $registration_token = new RegistrationToken();
        $registration_token->type = 'sms';
        $registration_token->document = $document;
        $registration_token->type_data = $phone;
        $registration_token->token = random_int(1000, 9999);
        $registration_token->number_wrong_attempts = 0;
        $registration_token->ip = \request()->ip();
        $registration_token->expiration = $time_with_ten_minutes;
        $registration_token->save();

        return $registration_token;
    }

    /**
     * @param $email
     * @param $token
     * @return mixed
     */
    private function sendRegistrationTokenEmail($email, $token)
    {

        $sendgridService = app(SendgridService::class);

        return $sendgridService->sendEmail(
            'noreply@cloudfox.net',
            'cloudfox',
            $email,
            isset($data['firstname']) ? $data['firstname'] : 'Cliente',
            "d-5f8d7ae156a2438ca4e8e5adbeb4c5ac",
            [
                "verify_code" => $token,
            ]
        );

    }

    /**
     * @param $phone
     * @param $token
     */
    private function sendRegistrationTokenSms($phone, $token)
    {
        $message = "Código de verificação CloudFox - " . $token;
        $smsService = new SmsService();
        $smsService->sendSms($phone, $message, ' ', 'DisparoPro');
    }

    /**
     * @param ValidateEmailTokenRequest $request
     * @return JsonResponse
     */
    public function matchEmailVerifyCode(ValidateEmailTokenRequest $request)
    {
        try {

            $data = $request->validated();
            $email = $data["email"];
            $document = $data["cpf"];
            $token = $data["code"];

            $existCodeToEmail = RegistrationToken::where('type', 'email')->where('type_data', $email)->latest()->first();

            if (!$existCodeToEmail) {

                return response()->json(
                    [
                        'message' => 'Não existe código para o email informado',
                    ],
                    400
                );

            }

            if ($existCodeToEmail->number_wrong_attempts >= 3 || Carbon::now()->greaterThan($existCodeToEmail->expiration)) {

                $registration_token = $this->createRegistrationTokenEmail($email, $document);
                $this->sendRegistrationTokenEmail($email, $registration_token->token);

                return response()->json(
                    [
                        "message" => "Seu cadastro com este email esta bloqueado por tentativas erradas ou se encontra expirado, enviamos um novo código para o seu email",
                    ],
                    403
                );

            }


            if ($existCodeToEmail->token != $token) {

                $existCodeToEmail->number_wrong_attempts = $existCodeToEmail->number_wrong_attempts + 1;
                $existCodeToEmail->ip = $request->ip();
                $existCodeToEmail->expiration = Carbon::now()->addMinutes(10);
                $existCodeToEmail->save();

                return response()->json(
                    [
                        'message' => 'O código informado está errado, você tem mais ' . (4 - $existCodeToEmail->number_wrong_attempts) . ' tentativas.',
                    ],
                    400
                );

            }


            $existCodeToEmail->validated = true;
            $existCodeToEmail->save();

            return response()->json(
                [
                    'checked' => true,
                    "message" => "Email verificado com sucesso!",
                ],
                200
            );

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
            $cellphone = $data["cellphone"];
            $document = $data["cpf"];

            $cellphone = preg_replace("/[^0-9]/", "", $cellphone);
            $verifyIfExistCode = RegistrationToken::where('type', 'sms')
                ->where('document', $document)
                ->where('type_data', $cellphone)
                ->orderByDesc('id')->first();

            if ($verifyIfExistCode && $verifyIfExistCode->validated) {

                return response()->json(
                    [
                        "message" => "telefone já se encontra validado",
                    ],
                    409
                );

            }

            $time_with_ten_minutes = Carbon::now()->addMinutes(10);

            if ($verifyIfExistCode) {
                $verifyIfExistCode->expiration = $time_with_ten_minutes;
                $verifyIfExistCode->save();
                $token = $verifyIfExistCode->token;
            } else {
                $registration_token = $this->createRegistrationTokenSms($cellphone, $document);
                $token = $registration_token->token;
            }

            $this->sendRegistrationTokenSms($cellphone, $token);

            return response()->json(
                [
                    "sent" => true,
                    "message" => "Mensagem enviada com sucesso!",
                ],
                200
            );

        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro'], 403);
        }
    }

    /**
     * @param ValidatePhoneNumberTokenRequest $request
     * @return JsonResponse
     */
    public function matchCellphoneVerifyCode(ValidatePhoneNumberTokenRequest $request)
    {
        try {

            $data = $request->validated();
            $token = $data["token"];
            $cellphone = $data["cellphone"];
            $document = $data["cpf"];
            $cellphone = preg_replace("/[^0-9]/", "", $cellphone);

            $existCodeToPhone = RegistrationToken::where('type', 'sms')->where('type_data', $cellphone)->latest()->first();

            if (!$existCodeToPhone) {

                return response()->json(
                    [
                        'message' => 'Não existe código de verificação para este telephone',
                    ],
                    400
                );

            }

            if ($existCodeToPhone->number_wrong_attempts >= 3 || Carbon::now()->greaterThan($existCodeToPhone->expiration)) {

                $registration_token = $this->createRegistrationTokenSms($cellphone,$document);
                $this->sendRegistrationTokenSms($cellphone, $registration_token->token);

                return response()->json(
                    [
                        "message" => "Seu cadastro com este telefone esta bloqueado por tentativas erradas ou se encontra expirado, enviamos um novo código para o seu telefone",
                    ],
                    403
                );

            }

            if ($existCodeToPhone->token != $token) {

                $existCodeToPhone->number_wrong_attempts = $existCodeToPhone->number_wrong_attempts + 1;
                $existCodeToPhone->ip = $request->ip();
                $existCodeToPhone->expiration = Carbon::now()->addMinutes(10);
                $existCodeToPhone->save();

                return response()->json(
                    [
                        'message' => 'O código informado está errado, você tem mais ' . (4 - $existCodeToPhone->number_wrong_attempts) . ' tentativas.',
                    ],
                    400
                );

            }

            $existCodeToPhone->validated = true;
            $existCodeToPhone->save();

            return response()->json(
                [
                    'checked' => true,
                    "message" => "Telefone verificado com sucesso!",
                ],
                200
            );


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
        $requestData['email_verified'] = self::VERIFIED_EMAIL;
        $requestData['cellphone_verified'] = self::VERIFIED_CELLPHONE;

        if (isset($requestData['date_birth']) && !stristr($requestData['date_birth'], '-')) {
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
     * @throws PresenterException
     */
    private function createCompanyToUser($requestData, Company $companyModel, User $user): void
    {
        $companyService = new IdwallService();
        $companyIdwall = $requestData['protocol'] ? $companyService->getReportByProtocolNumber($requestData['protocol']) : null;
        $company = json_decode($companyIdwall, true);

        $streetCompany = $requestData['street_company'] ?? null;
        $numberCompany = $requestData['number_company'] ?? null;
        $neighborhoodCompany = $requestData['neighborhood_company'] ?? null;
        $complementCompany = $requestData['complement_company'] ?? null;
        $stateCompany = $requestData['state_company'] ?? null;
        $cityCompany = $requestData['city_company'] ?? null;
        $supportEmail = $requestData['support_email'] ?? null;
        $supportPhone = $requestData['support_telephone'] ?? null;
        $agencyDigit = $requestData['agency_digit'] ?? null;
        $is_physical_person = $companyModel->present()->getCompanyType($requestData['company_type']) == 1;
        $fantasy_name = $is_physical_person ? $user->name : $company['result']['cnpj']['nome_empresarial'];
        $idwallResult = $companyIdwall ?? null;

        $companyModel->create(
            [
                'user_id' => $user->account_owner_id,
                'fantasy_name' => $fantasy_name,
                'company_document' => $is_physical_person ? $requestData['document'] : $requestData['company_document'],
                'company_type' => $is_physical_person ? 1 : 2,
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
                'agency_digit' => $agencyDigit,
                'account' => $requestData['account'],
                'account_digit' => $requestData['account_digit'],
                'id_wall_result' => $idwallResult,
            ]
        );
    }

    /**
     * @param $user
     * @return bool
     */
    public function verifyTokenValidate(User $user)
    {

        $tokenModel = new RegistrationToken();
        $cellphoneUser = preg_replace("/[^0-9]/", "", $user->cellphone);

        $email = $tokenModel->where('type_data', $user->email)->pluck('validated')->first();
        $cellphone = $tokenModel->where('type_data', $cellphoneUser)->pluck('validated')->first();

        if (empty($email) || empty($cellphone)) {
            return false;
        }

        return true;
    }

    /**
     * @param $user
     * @return bool|JsonResponse
     */
    public function acceptedTerms($user)
    {
        try {
            $userTermsModel = new UserTerms();

            $userIdRegistered = $user->id ?? User::find(3190);

            if (empty($userIdRegistered)) {
                return response()->json(
                    [
                        'message' => 'Ocorreu um erro, tente novamente!',
                    ],
                    400
                );
            }

            $userTerm = $userTermsModel->whereNotNull('accepted_at')
                ->where(
                    [
                        ['user_id', $userIdRegistered],
                        ['term_version', 'v1'],
                    ]
                )->first();

            if (!empty($userTerm)) {
                return true; // Salvo com Sucesso
            }

            $geoIp = null;
            try {
                $geoIp = geoip()->getLocation(IpService::getRealIpAddr());
            } catch (Exception $e) {
                report($e);
            }

            $operationalSystem = Agent::platform();
            $browser = Agent::browser();

            $deviceData = [
                'operational_system' => Agent::platform(),
                'operation_system_version' => Agent::version($operationalSystem),
                'browser' => Agent::browser(),
                'browser_version' => Agent::version($browser),
                'is_mobile' => Agent::isMobile(),
                'ip' => @$geoIp['ip'],
                'country' => @$geoIp['country'],
                'city' => @$geoIp['city'],
                'state' => @$geoIp['state'],
                'state_name' => @$geoIp['state_name'],
                'zip_code' => @$geoIp['postal_code'],
                'currency' => @$geoIp['currency'],
                'lat' => @$geoIp['lat'],
                'lon' => @$geoIp['lon'],
            ];

            $userTermsCreated = $userTermsModel->create(
                [
                    'user_id' => $userIdRegistered,
                    'term_version' => 'v1',
                    'device_data' => json_encode($deviceData, true),
                    'accepted_at' => Carbon::now(),
                ]
            );

            if ($userTermsCreated) {
                return true; // Salvo com Sucesso
            }

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente!',
                ],
                400
            );
        } catch (Exception $e) {
            report($e);

            return false;
        }
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
