<?php

namespace Modules\Register\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\UserNotification;
use Modules\Core\Services\BankService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\UserService;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\Invitation;
use Modules\Core\Services\SendgridService;
use Modules\Register\Http\Requests\RegisterRequest;

class RegisterApiController extends Controller
{
    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function store(RegisterRequest $request)
    {
        try {
            $requestData           = $request->validated();
            $userModel             = new User();
            $inviteModel           = new Invitation();
            $companyModel          = new Company();
            $userNotificationModel = new UserNotification();

            $parameter = $requestData['parameter'];

            $withoutInvite = false;
            if($parameter == 'nw2usr3cfx') {
                $withoutInvite = true;
            } elseif (strlen($parameter) > 15) {
                $inviteId = substr($parameter, 0, 15);
                $inviteId = Hashids::decode($inviteId);
                $invite   = $inviteModel->where('email_invited', $requestData['email'])->where('id', $inviteId)
                                        ->first();
                if (!isset($invite->id) || (isset($invite->id) && $invite->status != 2)) {
                    return response()->json(['success' => 'false', 'message' => 'Convite inválido!']);
                }
            } else {
                $companyId = Hashids::decode($parameter);
                $company   = $companyModel->where('id', $companyId)->first();
                if (isset($company->id)) {
                    $invitesSent    = $inviteModel->where('invite', $company->user_id)->count();
                    $companyService = new CompanyService();
                    if (!$companyService->isDocumentValidated($company->id)) {
                        return response()->json(['success' => 'false', 'message' => 'Convite indisponivel!']);
                    }

                    if ($invitesSent >= $company->user->invites_amount) {
                        return response()->json(['success' => 'false', 'message' => 'Convite indisponivel, limite atingido!']);
                    }
                } else {
                    return response()->json(['success' => 'false', 'message' => 'Link convite inválido']);
                }
            }

            $requestData['password']                            = bcrypt($requestData['password']);
            $requestData['percentage_rate']                     = '5.9';
            $requestData['transaction_rate']                    = '1.00';
            $requestData['balance']                             = '0';
            $requestData['foxcoin']                             = '0';
            $requestData['credit_card_antecipation_money_days'] = '30';
            $requestData['release_money_days']                  = '30';
            $requestData['boleto_antecipation_money_days']      = '2';
            $requestData['antecipation_tax']                    = '0';
            $requestData['percentage_antecipable']              = '100';
            $requestData['email_amount']                        = '0';
            $requestData['call_amount']                         = '0';
            $requestData['score']                               = '0';
            $requestData['sms_zenvia_amount']                   = '0';
            $requestData['invites_amount']                      = 1;

            $user = $userModel->create($requestData);

            $user->update(['account_owner_id' => $user->id]);

            $user->assignRole('account_owner');

            $streetCompany       = $requestData['street_company'] ?? null;
            $numberCompany       = $requestData['number_company'] ?? null;
            $neighborhoodCompany = $requestData['neighborhood_company'] ?? null;
            $complementCompany   = $requestData['complement_company'] ?? null;
            $stateCompany        = $requestData['state_company'] ?? null;
            $cityCompany         = $requestData['city_company'] ?? null;
            $supportEmail        = $requestData['support_email'] ?? null;
            $supportPhone        = $requestData['support_telephone'] ?? null;

            $companyModel->create([
                                      'user_id'           => $user->account_owner_id,
                                      'fantasy_name'      => ($requestData['company_type'] == $companyModel->present()
                                                                                                           ->getCompanyType('physical person')) ? $user->name : $requestData['fantasy_name'],
                                      'company_document'  => ($requestData['company_type'] == $companyModel->present()
                                                                                                           ->getCompanyType('physical person')) ? $requestData['document'] : $requestData['company_document'],
                                      'company_type'      => $requestData['company_type'],
                                      'support_email'     => $supportEmail,
                                      'support_telephone' => $supportPhone,
                                      'street'            => $streetCompany,
                                      'number'            => $numberCompany,
                                      'neighborhood'      => $neighborhoodCompany,
                                      'complement'        => $complementCompany,
                                      'state'             => $stateCompany,
                                      'city'              => $cityCompany,
                                      'bank'              => $requestData['bank'],
                                      'agency'            => $requestData['agency'],
                                      'agency_digit'      => $requestData['agency_digit'],
                                      'account'           => $requestData['account'],
                                      'account_digit'     => $requestData['account_digit'],
                                  ]);

            if (!empty($user)) {
                $user->load(["userNotification"]);
                $userNotification = $user->userNotification ?? collect();
                if ($userNotification->isEmpty()) {
                    $userNotificationModel->create(
                        [
                            "user_id" => $user->id,
                        ]
                    );
                }
            }

            auth()->loginUsingId($user->id, true);

            if($withoutInvite == false) {
                if (!isset($invite)) {
                    $invite = $inviteModel->where('email_invited', $requestData['email'])->first();
                }
                // $company = $companyModel->find(current(Hashids::decode($requestData['parameter'])));

                if ($invite) {
                    $invite->update([
                                        'user_invited'    => $user->account_owner_id,
                                        'status'          => '1',
                                        'register_date'   => Carbon::now()->format('Y-m-d'),
                                        'expiration_date' => Carbon::now()->addMonths(12)->format('Y-m-d'),
                                        'email_invited'   => $requestData['email'],
                                    ]);

                    if (empty($invite->invite) && isset($company->id)) {
                        $invite->update([
                                            'invite' => $company->user_id,
                                        ]);
                    }
                } else {

                    if ($company) {
                        $inviteModel->create([
                                                 'invite'          => $company->user_id,
                                                 'user_invited'    => $user->account_owner_id,
                                                 'status'          => '1',
                                                 'company_id'      => $company->id,
                                                 'register_date'   => Carbon::now()->format('Y-m-d'),
                                                 'expiration_date' => Carbon::now()->addMonths(12)->format('Y-m-d'),
                                                 'email_invited'   => $requestData['email'],
                                             ]);
                    }
                }
            }

            return response()->json([
                                        'success'      => 'true',
                                        'access_token' => auth()->user()
                                                                ->createToken("Laravel Password Grant Client")->accessToken,
                                    ]);
        } catch (Exception $ex) {
            Log::warning('Erro ao registrar novo usuario (RegisterController - store)');
            report($ex);

            return response()->json([
                                        'success' => 'false',
                                        'message' => 'revise os dados informados',
                                    ]);
        }
    }

