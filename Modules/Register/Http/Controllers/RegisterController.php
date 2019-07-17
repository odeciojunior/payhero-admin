<?php

namespace Modules\Register\Http\Controllers;

use App\Entities\Company;
use Carbon\Carbon;
use App\Entities\User;
use App\Entities\Invitation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Register\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
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

            $user = User::create($requestData);

            $user->assignRole('administrador empresarial');

            auth()->loginUsingId($user['id']);

            $invite = Invitation::where('email_invited', $requestData['email'])->first();

            if ($invite) {
                $invite->update([
                                    'user_invited'    => $user->id,
                                    'status'          => '1',
                                    'register_date'   => Carbon::now()->format('Y-m-d'),
                                    'expiration_date' => Carbon::now()->addMonths(12)->format('Y-m-d'),
                                    'email_invited'   => $requestData['email'],
                                ]);
            } else {

                $company = Company::find(current(Hashids::decode($requestData['parameter'])));

                if ($company) {
                    Invitation::create([
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
        $userEmail      = auth()->user()->email;
        $userName       = auth()->user()->name;
        $sendEmail      = new SendgridService();
        $data           = [
            "name" => $userName,
        ];
        $emailValidated = FoxUtils::validateEmail($userEmail);
        if ($emailValidated) {
            $sendEmail->sendEmail('Bem vindo(a)', 'noreply@cloudfox.net', 'cloudfox', $userEmail, $userName, 'd-267dbdcbcc5a454e94a5ae3ffb704505', $data);
        }
    }

    public function loginAsSomeUser($userId){

        auth()->loginUsingId($userId);

        $companies = Company::where('user_id',\Auth::user()->id)->get()->toArray();

        return view('dashboard::dashboard',[
            'companies' => $companies,
        ]);
    }
}


