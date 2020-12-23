<?php

namespace Modules\Profile\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Lang;
use Intervention\Image\Facades\Image;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Services\AmazonFileService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\CountryService;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\SmsService;
use Modules\Core\Services\UserService;
use Modules\Profile\Http\Requests\ProfilePasswordRequest;
use Modules\Profile\Http\Requests\ProfileUpdateRequest;
use Modules\Profile\Http\Requests\ProfileUploadDocumentRequest;
use Modules\Profile\Transformers\ProfileDocumentsResource;
use Modules\Profile\Transformers\UserResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ProfileApiController
 * @package Modules\Profile\Http\Controllers
 */
class ProfileApiController
{
    public function index()
    {
        try {
            $user = auth()->user();

            if (!Gate::allows('view', [$user])) {
                return response()->json(['message' => 'Ocorreu um erro'], 403);
            }

            $user->load(["userNotification", "userDocuments"]);

            return response()->json([
                'user' => new UserResource($user),
                'countries' => (new CountryService())->getCountries(),
            ], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro'], 400);
        }
    }

    public function update(ProfileUpdateRequest $request, $idCode)
    {
        try {
            $user = auth()->user();
            $userModel = new User();

            $requestData = $request->validated();

            if (!Gate::allows('update', [$user])) {
                return response()->json(['message' => 'Sem permissão para editar este perfil'], 403);
            }

            if ($requestData['country'] == 'brazil' && !empty($requestData['cellphone'])) {
                $requestData['cellphone'] = '+' . preg_replace("/[^0-9]/", "", $requestData['cellphone']);
            }

            $requestData['document'] = preg_replace("/[^0-9]/", "", $requestData['document']);
            $requestData['name'] = preg_replace('/( )+/', ' ', $requestData['name']);

            $equalUserEmail = $userModel->where('email', $requestData['email'])
                ->where('id', '!=', $user->account_owner_id)
                ->first();
            if (!empty($equalUserEmail)) {
                return response()->json(['message' => 'Já existe um usuário cadastrado com esse Email'], 400);
            }
            $userChanges = [
                'name' => $requestData['name'],
                'email' => $requestData['email'],
                'document' => $requestData['document'],
                'zip_code' => $requestData['zip_code'],
                'country' => $requestData['country'],
                'state' => $requestData['country'] == 'brazil' || $requestData['country'] == 'usa' ? $requestData['state'] : null,
                'city' => $requestData['city'],
                'neighborhood' => $requestData['neighborhood'],
                'street' => $requestData['street'],
                'number' => $requestData['number'],
                'complement' => $requestData['complement'],
            ];

            if ($user->cellphone != $requestData['cellphone']) {
                $user->fill(["cellphone_verified" => false])->save();
            }

            $user->fill($userChanges)->save();

            $companyModel = new Company();
            $company = $companyModel->where('user_id', $user->id)
                ->where('company_type', $companyModel->present()->getCompanyType('physical person'))
                ->first();

            $userUpdateChanges = $user->getChanges();
            if (!empty($userUpdateChanges)) {
                if ((!empty($userUpdateChanges['email']) || array_key_exists('email', $userUpdateChanges))) {
                    $user->fill(["email_verified" => false])->save();
                }

                if ((!empty($userUpdateChanges['document']) || array_key_exists('document', $userUpdateChanges))) {
                    if (!empty($company)) {
                        $company->update(['document' => $user->document]);
                    }
                }
                if ($user->address_document_status == $user->present()->getAddressDocumentStatus('approved')) {
                    if (!empty($userUpdateChanges['zip_code']) ||
                        !empty($userUpdateChanges['street']) ||
                        !empty($userUpdateChanges['number']) ||
                        !empty($userUpdateChanges['neighborhood']) ||
                        !empty($userUpdateChanges['complement']) ||
                        !empty($userUpdateChanges['city']) ||
                        !empty($userUpdateChanges['state']) ||
                        !empty($userUpdateChanges['country'])) {
                        $user->update(['address_document_status' => $user->present()->getAddressDocumentStatus('pending')]);
                    }
                }
            }

            $userPhoto = $request->file('profile_photo');

            if ($userPhoto != null) {
                try {
                    if (empty($requestData['photo_w']) || empty($requestData['photo_h'])
                        || empty($requestData['photo_x1']) || empty($requestData['photo_y1'])) {
                        return response()->json(['message' => 'Erro ao salvar foto'], 400);
                    }
                    $digitalOceanService = app(DigitalOceanFileService::class);
                    $digitalOceanService->deleteFile($user->photo);

                    $img = Image::make($userPhoto->getPathname());
                    $img->crop(
                        $requestData['photo_w'],
                        $requestData['photo_h'],
                        $requestData['photo_x1'],
                        $requestData['photo_y1']
                    );
                    $img->resize(200, 200);
                    $img->save($userPhoto->getPathname());

                    $digitalOceanPath = $digitalOceanService
                        ->uploadFile(
                            'uploads/user/' . Hashids::encode(auth()->user()->id) . '/public/profile',
                            $userPhoto
                        );

                    $user->update(
                        [
                            'photo' => $digitalOceanPath,
                        ]
                    );
                } catch (Exception $e) {
                    report($e);

                    return response()->json(['message' => 'Erro ao salvar foto'], 400);
                }
            }

            return response()->json(['message' => 'Dados atualizados com sucesso'], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro'], 403);
        }
    }

    public function changePassword(ProfilePasswordRequest $request)
    {
        try {
            $user = auth()->user();
            if (Gate::allows('changePassword', [$user])) {
                $requestData = $request->validated();

                $user->update(
                    [
                        'password' => bcrypt($requestData['new_password']),
                    ]
                );

                return response()->json("success");
            } else {
                return response()->json(['message' => 'Sem permissão para trocar a senha '], 403);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro'], 403);
        }
    }

    public function verifyCellphone(Request $request)
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

            $user = auth()->user();
            if ($cellphone != $user->cellphone) {
                $user->cellphone = $cellphone;
                $user->save();
            }

            $verifyCode = random_int(100000, 999999);

            $cellphone = preg_replace("/[^0-9]/", "", $cellphone);

            $message = "Código de verificação CloudFox - " . $verifyCode;
            $smsService = new SmsService();

            $smsService->sendSms($cellphone, $message, ' ', 'aws-sns');

            return response()->json(
                [
                    "message" => "Mensagem enviada com sucesso!",

                ],
                200
            )->withCookie("cellphoneverifycode_" . Hashids::encode(auth()->id()), $verifyCode, 15);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro'], 403);
        }
    }

    public function matchCellphoneVerifyCode(Request $request)
    {
        try {
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
            $cookie = Cookie::get("cellphoneverifycode_" . Hashids::encode(auth()->id()));
            if ($verifyCode != $cookie) {
                return response()->json(
                    [
                        'message' => 'Código de verificação inválido!',
                    ],
                    400
                );
            }

            User::where("id", auth()->id())->update(["cellphone_verified" => true]);

            return response()->json(
                [
                    "message" => "Telefone verificado com sucesso!",
                ],
                200
            )
                ->withCookie(Cookie::forget("cellphoneverifycode_" . Hashids::encode(auth()->id())));
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro'], 403);
        }
    }

    public function verifyEmail(Request $request)
    {
        try {
            $data = $request->all();
            $email = $data["email"] ?? null;

            if (FoxUtils::isEmpty($email)) {
                return response()->json([
                    'message' => 'Email não pode ser vazio!',
                ], 400);
            }

            if (!FoxUtils::validateEmail($email)) {
                return response()->json([
                    'message' => 'Email inválido!',
                ], 400);
            }


            $user = auth()->user();
            if ($email != $user->email) {
                $user->email = $email;
                $user->save();
            }

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
                auth()->user()->name,
                "d-5f8d7ae156a2438ca4e8e5adbeb4c5ac",
                $data
            )) {
                return response()->json(
                    [
                        "message" => "Email enviado com sucesso!",

                    ],
                    200
                )
                    ->withCookie("emailverifycode_" . Hashids::encode(auth()->id()), $verifyCode, 15);
            }

            return response()->json(
                [
                    "message" => "Erro ao enviar email, tente novamente mais tarde!",

                ],
                400
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro'], 403);
        }
    }

