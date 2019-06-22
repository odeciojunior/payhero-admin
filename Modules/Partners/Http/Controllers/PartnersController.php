<?php

namespace Modules\Partners\Http\Controllers;

use App\Entities\User;
use App\Entities\Company;
use App\Entities\Invitation;
use Exception;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Partners\Http\Requests\PartnersStoreRequest;
use Modules\Partners\Transformers\PartnersResource;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Helpers\StringHelper;
use Yajra\DataTables\Facades\DataTables;

class PartnersController extends Controller
{
    private $userModel;
    private $companyModel;
    private $invitationModel;
    private $projectModel;
    private $userProject;

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

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getProject()

    {
        if (!$this->projectModel) {
            $this->projectModel = app(Invitation::class);
        }

        return $this->projectModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getUserProject()
    {
        if (!$this->userProject) {
            $this->userProject = app(UserProject::class);
        }

        return $this->userProject;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            if ($request->input('project')) {
                $projectId = current(Hashids::decode($request->input('project')));

                $partners = $this->getUserProject()->with('userId')->where('project', $projectId)
                                 ->where('type', '!=', 'producer')->get();

                return PartnersResource::collection($partners);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados (PartnersController - index)');
            report($e);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        try {
            return view('partners::create');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar pagina de cadastro de parceiro (PartnersController - create)');
            report($e);
        }
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        try {
            dd($request->all());
            $requestValidate = $request->validated();
        } catch (Exception $e) {
            Log::warning('Erro ao tentar salvar parceiro (PartenersController - store)');
            report($e);
        }
        /*$requestData            = $request->all();
        $requestData['projeto'] = current(Hashids::decode($requestData['projeto']));

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

        return response()->json('sucesso');*/
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function show(Request $request)
    {
        try {


        } catch (Exception $e) {
            Log::warning('Erro ao acessar detalhes do parceiro');
            report($e);
        }

        $requestData = $request->all();

        $partner = UserProjeto::where('id', Hashids::decode($requestData['parceiro']))->first();

        $user = User::find($partner['user']);

        $detalhes = view('parceiros::detalhesparceiro', [
            'parceiro' => $partner,
            'user'     => $user,
        ]);

        return response()->json($detalhes->render());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {

        $requestData = $request->all();

        unset($requestData['projeto']);

        $partner = UserProjeto::where('id', Hashids::decode($requestData['id']))->first();

        $partner->update($requestData);

        return response()->json('sucesso');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {

        $requestData = $request->all();

        $partner = UserProjeto::where('id', Hashids::decode($requestData['id']))->first();

        $partner->delete();

        return response()->json('sucesso');
    }
}
