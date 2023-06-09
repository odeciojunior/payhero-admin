<?php

namespace Modules\CheckoutEditor\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Modules\CheckoutEditor\Http\Requests\SendSupportEmailVerificationRequest;
use Modules\CheckoutEditor\Http\Requests\SendSupportPhoneVerificationRequest;
use Modules\CheckoutEditor\Http\Requests\UpdateCheckoutConfigRequest;
use Modules\CheckoutEditor\Http\Requests\VerifySupportEmailRequest;
use Modules\CheckoutEditor\Http\Requests\VerifySupportPhoneRequest;
use Modules\CheckoutEditor\Transformers\CheckoutConfigResource;
use Modules\Core\Entities\CheckoutConfig;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\AmazonFileService;
use Modules\Core\Services\CacheService;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\SmsService;
use Spatie\Activitylog\Models\Activity;

class CheckoutEditorApiController extends Controller
{
    public function show($projectId)
    {
        try {
            $projectId = hashids_decode($projectId);

            $config = CheckoutConfig::where("project_id", $projectId)->first();

            return new CheckoutConfigResource($config);
        } catch (\Exception $e) {
            report($e);
            return foxutils()->isProduction()
                ? response()->json(["message" => "Erro ao obter as configurações do checkout"])
                : response()->json(["message" => $e->getMessage()]);
        }
    }

    public function update($id, UpdateCheckoutConfigRequest $request)
    {
        try {
            $amazonFileService = app(AmazonFileService::class);

            $id = hashids_decode($id);

            $data = $request->all();
            $data["company_id"] = hashids_decode($data["company_id"]);

            $config = CheckoutConfig::find($id);

            $userId = hashids_encode(auth()->user()->account_owner_id);

            $logo = $request->file("checkout_logo");
            if (!empty($logo)) {
                $amazonFileService->deleteFile($config->checkout_logo);
                $amazonPathLogo = $amazonFileService->uploadFile(
                    "uploads/user/" . $userId . "/public/projects/" . hashids_encode($id) . "/logo",
                    $logo
                );
                $data["checkout_logo"] = $amazonPathLogo;
            }

            if (intval($data["checkout_favicon_type"]) === CheckoutConfig::CHECKOUT_FAVICON_TYPE_LOGO) {
                $data["checkout_favicon"] = !empty($data["checkout_logo"])
                    ? $data["checkout_logo"]
                    : $config->checkout_logo;
            } else {
                $favicon = $request->file("checkout_favicon");
                if (!empty($favicon)) {
                    if ($config->checkout_favicon_type === CheckoutConfig::CHECKOUT_FAVICON_TYPE_FILE) {
                        $amazonFileService->deleteFile($config->checkout_favicon);
                    }
                    $amazonPathFavicon = $amazonFileService->uploadFile(
                        "uploads/user/" . $userId . "/public/projects/" . hashids_encode($id) . "/favicon",
                        $favicon
                    );
                    $data["checkout_favicon"] = $amazonPathFavicon;
                }
            }

            $banner = $request->file("checkout_banner");
            if (!empty($banner)) {
                $amazonFileService->deleteFile($config->checkout_banner);
                $amazonPathBanner = $amazonFileService->uploadFile(
                    "uploads/user/" . $userId . "/public/projects/" . hashids_encode($id) . "/banner",
                    $banner
                );
                $data["checkout_banner"] = $amazonPathBanner;
            }

            if ($data["company_id"] !== $config->company_id) {
                $bankAccount = Company::find($data["company_id"])->getDefaultBankAccount();
                $data["pix_enabled"] =
                    !empty($bankAccount) && $bankAccount->transfer_type == "PIX" && $data["pix_enabled"];
                // update company_id at users_projects table
                UserProject::where("project_id", $config->project_id)->update(["company_id" => $data["company_id"]]);
                // update company_default at users table
                $user = Auth::user();
                $user->company_default = $data["company_id"];
                $user->save();
            }

            if (empty($data["support_phone"])) {
                $data["support_phone_verified"] = 0;
            }

            $config->update($data);

            CacheService::forget(CacheService::CHECKOUT_PROJECT, $config->project_id);

            return new CheckoutConfigResource($config);
        } catch (\Exception $e) {
            report($e);
            return foxutils()->isProduction()
                ? response()->json(["message" => "Erro ao atualizar as configurações do checkout"])
                : response()->json(["message" => $e->getMessage()]);
        }
    }

