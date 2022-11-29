<?php

namespace Modules\UserInformations\Http\Controllers;

use App\Jobs\PipefyUpdateCardJob;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\UserInformation;
use Modules\Core\Entities\User;
use Modules\Core\Services\FoxUtils;
use Modules\UserInformations\Http\Requests\UserInformationsRequest;
use Symfony\Component\HttpFoundation\Response;

class UserInformationsApiController extends Controller
{
    public function store(UserInformationsRequest $request)
    {
        try {
            $data = $request->all();

            $model = new UserInformation();
            $user = User::find(auth()->user()->account_owner_id);
            $exists = $model->where("document", $user->document)->exists();

            if ($exists) {
                $user = $model->where("document", $user->document)->first();
                $user->status = 0;
                $user->document = $user->document;
                $user->email = $user->email;
                $user = $this->setData($user, $data);
                $user->save();

                if (FoxUtils::isProduction()) {
                    // PipefyUpdateCardJob::dispatch($user->userInformations);
                }

                return response()->json(
                    [
                        "message" => "Informações do usuário atualizadas",
                    ],
                    Response::HTTP_OK
                );
            }

            $model->status = 0;
            $model->document = $user->document;
            $model->email = $user->email;
            $model = $this->setData($model, $data);
            $model->save();

            return response()->json(
                [
                    "message" => "Informações do usuário cadastradas",
                ],
                Response::HTTP_OK
            );
        } catch (Exception $exception) {
            report($exception);

            return response()->json(
                [
                    "message" => $exception->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function setData($model, $data)
    {
        if (!empty($data["monthly_income"])) {
            $model->monthly_income = $data["monthly_income"];
        }

        if (!empty($data["niche"])) {
            $model->niche = $data["niche"];
        }

        if (!empty($data["website_url"])) {
            $model->website_url = $data["website_url"];
        }

        if (!empty($data["gateway"])) {
            $model->gateway = $data["gateway"];
        }

        if (!empty($data["ecommerce"])) {
            $model->ecommerce = $data["ecommerce"];
        }

        if (!empty($data["cloudfox_referer"])) {
            $model->cloudfox_referer = $data["cloudfox_referer"];
        }

        return $model;
    }
}