    /**
     * Send welcome e-mail
     */
    public function welcomeEmail()
    {
        $userEmail       = auth()->user()->email;
        $userName        = auth()->user()->name;
        $sendgridService = new SendgridService();
        $data            = [
            "name" => $userName,
        ];
        $emailValidated  = FoxUtils::validateEmail($userEmail);
        if ($emailValidated) {
            $sendgridService->sendEmail('noreply@cloudfox.net', 'cloudfox', $userEmail, $userName, 'd-267dbdcbcc5a454e94a5ae3ffb704505', $data);
        }
    }

    public function verifyCpf(Request $request)
    {
        $data        = $request->all();
        $userService = new UserService();
        $cpf         = $userService->verifyCpf($data['document']);
        if ($cpf) {
            return response()->json([
                                        'cpf_exist' => 'true',
                                        'message'   => 'Esse CPF já está cadastrado na plataforma',
                                    ]);
        } else {
            return response()->json([
                                        'cpf_exist' => 'false',
                                    ]);
        }
    }

    public function verifyCnpj(Request $request)
    {
        $data           = $request->all();
        $companyService = new CompanyService();
        $cnpj           = $companyService->verifyCnpj($data['company_document']);
        if ($cnpj) {
            return response()->json([
                                        'cnpj_exist' => 'true',
                                        'message'    => 'Esse CNPJ já está cadastrado na plataforma',
                                    ]);
        } else {
            return response()->json([
                                        'cnpj_exist' => 'false',
                                    ]);
        }
    }

    public function verifyEmail(Request $request)
    {
        $data      = $request->all();
        $userModel = new User();

        $user = $userModel->where('email', 'like', '%' . $data['email'] . '%')->first();
        if (!empty($user)) {
            return response()->json([
                                        'email_exist' => 'true',
                                        'message'     => 'Esse Email já está cadastrado na plataforma',
                                    ]);
        } else {
            return response()->json([
                                        'email_exist' => 'false',
                                    ]);
        }
    }

    public function getBanks()
    {
        $bankService = new BankService();
        $banks       = $bankService->getBanks('brazil');

        return response([
                            'banks' => $banks,
                        ], 200);
    }
}