    public function matchEmailVerifyCode(Request $request)
    {
        try {
            $data = $request->all();
            $verifyCode = $data["verifyCode"] ?? null;
            if (empty($verifyCode)) {
                return response()->json([
                    'message' => 'Código de verificação não pode ser vazio!',
                ], 400);
            }
            $cookie = Cookie::get("emailverifycode_" . Hashids::encode(auth()->id()));
            if ($verifyCode != $cookie) {
                return response()->json([
                    'message' => 'Código de verificação inválido!',
                ], 400);
            }

            User::where("id", auth()->id())->update(["email_verified" => true]);

            return response()->json([
                "message" => "Email verificado com sucesso!",
            ], 200)->withCookie(Cookie::forget("emailverifycode_" . Hashids::encode(auth()->id())));
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro'], 403);
        }
    }

    public function uploadDocuments(ProfileUploadDocumentRequest $request)
    {
        try {
            $user = auth()->user();

            if (!Gate::allows('uploadDocuments', [$user])) {
                return response()->json(['message' => 'Sem permissão para enviar o arquivo.'], 403);
            }

            $amazonFileService = app(AmazonFileService::class);
            $userDocument = new UserDocument();
            $userModel = new User();

            $dataForm = $request->validated();

            $document = $request->file('file');

            $amazonFileService->setDisk('s3_documents');
            $amazonPath = $amazonFileService->uploadFile(
                'uploads/user/' . Hashids::encode(auth()->user()->account_owner_id) . '/private/documents',
                $document,
                null,
                null,
                'private'
            );

            $documentType = $userModel->present()->getDocumentType($dataForm["document_type"]);

            $documentSaved = $userDocument->create([
                'user_id' => auth()->user()->account_owner_id,
                'document_url' => $amazonPath,
                'document_type_enum' => $documentType,
                'status' => $userDocument->present()
                    ->getTypeEnum('analyzing'),
            ]);

            if (($documentType ?? '') == $user->present()->getDocumentType('personal_document')) {
                $user->update([
                    'personal_document_status' => $user->present()
                        ->getPersonalDocumentStatus('analyzing'),
                ]);
            } else {
                if (($documentType ?? '') == $user->present()->getDocumentType('address_document')) {
                    $user->update([
                        'address_document_status' => $user->present()
                            ->getAddressDocumentStatus('analyzing'),
                    ]);
                } else {
                    $documentSaved->delete();

                    return response()->json(['message' => 'Não foi possivel enviar o arquivo.'], 400);
                }
            }

            return response()->json([
                'message' => 'Arquivo enviado com sucesso.',
                'personal_document_translate' => Lang::get(
                    'definitions.enum.personal_document_status.' . $user->present()
                        ->getPersonalDocumentStatus($user->personal_document_status)
                ),
                'address_document_translate' => Lang::get(
                    'definitions.enum.personal_document_status.' . $user->present()
                        ->getAddressDocumentStatus($user->address_document_status)
                ),
            ], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Não foi possivel enviar o arquivo.'], 400);
        }
    }

    public function updateUserNotification(Request $request)
    {
        try {
            $data = $request->all();
            $user = auth()->user();
            $user->load(["userNotification"]);
            $userNotification = $user->userNotification ?? null;
            if (FoxUtils::isEmpty($userNotification)) {
                return response()->json(
                    [
                        'message' => 'Ocorreu um erro inesperado, tente novamente mais tarde!',
                    ],
                    400
                );
            }

            $column = $data["column"] ?? null;
            $value = $data["value"] ?? null;

            if (FoxUtils::isEmpty($column) || is_null($value)) {
                return response()->json(
                    [
                        'message' => 'Ocorreu um erro inesperado, tente novamente mais tarde!',
                    ],
                    400
                );
            }

            $userNotification->$column = $value;
            if ($userNotification->save()) {
                return response()->json(
                    [
                        "message" => "Salvo com sucesso!",

                    ],
                    200
                );
            }

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                ],
                400
            );
        } catch (Exception $ex) {
            report($ex);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                ],
                400
            );
        }
    }

    public function openDocument(Request $request)
    {
        try {
            $digitalOceanFileService = app(DigitalOceanFileService::class);
            $amazonFileService = app(AmazonFileService::class);
            $data = $request->all();
            if (!empty($data['document_url'])) {
                $temporaryUrl = '';

                // Gera o Link temporário de acordo com o driver
                if (strstr($data['url'], 'digitaloceanspaces')) {
                    $temporaryUrl = $digitalOceanFileService->getTemporaryUrlFile($data['url'], 180);
                }

                if (strstr($data['url'], 'amazonaws')) {
                    $amazonFileService->setDisk('s3_documents');
                    $temporaryUrl = $amazonFileService->getTemporaryUrlFile($data['url'], 180);
                }

                // Validacao
                if (empty($temporaryUrl)) {
                    return response()->json(['message' => 'Erro ao acessar documentos do usuário!'], 400);
                }

                return response()->json(['data' => $temporaryUrl], 200);
            }

            return response()->json(['message' => 'Erro ao acessar documento do usuário!'], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro'], 400);
        }
    }

    public function getDocuments(Request $request)
    {
        try {
            if (empty($request->input('document_type'))) {
                return response()->json([
                    'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                ], 400);
            }

            $userDocumentModel = new UserDocument();
            $userModel = new User();

            $documentType = $userModel->present()->getDocumentType($request->input('document_type'));

            $userDocuments = $userDocumentModel->where('user_id', auth()->user()->account_owner_id)
                ->where('document_type_enum', $documentType)->get();

            return ProfileDocumentsResource::collection($userDocuments);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Ocorreu um erro, tente novamente mais tarde!',
            ], 400);
        }
    }

    public function verifyDocuments()
    {
        try {
            $companyService = new CompanyService();
            $userService = new UserService();

            $userDocumentRefused = $userService->haveAnyDocumentRefused();

            $link = null;
            $refused = false;
            $analyzing = false;

            if ($userDocumentRefused) {
                $refused = true;
                $link    = '/personal-info#documents';
            } else {
                $companyDocumentRefused = $companyService->companyDocumentRefused();
                $companyDocumentApproved = $companyService->companyDocumentApproved();
                if (empty($companyDocumentApproved) && !empty($companyDocumentRefused)) {
                    $refused     = true;
                    $companyCode = Hashids::encode($companyDocumentRefused->id);
                    if ($companyDocumentRefused->company_type == $companyDocumentRefused->present()->getCompanyType('physical person')) {
                        $link = "/personal-info#documents";
//                        $link = "/company-detail/${companyCode}/edit?type=1";
                    } else {
                        $link = "/companies/company-detail/${companyCode}#documents";
//                        $link = "/company-detail/${companyCode}/edit?type=2&tab=documents";
                    }
                } else {
                    $userValid = $userService->isDocumentValidated();
                    if (!$userValid) {
                        $analyzing = true;
                    } else {
                        $companyValid = $companyService->hasCompanyValid();
                        if (!$companyValid) {
                            $analyzing = true;
                        }
                    }
                }
            }

            if(env('ACCOUNT_FRONT_URL'))
                $link = env('ACCOUNT_FRONT_URL') . $link;

            return response()->json(
                ['message' => 'Documentos verificados!', 'analyzing' => $analyzing,'refused' => $refused,'link' => $link],
                200
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(['error' => 'Erro ao verificar documentos'], 400);
        }
    }
}
