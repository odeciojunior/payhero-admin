<?php

namespace Modules\Affiliates\Http\Controllers;

use App\Entities\Company;
use App\Entities\ExtraMaterial;
use App\Entities\Notification;
use Carbon\Carbon;
use App\Entities\Project;
use App\Entities\Affiliate;
use Illuminate\Http\Request;
use App\Entities\AffiliateRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;
use Modules\Notifications\Notifications\NewAffiliation;
use Modules\Notifications\Notifications\ApprovedAffiliation;
use Modules\Notifications\Notifications\NewAffiliationRequest;

/**
 * Class AffiliatesController
 * @package Modules\Affiliates\Http\Controllers
 */
class AffiliatesController extends Controller
{
    /**
     * @param $projectId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create($projectId)
    {
        $userModel             = new User();
        $affiliateModel        = new Affiliate();
        $projectModel          = new Project();
        $userProjectModel      = new UserProject();
        $notificationModel     = new Notification();
        $affiliateRequestModel = new AffiliateRequest();
        $companyModel          = new Company();

        $project = $projectModel->where('id', Hashids::decode($projectId))->first();

        $userProject = $userProjectModel->where([
                                                    ['project', $project['id']],
                                                    ['tipo', 'producer'],
                                                ])->first();

        $user = $userModel->find($userProject['user']);

        if (!$project['automatic_affiliation']) {

            $notification = $notificationModel->where([
                                                          ['notifiable_id', $user['id']],
                                                          ['type', 'Modules\Notificacoes\Notifications\NewAffiliationRequest'],
                                                      ])
                                              ->whereNull('read_at')
                                              ->first();

            if ($notification) {
                $data = json_decode($notification['data']);
                $notification->update([
                                          'data' => json_encode(['qtd' => preg_replace("/[^0-9]/", "", $data->qtd) + 1]),
                                      ]);
            } else {
                $user->notify(new NewAffiliationRequest());
            }

            $affiliateRequestModel->create([
                                               'user'    => auth()->user()->id,
                                               'project' => $project['id'],
                                               'status'  => 'Pendente',
                                           ]);

            \Session::flash('success', "Solicitação de afiliação enviada para o produtor do projeto!");

            return redirect()->route('affiliates.my_affiliations');
        }

        $company = $companyModel->where('user', auth()->user()->id)->first();

        $affiliate = $affiliateModel->create([
                                                 'user'       => auth()->user()->id,
                                                 'project'    => $project['id'],
                                                 'percentage' => $project['percentage_affiliates'],
                                                 'company'    => @$company->id,
                                             ]);

        $notification = $notificationModel->where([
                                                      ['notifiable_id', $user['id']],
                                                      ['type', 'Modules\Notificacoes\Notifications\NewAffiliation'],
                                                  ])
                                          ->whereNull('read_at')
                                          ->first();

        if ($notification) {
            $data = json_decode($notification['data']);
            $notification->update([
                                      'data' => json_encode(['qtd' => preg_replace("/[^0-9]/", "", $data->qtd) + 1]),
                                  ]);
        } else {
            $user->notify(new NewAffiliation());
        }

        return redirect()->route('affiliates.minhasafiliacoes');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmAffiliation(Request $request)
    {

        $affiliateRequestModel = new AffiliateRequest();
        $projectModel          = new Project();
        $userModel             = new User();
        $affiliateModel        = new Affiliate();
        $notificationModel     = new Notification();
        $companyModel          = new Company();

        $requestData = $request->all();

        $affiliationRequest = $affiliateRequestModel->where('id', Hashids::decode($requestData['id']))->first();

        $project = $projectModel->where('id', $affiliationRequest['project'])->first();

        $user = $userModel->find($affiliationRequest['user']);

        $company = $companyModel->where('user', $user['id'])->first();

        $affiliate = $affiliateModel->create([
                                                 'user'       => $user['id'],
                                                 'project'    => $project['id'],
                                                 'percentage' => $project['percentage_affiliates'],
                                                 'company'    => @$company->id,
                                             ]);

        $affiliationRequest->update([
                                        'status' => 'Confirmada',
                                    ]);

        $notification = $notificationModel->where([
                                                      ['notifiable_id', $user['id']],
                                                      ['type', 'Modules\Notificacoes\Notifications\ApprovedAffiliation'],
                                                  ])
                                          ->whereNull('read_at')
                                          ->first();

        if ($notification) {
            $data = json_decode($notification['data']);
            $notification->update([
                                      'data' => json_encode(['qtd' => preg_replace("/[^0-9]/", "", $data->qtd) + 1]),
                                  ]);
        } else {
            $user->notify(new ApprovedAffiliation());
        }

        return response()->json('Sucesso');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $affiliateModel = new Affiliate();

        $requestData = $request->all();

        $affiliate = $affiliateModel->where('id', Hashids::decode($requestData['affiliate']))->first();
        $affiliate->delete();

        return response()->json('sucesso');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function myAffiliates()
    {

        return view('affiliates::my_affiliates');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function myAffiliations()
    {
        $affiliateModel = new Affiliate();
        $projectModel   = new Project();

        $myAffiliations = $affiliateModel->where('user', auth()->user()->id)->get()->toArray();

        $projects = [];

        if (count($myAffiliations) > 0) {
            foreach ($myAffiliations as $affiliation) {

                $project             = $projectModel->find($affiliation['project']);
                $p['affiliation_id'] = Hashids::encode($affiliation['id']);
                $p['photo']          = $project['photo'];
                $p['name']           = $project['name'];
                $p['description']    = $project['description'];
                $projects[]          = $p;
            }
        }

        return view('affiliates::my_affiliations', [
            'projects' => $projects,
        ]);
    }

    /**
     * @param $affiliationId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function details($affiliationId)
    {
        $affiliateModel      = new Affiliate();
        $projectModel        = new Project();
        $userModel           = new User();
        $companyModel        = new Company();
        $userProjectModel    = new UserProject();
        $extraMaterialsModel = new ExtraMaterial();

        $affiliate  = $affiliateModel->where('id', Hashids::decode($affiliationId))->first();
        $idAfiliado = Hashids::encode($affiliate->id);

        $project = $projectModel->find($affiliate['project']);

        $companies = $companyModel->where('user', auth()->user()->id)->get()->toArray();

        $userProject = $userProjectModel->where([
                                                    ['project', $project['id']],
                                                    ['tipo', 'producer'],
                                                ])->first();
        $user        = $userModel->find($userProject['user']);

        $extraMaterials = $extraMaterialsModel->where('project', $project['id'])->get()->toArray();

        return view('affiliates::affiliation_details', [
            'affiliate_id'     => $idAfiliado,
            'project'          => $project,
            'producer'         => $user['name'],
            'companies'        => $companies,
            'affiliate'        => $affiliate,
            'extras_materials' => $extraMaterials,
        ]);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function myAffiliatesData()
    {
        $userProjectModel = new UserProject();

        $userProjects = $userProjectModel->where([
                                                     ['user', auth()->user()->id],
                                                     ['type', 'producer'],
                                                 ])->pluck('project')->toArray();

        $myAffiliates = DB::table('affiliates as affiliate')
                          ->leftJoin('users as user', 'affiliate.user', 'user.id')
                          ->leftJoin('projects as project', 'affiliate.project', 'project.id')
                          ->whereIn('project.id', $userProjects)
                          ->whereNull('affiliate.deleted_at')
                          ->select([
                                       'affiliate.id',
                                       'affiliate.deleted_at',
                                       'user.name',
                                       'affiliate.percentage',
                                       'project.name',
                                   ]);

        return Datatables::of($myAffiliates)
                         ->addColumn('detalhes', function($affiliate) {
                             return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_afiliado' data-placement='top' data-toggle='tooltip' title='Detalhes' afiliado='" . Hashids::encode($affiliate->id) . "'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                            Detalhes
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_remover_afiliado' style='margin-left:10px'>
                        <a class='remover_afiliado btn btn-outline btn-danger' data-placement='top' data-toggle='tooltip' title='Remover afiliado' afiliado='" . Hashids::encode($affiliate->id) . "'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                            Remover afiliado
                        </a>
                    </span>";
                         })
                         ->rawColumns(['detalhes'])
                         ->make(true);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function pendingAffiliations()
    {
        $userProjectModel = new UserProject();

        $userProjects = $userProjectModel->where([
                                                     ['user', auth()->user()->id],
                                                     ['type', 'producer'],
                                                 ])->pluck('project')->toArray();

        $affiliationsRequests = DB::table('affiliate_requests as affiliate_request')
                                  ->leftJoin('users as user', 'affiliate_request.user', 'user.id')
                                  ->leftJoin('projects as project', 'affiliate_request.project', 'project.id')
                                  ->whereIn('affiliate_request.project', $userProjects)
                                  ->whereNull('affiliate_request.deleted_at')
                                  ->where('affiliate_request.status', 'pending')
                                  ->select([
                                               'affiliate_request.id',
                                               'user.name',
                                               'project.percentage_affiliates',
                                               'affiliate_request.created_at as data_solicitacao',
                                               'project.name',
                                           ]);

        return Datatables::of($affiliationsRequests)
                         ->editColumn('data_solicitacao', function($affiliate) {
                             return Carbon::parse($affiliate->data_solicitacao)->format('d/m/Y H:i');
                         })
                         ->addColumn('detalhes', function($affiliationRequest) {
                             return "<span>
                        <a class='btn btn-outline btn-success confirmar_afiliacao' data-placement='top' data-toggle='tooltip' title='Confirmar' affiliate_request='" . Hashids::encode($affiliationRequest->id) . "'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                            Confirmar afiliação
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_cancelar_solicitacao'>
                        <a class='cancelar_solicitacao btn btn-outline btn-danger' data-placement='top' data-toggle='tooltip' title='cancelar solicitação' affiliate_request='" . Hashids::encode($affiliationRequest->id) . "'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                            Negar solicitação
                        </a>
                    </span>";
                         })
                         ->rawColumns(['detalhes'])
                         ->make(true);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function myPendingAffiliations()
    {

        $affiliationsRequests = DB::table('affiliate_requests as affiliate_request')
                                  ->leftJoin('projects as project', 'project.id', '=', 'affiliate_request.project')
                                  ->whereNull('affiliate_request.deleted_at')
                                  ->where('affiliate_request.user', auth()->user()->id)
                                  ->whereIn('affiliate_request.status', ['pending', 'denied'])
                                  ->select([
                                               'affiliate_request.id',
                                               'affiliate_request.project',
                                               'affiliate_request.status',
                                               'project.name',
                                               'affiliate_request.created_at as data_solicitacao',
                                           ]);

        return Datatables::of($affiliationsRequests)
                         ->editColumn('data_solicitacao', function($affiliationRequest) {
                             return Carbon::parse($affiliationRequest->data_solicitacao)->format('d/m/Y H:i');
                         })
                         ->addColumn('detalhes', function($affiliationRequest) {
                             return "<span data-toggle='modal' data-target='#modal_cancelar_solicitacao'>
                        <a class='cancelar_solicitacao btn btn-outline btn-danger' data-placement='top' data-toggle='tooltip' title='cancelar solicitação' affiliate_request='" . Hashids::encode($affiliationRequest->id) . "'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                            Cancelar solicitação
                        </a>
                    </span>";
                         })
                         ->rawColumns(['detalhes'])
                         ->make(true);
    }

    /**
     * @param $projectId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function projectAffiliations($projectId)
    {
        $affiliateModel = new Affiliate();
        $projectModel   = new Project();
        $userModel      = new User();

        $project = $projectModel->where('id', $projectId)->first();

        $affiliates = $affiliateModel->where('project', $projectId)->get()->toArray();

        foreach ($affiliates as &$affiliate) {
            $user              = $userModel->find($affiliate['user']);
            $affiliate['name'] = $user['name'];
        }

        $view = view('affiliates::afiliados_project', [
            'project'   => $project,
            'afiliados' => $affiliates,
        ]);

        return response()->json($view->render());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setAffiliationCompany(Request $request)
    {
        $affiliateModel = new Affiliate();

        $requestData = $request->all();

        $affiliate = $affiliateModel->where('id', $requestData['affiliate'])->first();
        $affiliate->update($requestData);

        return response()->json('sucesso');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelRequest(Request $request)
    {
        $affiliationRequestModel = new AffiliateRequest();

        $requestData = $request->all();

        $request = $affiliationRequestModel->where('id', Hashids::decode($requestData['id_solicitacao']))->first();
        $request->delete();

        return response()->json('sucesso');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function denyRequest(Request $request)
    {
        $affiliationRequestModel = new AffiliateRequest();

        $requestData = $request->all();

        $request = $affiliationRequestModel->where('id', Hashids::decode($requestData['id_solicitacao']))->first();

        $request->update([
                             'status' => 'denied',
                         ]);

        return response()->json('sucesso');
    }
}
