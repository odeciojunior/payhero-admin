<?php

namespace Modules\Profile\Http\Controllers;

use App\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Helpers\CaminhoArquivosHelper;
use Exception;
use Modules\Profile\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    /**
     * @var User
     */
    private $userModel;

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

            return view('profile::index', [
                'user' => $user,
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
    public function update(ProfileUpdateRequest $request, $profileId)
    {
        try {

            $requestData = $request->all();

            $user = $this->getUserModel()->find($requestData['id']);

            $this->getUserModel()->update($requestData, $user->id);

            $userPhoto = $request->file('foto_usuario');

            if ($userPhoto != null) {

                try {
                    $photoName = 'user_' . $user->id . '_.' . $userPhoto->getClientOriginalExtension();

                    Storage::delete('public/upload/perfil/' . $photoName);

                    $userPhoto->move(CaminhoArquivosHelper::CAMINHO_FOTO_USER, $photoName);

                    $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_USER . $photoName);

                    $img->crop($requestData['foto_w'], $requestData['foto_h'], $requestData['foto_x1'], $requestData['foto_y1']);

                    $img->resize(200, 200);

                    Storage::delete('public/upload/perfil/' . $photoName);

                    $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_USER . $photoName);

                    $user->update([
                                      'photo' => $photoName,
                                  ]);
                } catch (\Exception $e) {
                    dd($e);
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
    public function changePassword(Request $request)
    {
        try {
            $requestData = $request->all();

            $user = auth()->user();

            $user->update([
                              'password' => bcrypt($requestData['nova_senha']),
                          ]);

            return response()->json("sucesso");
        } catch (Exception $e) {
            Log::warning('ProfileController changePassword');
            report($e);
        }
    }
}
