<?php

namespace Modules\Register\Http\Controllers;

use Carbon\Carbon;
use App\Entities\User;
use App\Entities\Invitation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class RegisterController extends Controller {

    public function create($parameter) {

        $invite = Invitation::where('parameter',$parameter)->first();

        if($invite == null){
            echo 'convite não encontrado';
            die;
        }

        return view('register::create', [
            'invite' => $invite
        ]);
    }

    public function store(Request $request){

        $dados = $request->all();

        $invite = Invitation::find($dados['id_convite']);

        $user = User::where('email', $dados['email'])->first();

        if($user != null){
            return view('register::create', [
                'invite' => $invite,
                'erro'   => 'Email já esta sendo utilizado'
            ]);
        }

        $dados['password'] = bcrypt($dados['password']);

        $dados['percentage_rate'] = '9.9';

        $dados['antecipation_days'] = '30';

        $user = User::create($dados);

        $user->assignRole('administrador empresarial');

        $invite->update([
            'user_invited'    => $user->id,
            'status'          => 'Ativo',
            'register_date'   => Carbon::now()->format('Y-m-d'),
            'expiration_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
            'email_invited'   => $dados['email'],
        ]);

        return response()->json('sucesso');
    }

}


