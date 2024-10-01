<?php

declare(strict_types=1);

namespace App\Http\Controllers\Idwall;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserBiometryResult;
use Modules\Core\Enums\User\UserBiometryStatusEnum;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

final class UserValidationController extends Controller
{
    public function validateUser(Request $request)
    {
        try {
            $data = $request->all();

            $user = User::find(current(Hashids::decode($data["user_id"])));

            if (! $user) {
                return response()->json(
                    [
                        "status" => "error",
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $idWallData = [
                "ref" => $data["user_id"] . "-" . time(),
                "sdkToken" => $data["token"],
                "personal" => [
                    "cpfNumber" => $user->document,
                ],
            ];

            $response = Http::withHeaders([
                "Authorization" => env("IDWALL_TOKEN"),
            ])->post("https://api-v3.idwall.co/maestro/profile/sdk?runOCR=true", $idWallData);

            UserBiometryResult::create([
                "user_id" => $user->id,
                "vendor" => "idwall",
                "biometry_id" => "",
                "status" => "",
                "request_data" => json_encode($idWallData),
                "response_data" => json_encode($response->body()),
            ]);

            if ($response->status() >= Response::HTTP_OK && $response->status() < Response::HTTP_MULTIPLE_CHOICES) {
                $user->update([
                    "biometry_status" => UserBiometryStatusEnum::IN_PROCESS->value,
                ]);
            }

            return response()->json(["url_redirect" => env("ACCOUNT_FRONT_URL") . "/personal-info"], 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(
                [
                    "status" => "error",
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
