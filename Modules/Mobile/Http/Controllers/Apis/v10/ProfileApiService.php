<?php


namespace Modules\Mobile\Http\Controllers\Apis\v10;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\FoxUtils;
use Modules\Profile\Http\Requests\ProfilePasswordRequest;
use Modules\Profile\Transformers\UserResource;

class ProfileApiService {

    /**
     * ProfileApiService constructor.
     */
    public function __construct() { }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfileData() {

        try {
            $user = auth()->user();

            if (Gate::allows('view', [$user])) {
                $user->load(["userNotification"]);
                $userResource = new UserResource($user);
                $userData = new UserResource($userResource);

                return response()->json(compact('userData'), 200);
            } else {
                return response()->json([
                    'message' => 'Sem permissão para carregar os dados do perfil - ProfileApiService getProfileData',
                ], 401);
            }
        } catch (Exception $e) {
            Log::warning('ProfileController index');
            report($e);

            return response()->json([
                'message' => 'Erro ao carregar dados do perfil - ProfileApiService - getProfileData',
            ], 400);
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
                return response()->json(['message' => 'Sem permissão para trocar a senha'], 401);
            }
        } catch (Exception $e) {
            Log::warning('ProfileController changePassword');
            report($e);

            return response()->json(['message' => 'Erro ao trocar a senha'], 400);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
}
