<?php

namespace App\Http\Controllers\Idwall;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserBiometryResult;
use Vinkla\Hashids\Facades\Hashids;

class UserValidationController extends Controller
{
    public function validateUser(Request $request)
    {
        try {
            $data = $request->all();

            $user = User::find(current(Hashids::decode($data["user_id"])));

            if (!$user) {
                return response()->json(
                    [
                        "status" => "error",
                    ],
                    400
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

            if ($response->status() >= 200 && $response->status() < 300) {
                $user->update([
                    "biometry_status" => User::BIOMETRY_STATUS_IN_PROCESS,
                ]);
            }

            return response()->json(["url_redirect" => env("ACCOUNT_FRONT_URL") . "/personal-info"], 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(
                [
                    "status" => "error",
                ],
                400
            );
        }
    }
}
