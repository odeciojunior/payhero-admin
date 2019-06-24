<?php

namespace Modules\Register\Http\Controllers;

use Carbon\Carbon;
use App\Entities\User;
use App\Entities\Invitation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
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
        try{
            $requestData = $request->validated();

            $requestData = $request->all();

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

            $invite = Invitation::where('email',$requestData['email'])->first();

            if($invite){
                $invite->update([
                    'user_invited'    => $user->id,
                    'status'          => '1',
                    'register_date'   => Carbon::now()->format('Y-m-d'),
                    'expiration_date' => Carbon::now()->addMonths(12)->format('Y-m-d'),
                    'email_invited'   => $requestData['email'],
                ]);
            }
            else{

                $company = Company::find(current(Hashids::decode($requestData['parameter'])));

                Invite::create([
                    'invite'          => $requestData['parameter'],
                    'user_invited'    => $user->id,
                    'status'          => '1',
                    'company'         => $company->id,
                    'register_date'   => Carbon::now()->format('Y-m-d'),
                    'expiration_date' => Carbon::now()->addMonths(12)->format('Y-m-d'),
                    'email_invited'   => $requestData['email'],
                ]);
            }

            auth()->loginUsingId($user['id']);

            return response()->json([
                'success' => 'true',
            ]);

        } catch (Exception $ex) {
            Log::warning('Erro ao registrar novo usuario (RegisterController - store)');
            report($ex);
            return response()->json([
                'success' => 'false'
            ]);
        }
    }


}


