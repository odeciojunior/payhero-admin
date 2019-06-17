<?php

namespace Modules\Profile\Http\Controllers;

use App\Entities\User;
use Modules\Profile\Http\Requests\ProfilePasswordRequest;
use Modules\Profile\Transformers\UserResource;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Helpers\CaminhoArquivosHelper;
use Exception;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Profile\Http\Requests\ProfileUpdateRequest;

/**
 * uploads/user/ID/profile/photo.jpg
 * uploads/user/ID/private/documents/*
 * uploads/user/ID/private/company-documents/*
 * uploads/product/ID/photo.jpg
 * uploads/product/ID/private/product.pdf
 */

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
     * ProfileController constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getDigitalOceanFileService()
    {
        if (!$this->digitalOceanFileService) {
            $this->digitalOceanFileService = app(DigitalOceanFileService::class);
        }

        return $this->digitalOceanFileService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getUserModel()
    {
        if (!$this->userModel) {
            $this->userModel = app(User::class);
        }

        return $this->userModel;
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
                'user' => $userResource,
            ]);
        } catch (Exception $e) {
            Log::warning('ProfileController index');
            report($e);
        }
    }

    /**
     * @param Request $request
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
                              'country'      => $requestData['country'],
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
                }
            }

            return redirect()->route('profile');
        } catch (Exception $e) {
            Log::warning('ProfileController update');
            report($e);
        }
    }

    /**
     * @param Request $request
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

            return response()->json("sucesso");
        } catch (Exception $e) {
            Log::warning('ProfileController changePassword');
            report($e);
        }
    }
}
