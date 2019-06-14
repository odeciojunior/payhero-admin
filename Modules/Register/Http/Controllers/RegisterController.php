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
        $invite = Invitation::where('parameter', $parameter)->first();

        if ($invite == null) {
            echo 'convite não encontrado';
            die;
        }

        return view('register::create', [ 
            'invite' => $invite,
        ]);
    }

    public function store(RegisterRequest $request)
    {
        try{
            $requestData = $request->validated();

            $dados = $request->all();

            $invite = Invitation::find($dados['invite']);

            $dados['password']                            = bcrypt($dados['password']);
            $dados['percentage_rate']                     = '6.5';
            $dados['transaction_rate']                    = '1.00';
            $dados['balance']                             = '0';
            $dados['foxcoin']                             = '0';
            $dados['credit_card_antecipation_money_days'] = '15';
            $dados['release_money_days']                  = '30';
            $dados['boleto_antecipation_money_days']      = '7'; 
            $dados['antecipation_tax']                    = '5.0';
            $dados['percentage_antecipable']              = '80';
            $dados['email_amount']                        = '0';
            $dados['call_amount']                         = '0';
            $dados['score']                               = '0';

            $user = User::create($dados);

            $user->assignRole('administrador empresarial');

            auth()->loginUsingId($user['id']);

            // $invite->update([
            //     'user_invited'    => $user->id,
            //     'status'          => 'Ativo',
            //     'register_date'   => Carbon::now()->format('Y-m-d'),
            //     'expiration_date' => Carbon::now()->addMonths(12)->format('Y-m-d'),
            //     'email_invited'   => $dados['email'],
            // ]);

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


