<?php

namespace Modules\Profile\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\Facades\Image;
use Modules\Core\Entities\UserDocument;
use Modules\Profile\Transformers\UserResource;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Profile\Http\Requests\ProfileIndexRequest;
use Modules\Profile\Http\Requests\ProfileUpdateRequest;
use Modules\Profile\Http\Requests\ProfilePasswordRequest;
use Modules\Profile\Http\Requests\ProfileUploadDocumentRequest;

/**
 * Class ProfileController
 * @package Modules\Profile\Http\Controllers
 */
class ProfileController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        try {

            $user = auth()->user();

            if (Gate::allows('view', [$user])) {
                $userResource = new UserResource($user); 

                return view('profile::index', [
                    'user' => json_decode(json_encode($userResource)),
                ]);
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileUpdateRequest $request, $idCode)
    {
        try {
            $user = auth()->user();

            if (Gate::allows('update', [$user])) {

                $digitalOceanFileService = app(DigitalOceanFileService::class);
                $requestData             = $request->validated();

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

    /**
     * @param ProfilePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
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
                                      'personal_document_status' => $user->present()->getPersonalDocumentStatus('analyzing'),
                                  ]);
                }

                if (($dataForm["document_type"] ?? '') == $user->present()->getDocumentType('address_document')) {
                    $user->update([
                                      'address_document_status' => $user->present()->getAddressDocumentStatus('analyzing'),
                                  ]);
                }

                return response()->json([
                                            'message'                     => 'Arquivo enviado com sucesso.',
                                            'personal_document_translate' => $user->present()->getPersonalDocumentStatus($user->personal_document_status),
                                            'address_document_translate'  => $user->present()->getAddressDocumentStatus($user->address_document_status),
                                        ], 200);
            } else {
                return response()->json(['message' => 'Sem permissão para enviar o arquivo.'], 403);
            }
        } catch (Exception $e) {
            Log::warning('ProfileController uploadDocuments');
            report($e);

            return response()->json(['message' => 'Não foi possivel enviar o arquivo.'], 400);
        }
    }
}
