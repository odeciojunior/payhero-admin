<?php

namespace Modules\Partners\Http\Controllers;

use App\Entities\User;
use App\Entities\Company;
use App\Entities\Invitation;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Helpers\StringHelper;
use Yajra\DataTables\Facades\DataTables;

class PartnersController extends Controller
{
    private $userModel;
    private $companyModel;
    private $invitationModel;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getUser()
    {
        if (!$this->userModel) {
            $this->userModel = app(User::class);
        }

        return $this->userModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getCompany()
    {
        if (!$this->companyModel) {
            $this->companyModel = app(Company::class);
        }

        return $this->companyModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getInvitation()
    {
        if (!$this->invitationModel) {
            $this->invitationModel = app(Invitation::class);
        }

        return $this->invitationModel;
    }

    public function index(Request $request)
    {

        $requestData = $request->all();

        $partners = \DB::table('projects as project')
                       ->leftJoin('users_projects as user_project', 'project.id', 'user_project.project')
                       ->leftJoin('users as user', 'user_project.user', 'user.id')
                       ->where('type', '!=', 'producer')
            // ->where('user_project.user','<',\Auth::user()->id)
            // ->orWhereNull('user_project.user')
                       ->where('project.id', Hashids::decode($requestData['projeto']))
                       ->get([
                                 'user_project.id',
                                 'user.name',
                                 'user_project.type',
                                 'user_project.status',
                             ]);

        return Datatables::of($partners)
                         ->addColumn('detalhes', function($partner) {
                             return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_parceiro' data-placement='top' data-toggle='tooltip' title='Detalhes' parceiro='" . Hashids::encode($partner->id) . "'>
                            <i class='icon wb-menu' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a class='btn btn-outline btn-primary editar_parceiro' data-placement='top' data-toggle='tooltip' title='Editar' parceiro='" . Hashids::encode($partner->id) . "'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_parceiro' data-placement='top' data-toggle='tooltip' title='Excluir' parceiro='" . Hashids::encode($partner->id) . "'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
                         })
                         ->rawColumns(['detalhes'])
                         ->make(true);
    }

    public function store(Request $request)
    {

        $requestData            = $request->all();
        $requestData['projeto'] = Hashids::decode($requestData['projeto'])[0];

        $user = User::where('email', $requestData['email_parceiro'])->first();

        if ($user != null) {
            $requestData['user']   = $user['id'];
            $requestData['status'] = 'ativo';
            $companies             = Company::where('user', $user['id'])->get()->toArray();

            if (count($companies) > 0) {
                foreach ($companies as $company) {
                    $requestData['empresa'] = $company['id'];
                    break;
                }
            }
        } else {
            $requestData['status'] = 'convite enviado';

            $requestDataConvite['email_convidado'] = $requestData['email_parceiro'];
            $requestDataConvite['user_convite']    = \Auth::user()->id;
            $requestDataConvite['status']          = "Convite enviado";

            $novoParametro = false;

            while (!$novoParametro) {

                $parametro = StringHelper::randString(15);

                $convite = Invitation::where('parametro', $parametro)->first();

                if ($convite == null) {
                    $novoParametro                   = true;
                    $requestDataConvite['parametro'] = $parametro;
                }
            }

            $requestDataConvite['empresa'] = @Company::where('user', \Auth::user()->id)->first()->id;

            $convite = Invitation::create($requestDataConvite);

            Mail::send('convites::email_convite', ['convite' => $convite], function($mail) use ($requestDataConvite) {
                $mail->from('julioleichtweis@gmail.com', 'Cloudfox');

                $mail->to($requestDataConvite['email_convidado'], 'Cloudfox')
                     ->subject('Convite para participar de um projeto no Cloudfox!');
            });
        }

        if (isset($requestData['responsavel_frete']) && $requestData['responsavel_frete'] == 'on') {
            $requestData['responsavel_frete'] = true;
        }

        UserProject::create($requestData);

        return response()->json('sucesso');
    }

    public function update(Request $request)
    {

        $requestData = $request->all();

        unset($requestData['projeto']);

        $partner = UserProjeto::where('id', Hashids::decode($requestData['id']))->first();

        $partner->update($requestData);

        return response()->json('sucesso');
    }

    public function delete(Request $request)
    {

        $requestData = $request->all();

        $partner = UserProjeto::where('id', Hashids::decode($requestData['id']))->first();

        $partner->delete();

        return response()->json('sucesso');
    }

    public function create()
    {

        $form = view('parceiros::cadastro');

        return response()->json($form->render());
    }

    public function edit(Request $request)
    {

        $requestData = $request->all();

        $partner    = UserProjeto::where('id', Hashids::decode($requestData['id_parceiro']))->first();
        $idParceiro = Hashids::encode($partner->id);

        $user = User::find($partner->user);

        $form = view('parceiros::editar', [
            'id_parceiro' => $idParceiro,
            'parceiro'    => $partner,
            'user'        => $user,
        ]);

        return response()->json($form->render());
    }

    public function details(Request $request)
    {

        $requestData = $request->all();

        $partner = UserProjeto::where('id', Hashids::decode($requestData['parceiro']))->first();

        $user = User::find($partner['user']);

        $detalhes = view('parceiros::detalhesparceiro', [
            'parceiro' => $partner,
            'user'     => $user,
        ]);

        return response()->json($detalhes->render());
    }
}
