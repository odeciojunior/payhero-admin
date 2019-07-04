<?php

namespace Modules\Profile\Http\Controllers;

use App\Entities\User;
use App\Entities\UserDocument;
use Modules\Profile\Http\Requests\ProfilePasswordRequest;
use Modules\Profile\Http\Requests\ProfileUploadDocumentRequest;
use Modules\Profile\Transformers\UserResource;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Exception;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Profile\Http\Requests\ProfileUpdateRequest;

/**
 * Class ProfileController
 * @package Modules\Profile\Http\Controllers
 */
class ProfileController extends Controller
{
    /**
     * @var User
     */
    private $userModel;
    /**
     * @var DigitalOceanFileService
     */
    private $digitalOceanFileService;
    /**
     * @var UserDocument
     */
    private $userDocumentModel;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed|DigitalOceanFileService
     */
    private function getDigitalOceanFileService()
    {
        if (!$this->digitalOceanFileService) {
            $this->digitalOceanFileService = app(DigitalOceanFileService::class);
        }

        return $this->digitalOceanFileService;
    }

    /**
     * @return User|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getUserModel()
    {
        if (!$this->userModel) {
            $this->userModel = app(User::class);
        }

        return $this->userModel;
    }

    /**
     * @return UserDocument|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getUserDocumentModel()
    {
        if (!$this->userDocumentModel) {
            $this->userDocumentModel = app(UserDocument::class);
        }

        return $this->userDocumentModel;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $user = auth()->user();

            $userResource = new UserResource($user);

            return view('profile::index', [
                'user' => json_decode(json_encode($userResource)),
            ]);
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

            $requestData = $request->validated();

            $user = auth()->user();

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
                    $this->getDigitalOceanFileService()->deleteFile($user->photo);

                    $img = Image::make($userPhoto->getPathname());
                    $img->crop($requestData['photo_w'], $requestData['photo_h'], $requestData['photo_x1'], $requestData['photo_y1']);
                    $img->resize(200, 200);
                    $img->save($userPhoto->getPathname());

                    $digitalOceanPath = $this->getDigitalOceanFileService()
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
            $requestData = $request->validated();

            $user = auth()->user();

            $user->update([
                              'password' => bcrypt($requestData['new_password']),
                          ]);

            return response()->json("success");
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
            $dataForm = $request->validated();

            $document = $request->file('file');

            $digitalOceanPath = $this->getDigitalOceanFileService()
                                     ->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->id) . '/private/documents', $document, null, null, 'private');

            $this->getUserDocumentModel()->create([
                                                      'user_id'            => auth()->user()->id,
                                                      'document_url'       => $digitalOceanPath,
                                                      'document_type_enum' => $dataForm["document_type"],
                                                      'status'             => null,
                                                  ]);

            $user = auth()->user();

            if (($dataForm["document_type"] ?? '') == $user->getEnum('document_type', 'personal_document')) {
                $user->update([
                                  'personal_document_status' => $user->getEnum('personal_document_status', 'analyzing'),
                              ]);
            }

            if (($dataForm["document_type"] ?? '') == $user->getEnum('document_type', 'address_document')) {
                $user->update([
                                  'address_document_status' => $user->getEnum('address_document_status', 'analyzing'),
                              ]);
            }

            return response()->json([
                                        'message'                     => 'Arquivo enviado com sucesso.',
                                        'personal_document_translate' => $user->getEnum('personal_document_status', $user->personal_document_status, true),
                                        'address_document_translate'  => $user->getEnum('address_document_status', $user->address_document_status, true),
                                    ], 200);
        } catch (Exception $e) {
            Log::warning('ProfileController uploadDocuments');
            report($e);

            return response()->json(['message' => 'Não foi possivel enviar o arquivo.'], 400);
        }
    }
}
