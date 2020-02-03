<?php

namespace Modules\Partners\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\User;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Entities\UserProject;
use Modules\Core\Helpers\StringHelper;
use Yajra\DataTables\Facades\DataTables;
use Modules\Partners\Transformers\PartnersResource;
use Modules\Partners\Http\Requests\PartnersStoreRequest;
use Modules\Partners\Http\Requests\PartnersUpdateRequest;

class PartnersController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $userProjectModel = new UserProject();
            if ($request->input('project')) {
                $projectId = current(Hashids::decode($request->input('project')));

                $partners = $userProjectModel->with('userId')->where('project_id', $projectId)
                    ->where('type_enum', '!=', $userProjectModel->present()->getTypeEnum('producer'))->get();

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
     * @param PartnersStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PartnersStoreRequest $request)
    {
        try {
            $userModel = new User();
            $companyModel = new Company();
            $invitationModel = new Invitation();
            $userProjectModel = new UserProject();

            $requestvalidated = $request->validated();
            if ($requestvalidated) {

                $user = $userModel->where('email', $requestvalidated['email_invited'])
                    ->first();
                $requestvalidated['project'] = current(Hashids::decode($requestvalidated['project']));
                if ($user) {
                    $company = $companyModel->where('user', $user->account_owner_id)->first();
                    $requestvalidated['status'] = 'active';

                    if ($company) {
                        $requestvalidated['company'] = $company->id;
                    }
                } else {
                    $requestDataInvitation['status'] = 'convite enviado';

                    $requestDataInvitation['email_invited'] = $requestvalidated['email_invited'];

                    $parameter = false;

                    while (!$parameter) {
                        $parameter = StringHelper::randString(15);
                        $invite = $invitationModel->where('parameter', $parameter)->first();

                        if (!$invite) {
                            $parameter = true;
                            $requestDataInvitation['parameter'] = $parameter;
                        }
                    }

                    $requestDataInvitation['company'] = $companyModel->where('user_id',
                        auth()->user()->account_owner_id)
                        ->first()->id;
                    $requestDataInvitation['invite'] = auth()->user()->account_owner_id;
                    $invite = $invitationModel->create($requestDataInvitation);
                    /*Mail::send('convites::email_convite', ['convite' => $invite], function($mail) use ($requestDataInvitation) {
                        $mail->from('teste@teste', 'cloudfox');
                        $mail->to($requestDataInvitation['email_invited'], 'Cloudfox')
                             ->subject('Convite para participar de um projeto no Cloudfox');
                    });*/
                }
                $requestvalidated['status'] = 'inactive';
                $requestvalidated['status_enum'] = $userProjectModel->present()->getTypeFlag('inactive');
                $requestvalidated['user'] = $user->account_owner_id ?? null;
                $userProjectModel->create($requestvalidated);

                return response()->json('success');
            }
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
            $userProjectModel = new UserProject();
            if ($request->input('data')) {
                $partnerId = current(Hashids::decode($request->input('data')));
                $partner = $userProjectModel->with(['userId'])->find($partnerId);

                if ($partner) {
                    $view = view("partners::details", ['partner' => $partner, 'user' => $partner->userId]);

                    return response()->json($view->render(), 200);
                }

                return response()->json('erro', 402);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao acessar detalhes do parceiro (PartnersController - show)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function edit(Request $request)
    {

        $requestData = $request->all();

        $partner = UserProject::where('id', Hashids::decode($requestData['id_parceiro']))->first();
        $idParceiro = Hashids::encode($partner->id);

        $user = User::find($partner->user);

        $form = view('parceiros::editar', [
            'id_parceiro' => $idParceiro,
            'parceiro' => $partner,
            'user' => $user,
        ]);

        return response()->json($form->render());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PartnersUpdateRequest $request, $id)
    {
        try {
            $userProjectModel = new UserProject();

            $requestValidated = $request->validated();
            if ($requestValidated) {
                $userProjectId = current(Hashids::decode($id));
                $partner = $userProjectModel->where('id', $userProjectId)->first();
                $partnerDeleted = $partner->update($requestValidated);
                if ($partnerDeleted) {
                    return response()->json('Parceito atualizado com sucesso!', 200);
                }
            }

            return response()->json('Erro ao tentar atualizar dados!', 402);
        } catch (Exception $e) {
            Log::warning('Erro ao tenta atualizar dados');
            report($e);
        }

        $requestData = $request->all();

        unset($requestData['projeto']);

        $partner = UserProject::where('id', Hashids::decode($requestData['id']))->first();

        $partner->update($requestData);

        return response()->json('sucesso');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $userProjectModel = new UserProject();
            if ($id) {
                $userProject = current(Hashids::decode($id));
                $partner = $userProjectModel->where('id', $userProject)->first();
                $partnerDeleted = $partner->delete();
                if ($partnerDeleted) {
                    return response()->json('Parceiro removido com sucesso', 200);
                }
            }

            return response()->json('Erro ao tentar deletar parceiro', 422);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar remover parceiro (PartnersController - destroy)');
            report($e);
        }
    }
}
