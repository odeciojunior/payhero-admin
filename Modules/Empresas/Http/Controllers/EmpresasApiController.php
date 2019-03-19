<?php

namespace Modules\Empresas\Http\Controllers;

use App\Empresa;
use PagarMe\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Empresas\Transformers\EmpresasResource;

class EmpresasApiController extends Controller {


    public function index()  {

        $empresas = Empresa::where('user',\Auth::user()->id);

        return EmpresasResource::collection($empresas->paginate());
    }

    public function create(Request $request){

        $dados = $request->all();

        $dados['user'] = \Auth::user()->id;

        $empresa = Empresa::create($dados);

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        try {
            $bankAccount = $pagarMe->bankAccounts()->create([
                'bank_code' => $dados['banco'],
                'agencia' => $dados['agencia'],
                'agencia_dv' => $dados['agencia_digito'],
                'conta' => $dados['conta'],
                'conta_dv' => $dados['conta_digito'],
                'document_number' => $dados['cnpj'],
                'legal_name' => $dados['nome_fantasia']
            ]);
        }
        catch(\Exception $e){
            return response()->json('Empresa cadastrada, porém os dados bancários informados são inválidos');
        }

        $recipient = $pagarMe->recipients()->create([
            'anticipatable_volume_percentage' => '80',
            'automatic_anticipation_enabled' => 'false',
            'bank_account_id' => $bankAccount->id,
            'transfer_enabled' => 'true',
        ]);

        $empresa->update([
            'bank_account_id' => $bankAccount->id,
            'recipient_id'    => $recipient->id
        ]);


        return response()->json("sucesso");
    }

    public function show($id){

        $empresa = Empresa::select(
            'cnpj', // vale tanto pra cnpj quanto pra cpf
            'nome_fantasia',
            'cep',
            'uf',
            'municipio',
            'bairro',
            'logradouro',
            'numero',
            'complemento',
            'banco',
            'agencia',
            'agencia_digito',
            'conta',
            'conta_digito'
        )->where('id',$id)->first();
        

        return response()->json($empresa);
    }

    public function update(Request $request){

        $dados = $request->all();

        if(!isset($dados['id'])){
            return response()->json('id não informado');
        }

        $empresa = Empresa::find($dados['id']);

        if(!$empresa){
            return response()->json('empresa não encontrada');
        }

        $empresa->update($dados);

        return response()->json('sucesso');
    }

    public function delete($id){

        $empresa = Empresa::find($id);

        if(!$empresa){
            return response()->json('empresa não encontrada');
        }

        $empresa->delete();

        return response()->json('sucesso');
    }


}
