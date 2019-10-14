<?php

namespace Modules\Profile\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Facades\Image;
use Modules\Core\Entities\UserDocument;
use Modules\Profile\Transformers\UserResource;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Profile\Transformers\ProfileTaxResource;
use Modules\Profile\Http\Requests\ProfileUpdateRequest;
use Modules\Profile\Http\Requests\ProfilePasswordRequest;
use Modules\Profile\Http\Requests\ProfileUploadDocumentRequest;

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
                $userResource = new UserResource($user);

                return new UserResource($userResource);
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

                $user->update([
                                  'name'         => $requestData['name'],
                                  'email'        => $requestData['email'],
                                  'document'     => $requestData['document'],
                                  'cellphone'    => $requestData['cellphone'],
                                  'date_birth'   => $requestData['date_birth'],
                                  'zip_code'     => $requestData['zip_code'],
                                  'country'      => 'br',
                                  'state'        => $requestData['state'],
                                  'city'         => $requestData['city'],
                                  'neighborhood' => $requestData['neighborhood'],
                                  'street'       => $requestData['street'],
                                  'number'       => $requestData['number'],
                                  'complement'   => $requestData['complement'],
                              ]);

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

    public function updateTaxes(Request $request){

        try{
            $requestData = $request->all();

            $newCardTax = '';

            if($requestData['plan'] == 'plan-30'){
                auth()->user()->update([
                    'credit_card_tax' => '5.9',
                    'credit_card_release_money_days' => 30
                ]);
                $newCardTax = '5.9%';
            }
            elseif($requestData['plan'] == 'plan-15'){
                auth()->user()->update([
                    'credit_card_tax' => '6.5',
                    'credit_card_release_money_days' => 15
                ]);
                $newCardTax = '6.5%';
            }

            return response()->json([
                'message' => 'Plano atualizado com sucesso', 
                'data' => [
                    'new_tax_value' => $newCardTax
                ]
            ]);
        }
        catch(Exception $e){
            report($e);
            return response()->json([
                'message' => 'Ocorreu algum erro'
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

                $dataForm = $request->validated();

                $digitalOceanService = app(DigitalOceanFileService::class);
                $userDocuments       = new UserDocument();

                $document = $request->file('file');

                $digitalOceanPath = $digitalOceanFileService->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->id) . '/private/documents', $document, null, null, 'private');

                $userDocument->create([
                                          'user_id'            => auth()->user()->id,
                                          'document_url'       => $digitalOceanPath,
                                          'document_type_enum' => $dataForm["document_type"],
                                          'status'             => null,
                                      ]);

                if (($dataForm["document_type"] ?? '') == $user->present()->getDocumentType('personal_document')) {
                    $user->update([
                                      'personal_document_status' => $user->present()
                                                                         ->getPersonalDocumentStatus('analyzing'),
                                  ]);
                }

                if (($dataForm["document_type"] ?? '') == $user->present()->getDocumentType('address_document')) {
                    $user->update([
                                      'address_document_status' => $user->present()
                                                                        ->getAddressDocumentStatus('analyzing'),
                                  ]);
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
                if ($user->id == $userId) {
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
}