    public function sendSupportPhoneVerification(SendSupportPhoneVerificationRequest $request)
    {
        try {
            $data = $request->all();

            $configId = hashids_decode($data["id"]);
            $supportPhone = $data["support_phone"];

            $config = CheckoutConfig::find($configId);

            activity()
                ->on($config)
                ->tap(function (Activity $activity) use ($config) {
                    $activity->log_name = "visualization";
                    $activity->subject_id = $config->id;
                })
                ->log("Enviou o código de verificação do telefone de suporte");

            $config->support_phone = $supportPhone;
            $config->save();

            $verificationCode = random_int(100000, 999999);
            $message = "Código de verificação Admin: " . $verificationCode;
            $smsService = new SmsService();
            $smsService->sendSms($supportPhone, $message);

            $cookieName =
                "supportphoneverificationcode_" .
                hashids_encode(auth()->id()) .
                "_" .
                hashids_encode($config->project_id);

            return response()
                ->json(["message" => "Mensagem enviada com sucesso!"])
                ->withCookie($cookieName, $verificationCode, 15);
        } catch (\Exception $e) {
            report($e);
            return foxutils()->isProduction()
                ? response()->json(["message" => "Ocorreu um erro ao enviar sms para o telefone informado!"])
                : response()->json(["message" => $e->getMessage()]);
        }
    }

    public function verifySupportPhone(VerifySupportPhoneRequest $request)
    {
        try {
            $data = $request->all();

            $configId = hashids_decode($data["id"]);
            $config = CheckoutConfig::find($configId);

            activity()
                ->on($config)
                ->tap(function (Activity $activity) use ($config) {
                    $activity->log_name = "updated";
                    $activity->subject_id = $config->id;
                })
                ->log("Verificação do código do telefone de suporte");

            $cookieName =
                "supportphoneverificationcode_" .
                hashids_encode(auth()->id()) .
                "_" .
                hashids_encode($config->project_id);
            $cookie = Cookie::get($cookieName);

            if ($data["verification_code"] !== $cookie) {
                return response()->json(["message" => "Código de verificação inválido!"], 400);
            }

            $config->update(["support_phone_verified" => true]);

            return response()
                ->json(["message" => "Telefone verificado com sucesso!"])
                ->withCookie(Cookie::forget($cookieName));
        } catch (\Exception $e) {
            report($e);
            return foxutils()->isProduction()
                ? response()->json(["message" => "Ocorreu um erro ao verificar código!"])
                : response()->json(["message" => $e->getMessage()]);
        }
    }

    public function sendSupportEmailVerification(SendSupportEmailVerificationRequest $request)
    {
        try {
            $data = $request->all();

            $configId = hashids_decode($data["id"]);
            $supportEmail = $data["support_email"];

            $config = CheckoutConfig::find($configId);

            activity()
                ->on($config)
                ->tap(function (Activity $activity) use ($config) {
                    $activity->log_name = "visualization";
                    $activity->subject_id = $config->id;
                })
                ->log("Enviou o código de verificação do e-mail de suporte");

            $config->support_email = $supportEmail;
            $config->save();

            $verificationCode = random_int(100000, 999999);

            $sendgridService = app(SendgridService::class);
            $sendgridService->sendEmail(
                "noreply@nexuspay.com.br",
                "NexusPay",
                $supportEmail,
                auth()->user()->name,
                "d-bab201a0bccb43b79ede4e5cb9b5937c", // done
                ["verify_code" => $verificationCode]
            );

            $cookieName =
                "supportemailverificationcode_" .
                hashids_encode(auth()->id()) .
                "_" .
                hashids_encode($config->project_id);

            return response()
                ->json(["message" => "E-mail enviado com sucesso!"])
                ->withCookie($cookieName, $verificationCode, 15);
        } catch (\Exception $e) {
            report($e);
            return foxutils()->isProduction()
                ? response()->json(["message" => "Erro ao enviar o e-mail com o código de verificação!"])
                : response()->json(["message" => $e->getMessage()]);
        }
    }

    public function verifySupportEmail(VerifySupportEmailRequest $request)
    {
        try {
            $data = $request->all();

            $configId = hashids_decode($data["id"]);
            $config = CheckoutConfig::find($configId);

            activity()
                ->on($config)
                ->tap(function (Activity $activity) use ($config) {
                    $activity->log_name = "updated";
                    $activity->subject_id = $config->id;
                })
                ->log("Verificação do código do e-mail de suporte");

            $cookieName =
                "supportemailverificationcode_" .
                hashids_encode(auth()->id()) .
                "_" .
                hashids_encode($config->project_id);
            $cookie = Cookie::get($cookieName);

            if ($data["verification_code"] !== $cookie) {
                return response()->json(["message" => "Código de verificação inválido!"], 400);
            }

            $config->update(["support_email_verified" => true]);

            return response()
                ->json(["message" => "E-mail verificado com sucesso!"])
                ->withCookie(Cookie::forget($cookieName));
        } catch (\Exception $e) {
            report($e);
            return foxutils()->isProduction()
                ? response()->json(["message" => "Ocorreu um erro ao verificar código!"])
                : response()->json(["message" => $e->getMessage()]);
        }
    }
}
