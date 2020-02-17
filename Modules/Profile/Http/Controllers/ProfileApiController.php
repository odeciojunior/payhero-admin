<?php

namespace Modules\Profile\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\SmsService;
use Modules\Core\Services\UserService;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Lang;
use Intervention\Image\Facades\Image;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Profile\Http\Requests\ProfilePasswordRequest;
use Modules\Profile\Http\Requests\ProfileUpdateRequest;
use Modules\Profile\Http\Requests\ProfileUploadDocumentRequest;
use Modules\Profile\Transformers\ProfileDocumentsResource;
use Modules\Profile\Transformers\ProfileTaxResource;
use Modules\Profile\Transformers\UserResource;

/**
 * Class ProfileApiController
 * @package Modules\Profile\Http\Controllers
 */
class ProfileApiController
{
    /**
     * @return UserResource
     */
    public function index()
    {
        try {

            $user = auth()->user();

            if (Gate::allows('view', [$user])) {
                $user->load(["userNotification", "userDocuments"]);

                return new UserResource($user);
            } else {
                //sem permissao

            }
        } catch (Exception $e) {
            Log::warning('ProfileController index');
            report($e);
        }
    }

    /**
     * @param ProfileUpdateRequest $request
     * @param $idCode
     * @return RedirectResponse
     */
    public function update(ProfileUpdateRequest $request, $idCode)
    {
        try {
            $user = auth()->user();

            if (Gate::allows('update', [$user])) {
                $requestData = $request->validated();
                if ($requestData['country'] == 'brazil' && !empty($requestData['cellphone'])) {
                    $requestData['cellphone'] = '+' . preg_replace("/[^0-9]/", "", $requestData['cellphone']);
                }
                $requestData['document'] = preg_replace("/[^0-9]/", "", $requestData['document']);
                $requestData['name']     = preg_replace('/( )+/', ' ', $requestData['name']);

                $user->fill(
                    [
                        'name'         => $requestData['name'],
                        'email'        => $requestData['email'],
                        'document'     => $requestData['document'],
                        'cellphone'    => $requestData['cellphone'],
                        'date_birth'   => $requestData['date_birth'],
                        'zip_code'     => $requestData['zip_code'],
                        'country'      => $requestData['country'],
                        'state'        => $requestData['country'] == 'brazil' || $requestData['country'] == 'usa' ? $requestData['state'] : null,
                        'city'         => $requestData['city'],
                        'neighborhood' => $requestData['neighborhood'],
                        'street'       => $requestData['street'],
                        'number'       => $requestData['number'],
                        'complement'   => $requestData['complement'],
                    ]
                )->save();

                $userUpdateChanges = $user->getChanges();
                if (!empty($userUpdateChanges)) {


                    if ((!empty($userUpdateChanges['email']) || array_key_exists('email', $userUpdateChanges))) {
                        $user->fill(["email_verified" => false])->save();
                    }
                    if ((!empty($userUpdateChanges['cellphone']) || array_key_exists('cellphone', $userUpdateChanges))) {

                        $user->fill(["cellphone_verified" => false])->save();
                    }
                    if ((!empty($userUpdateChanges['document']) || array_key_exists('document', $userUpdateChanges))) {

                        $companyModel = new Company();
                        $company      = $companyModel->where('user_id', $user->id)
                                                     ->where('company_type', $companyModel->present()
                                                                                          ->getCompanyType('physical person'))
                                                     ->first();
                        if (!empty($company)) {
                            $company->update(['company_document' => $user->document]);
                        }
                    }
                }

                $userPhoto = $request->file('profile_photo');

                if ($userPhoto != null) {

                    try {
                        $digitalOceanService = app(DigitalOceanFileService::class);
                        $digitalOceanService->deleteFile($user->photo);

                        $img = Image::make($userPhoto->getPathname());
                        $img->crop($requestData['photo_w'], $requestData['photo_h'], $requestData['photo_x1'], $requestData['photo_y1']);
                        $img->resize(200, 200);
                        $img->save($userPhoto->getPathname());

                        $digitalOceanPath = $digitalOceanService
                            ->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->id) . '/public/profile', $userPhoto);

                        $user->update([
                                          'photo' => $digitalOceanPath,
                                      ]);
                    } catch (Exception $e) {
                        Log::warning('ProfileController - update - Erro ao enviar foto do profile');
                        report($e);

                        return response()->json(['message' => 'Erro ao salvar foto'], 400);
                    }
                }

                return response()->json(['message' => 'Dados atualizados com sucesso'], 200);
                //return redirect()->route('profile');

            } else {
                //sem permissao
                return response()->json(['message' => 'Sem permissão para editar este perfil'], 403);
            }
        } catch (Exception $e) {
            Log::warning('ProfileController update');
            report($e);
        }
    }

    public function updateTaxes(Request $request)
    {
        try {
            $requestData = $request->all();

            $newCardTax = '';

            if ($requestData['plan'] == 'plan-30') {
                auth()->user()->update([
                                           'credit_card_tax'                => '5.9',
                                           'credit_card_release_money_days' => 30,
                                       ]);
                $newCardTax = '5.9%';
            } else if ($requestData['plan'] == 'plan-15') {
                auth()->user()->update([
                                           'credit_card_tax'                => '6.5',
                                           'credit_card_release_money_days' => 15,
                                       ]);
                $newCardTax = '6.5%';
            }

            return response()->json([
                                        'message' => 'Plano atualizado com sucesso',
                                        'data'    => [
                                            'new_tax_value' => $newCardTax,
                                        ],
                                    ]);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu algum erro',
                                    ]);
        }
    }

    /**
     * @param ProfilePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ProfilePasswordRequest $request)
    {
        try {
            $user = auth()->user();
            if (Gate::allows('changePassword', [$user])) {
                $requestData = $request->validated();

                $user->update([
                                  'password' => bcrypt($requestData['new_password']),
                              ]);

                return response()->json("success");
            } else {
                return response()->json(['message' => 'Sem permissão para trocar a senha '], 403);
            }
        } catch (Exception $e) {
            Log::warning('ProfileController changePassword');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyCellphone(Request $request)
    {
        try {
            $data      = $request->all();
            $cellphone = $data["cellphone"] ?? null;
            if (FoxUtils::isEmpty($cellphone)) {
                return response()->json(
                    [
                        'message' => 'Telefone não pode ser vazio!',
                    ], 400);
            }

            $user = auth()->user();
            if ($cellphone != $user->cellphone) {
                $user->cellphone = $cellphone;
                $user->save();
            }

            $verifyCode = random_int(100000, 999999);

            $message    = "Código de verificação CloudFox - " . $verifyCode;
            $smsService = new SmsService();
            $smsService->sendSms($cellphone, $message);

            return response()->json(
                [
                    "message" => "Mensagem enviada com sucesso!",

                ], 200)
                             ->withCookie("cellphoneverifycode_" . Hashids::encode(auth()->id()), $verifyCode, 15);
        } catch (Exception $e) {
            Log::warning('ProfileController verifyCellphone');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function matchCellphoneVerifyCode(Request $request)
    {
        try {
            $data       = $request->all();
            $verifyCode = $data["verifyCode"] ?? null;
            if (empty($verifyCode)) {
                return response()->json(
                    [
                        'message' => 'Código de verificação não pode ser vazio!',
                    ], 400);
            }
            $cookie = Cookie::get("cellphoneverifycode_" . Hashids::encode(auth()->id()));
            if ($verifyCode != $cookie) {
                return response()->json(
                    [
                        'message' => 'Código de verificação inválido!',
                    ], 400);
            }

            User::where("id", auth()->id())->update(["cellphone_verified" => true]);

            return response()->json(
                [
                    "message" => "Telefone verificado com sucesso!",
                ], 200)
                             ->withCookie(Cookie::forget("cellphoneverifycode_" . Hashids::encode(auth()->id())));
        } catch (Exception $e) {
            Log::warning('ProfileController matchCellphoneVerifyCode');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyEmail(Request $request)
    {
        try {
            $data  = $request->all();
            $email = $data["email"] ?? null;
            if (FoxUtils::isEmpty($email)) {
                return response()->json(
                    [
                        'message' => 'Email não pode ser vazio!',
                    ], 400);
            } else if (!FoxUtils::validateEmail($email)) {
                return response()->json(
                    [
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
                'noreply@cloudfox.net', 'cloudfox', $email, auth()->user()->name, "d-5f8d7ae156a2438ca4e8e5adbeb4c5ac", $data
            )) {
                return response()->json(
                    [
                        "message" => "Email enviado com sucesso!",

                    ], 200)
                                 ->withCookie("emailverifycode_" . Hashids::encode(auth()->id()), $verifyCode, 15);
            }

            return response()->json(
                [
                    "message" => "Erro ao enviar email, tente novamente mais tarde!",

                ], 400);
        } catch (Exception $e) {
            Log::warning('ProfileController verifyEmail');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function matchEmailVerifyCode(Request $request)
    {
        try {
            $data       = $request->all();
            $verifyCode = $data["verifyCode"] ?? null;
            if (empty($verifyCode)) {
                return response()->json(
                    [
                        'message' => 'Código de verificação não pode ser vazio!',
                    ], 400);
            }
            $cookie = Cookie::get("emailverifycode_" . Hashids::encode(auth()->id()));
            if ($verifyCode != $cookie) {
                return response()->json(
                    [
                        'message' => 'Código de verificação inválido!',
                    ], 400);
            }

            User::where("id", auth()->id())->update(["email_verified" => true]);

            return response()->json(
                [
                    "message" => "Email verificado com sucesso!",
                ], 200)
                             ->withCookie(Cookie::forget("emailverifycode_" . Hashids::encode(auth()->id())));
        } catch (Exception $e) {
            Log::warning('ProfileController matchEmailVerifyCode');
            report($e);
        }
    }

    /**
     * @param ProfileUploadDocumentRequest $request
     * @return JsonResponse
     */
    public function uploadDocuments(ProfileUploadDocumentRequest $request)
    {
        try {

            $user = auth()->user();

            if (Gate::allows('uploadDocuments', [$user])) {

                $digitalOceanFileService = app(DigitalOceanFileService::class);
                $userDocument            = new UserDocument();
                $userModel               = new User();

                $dataForm = $request->validated();

                $document = $request->file('file');

                $digitalOceanPath = $digitalOceanFileService->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->account_owner_id) . '/private/documents', $document, null, null, 'private');

                $documentType = $userModel->present()
                                          ->getDocumentType($dataForm["document_type"]);

                $documentSaved = $userDocument->create([
                                                           'user_id'            => auth()->user()->account_owner_id,
                                                           'document_url'       => $digitalOceanPath,
                                                           'document_type_enum' => $documentType,
                                                           'status'             => $userDocument->present()
                                                                                                ->getTypeEnum('analyzing'),
                                                       ]);

                if (($documentType ?? '') == $user->present()->getDocumentType('personal_document')) {
                    $user->update([
                                      'personal_document_status' => $user->present()
                                                                         ->getPersonalDocumentStatus('analyzing'),
                                  ]);
                } else if (($documentType ?? '') == $user->present()->getDocumentType('address_document')) {
                    $user->update([
                                      'address_document_status' => $user->present()
                                                                        ->getAddressDocumentStatus('analyzing'),
                                  ]);
                } else {
                    $documentSaved->delete();

                    return response()->json(['message' => 'Não foi possivel enviar o arquivo.'], 400);
                }

                return response()->json([
                                            'message'                     => 'Arquivo enviado com sucesso.',
                                            'personal_document_translate' => Lang::get('definitions.enum.personal_document_status.' . $user->present()
                                                                                                                                           ->getPersonalDocumentStatus($user->personal_document_status)),
                                            'address_document_translate'  => Lang::get('definitions.enum.personal_document_status.' . $user->present()
                                                                                                                                           ->getAddressDocumentStatus($user->address_document_status)),
                                        ], 200);
            } else {
                return response()->json(['message' => 'Sem permissão para enviar o arquivo.'], 403);
            }
        } catch (Exception $e) {
            Log::warning('ProfileApiController uploadDocuments');
            report($e);

            return response()->json(['message' => 'Não foi possivel enviar o arquivo.'], 400);
        }
    }

    /**
     * @param $userId
     * @return JsonResponse|ProfileTaxResource
     */
    public function getTax($userId)
    {
        try {
            if (!empty($userId)) {
                $user   = auth()->user();
                $userId = current(Hashids::decode($userId));
                if ($user->account_owner_id == $userId) {
                    return new ProfileTaxResource($user);
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro!',
                                            ], 400);
                }
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar dados taxas do usuario (ProfileApiController - getTax)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateUserNotification(Request $request)
    {
        try {
            $data = $request->all();
            $user = auth()->user();
            $user->load(["userNotification"]);
            $userNotification = $user->userNotification ?? null;
            if (FoxUtils::isEmpty($userNotification)) {
                return response()->json([
                                            'message' => 'Ocorreu um erro inesperado, tente novamente mais tarde!',
                                        ], 400);
            }

            $column = $data["column"] ?? null;
            $value  = $data["value"] ?? null;

            if (FoxUtils::isEmpty($column) || is_null($value)) {
                return response()->json([
                                            'message' => 'Ocorreu um erro inesperado, tente novamente mais tarde!',
                                        ], 400);
            }

            $userNotification->$column = $value;
            if ($userNotification->save()) {
                return response()->json(
                    [
                        "message" => "Salvo com sucesso!",

                    ], 200);
            }

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                    ], 400);
        } catch (Exception $ex) {
            report($ex);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function openDocument(Request $request)
    {
        try {
            $digitalOceanFileService = app(DigitalOceanFileService::class);
            $data                    = $request->all();
            if (!empty($data['document_url'])) {
                $temporaryUrl = $digitalOceanFileService->getTemporaryUrlFile($data['document_url'], 180);

                return response()->json(['data' => $temporaryUrl], 200);
            }

            return response()->json(['message' => 'Erro ao acessar documento do usuário!'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao acessar documento do usuário  ProfileApiController - openDocument');
            report($e);
        }
    }

    public function getDocuments(Request $request)
    {
        try {
            if (!empty($request->input('document_type'))) {
                $userDocumentModel = new UserDocument();
                $userModel         = new User();

                $documentType = $userModel->present()->getDocumentType($request->input('document_type'));

                $userDocuments = $userDocumentModel->where('user_id', auth()->user()->account_owner_id)
                                                   ->where('document_type_enum', $documentType)->get();

                return ProfileDocumentsResource::collection($userDocuments);
            } else {

                return response()->json([
                                            'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                        ], 400);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                    ], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function verifyDocuments()
    {
        try {
            $companyService = new CompanyService();
            $userService    = new UserService();

            $companyDocumentPending = $companyService->haveAnyDocumentPending();
            $userDocumentPending    = $userService->haveAnyDocumentPending();

            $link = null;

            if ($userDocumentPending) {
                $link = '/profile';
            } else if ($companyDocumentPending) {
                $link = '/companies';
            }

            $result = $companyDocumentPending || $userDocumentPending;

            return response()->json(['message' => 'Documentos verificados!', 'pending' => $result, 'link' => $link], 200);
        } catch (Exception $e) {
            Log::warning('Erro ao verificar documentos ProfileApiController - verifyDocuments');
            report($e);

            return response()->json(['error' => 'Erro ao verificar documentos'], 400);
        }
    }
}
