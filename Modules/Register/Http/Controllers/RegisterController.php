<?php

namespace Modules\Register\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\User;
use Illuminate\Routing\Controller;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Register\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{

    public function create($parameter)
    {
        return view('register::create', [
            'parameter' => $parameter,
        ]);
    }

    public function store(RegisterRequest $request)
    {
        try {
            $requestData = $request->validated();

            $userModel    = new User();
            $inviteModel  = new Invitation();
            $companyModel = new Company();

            $requestData['password']                            = bcrypt($requestData['password']);
            $requestData['percentage_rate']                     = '6.5';
            $requestData['transaction_rate']                    = '1.00';
            $requestData['balance']                             = '0';
            $requestData['foxcoin']                             = '0';
            $requestData['credit_card_antecipation_money_days'] = '15';
            $requestData['release_money_days']                  = '30';
            $requestData['boleto_antecipation_money_days']      = '7';
            $requestData['antecipation_tax']                    = '5.0';
            $requestData['percentage_antecipable']              = '80';
            $requestData['email_amount']                        = '0';
            $requestData['call_amount']                         = '0';
            $requestData['score']                               = '0';
            $requestData['sms_zenvia_amount']                   = '0';

            $user = $userModel->create($requestData);

            $user->assignRole('administrador empresarial');

            auth()->loginUsingId($user['id']);

            $invite = $inviteModel->where('email_invited', $requestData['email'])->first();

            if ($invite) {
                $invite->update([
                                    'user_invited'    => $user->id,
                                    'status'          => '1',
                                    'register_date'   => Carbon::now()->format('Y-m-d'),
                                    'expiration_date' => Carbon::now()->addMonths(12)->format('Y-m-d'),
                                    'email_invited'   => $requestData['email'],
                                ]);
            } else {

                $company = $companyModel->find(current(Hashids::decode($requestData['parameter'])));

                if ($company) {
                    $inviteModel->create([
                                           'invite'          => null,
                                           'user_invited'    => $user->id,
                                           'status'          => '1',
                                           'company'         => $company->id,
                                           'register_date'   => Carbon::now()->format('Y-m-d'),
                                           'expiration_date' => Carbon::now()->addMonths(12)->format('Y-m-d'),
                                           'email_invited'   => $requestData['email'],
                                       ]);
                }
            }

            return response()->json([
                                        'success' => 'true',
                                    ]);
        } catch (Exception $ex) {
            Log::warning('Erro ao registrar novo usuario (RegisterController - store)');
            report($ex);

            return response()->json([
                                        'success' => 'false',
                                    ]);
        }
    }

    public function welcomeEmail()
    {
        $userEmail       = auth()->user()->email;
        $userName        = auth()->user()->name;
        $sendgridService = new SendgridService();
        $data            = [
            "name" => $userName,
        ];
        $emailValidated = FoxUtils::validateEmail($userEmail);
        if ($emailValidated) {
            $sendgridService->sendEmail('noreply@cloudfox.net', 'cloudfox', $userEmail, $userName, 'd-267dbdcbcc5a454e94a5ae3ffb704505', $data);
        }
    }

    public function loginAsSomeUser($userId){

        auth()->loginUsingId($userId);

        $companyModel = new Company();

        $companies = $companyModel->where('user_id',\Auth::user()->id)->get();

        return response()->redirectTo('/dashboard');
    }
}


