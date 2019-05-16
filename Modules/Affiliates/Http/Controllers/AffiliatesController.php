<?php

namespace Modules\Affiliates\Http\Controllers;

use Carbon\Carbon;
use App\Entities\User;
use App\Entities\Project;
use App\Entities\Affiliate;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use App\Entities\AffiliateRequest;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;
use Modules\Notificacoes\Notifications\NovaAfiliacao;
use Modules\Notificacoes\Notifications\AfiliacaoAprovada;
use Modules\Notificacoes\Notifications\NovaSolicitacaoAfiliacao;

class AffiliatesController extends Controller {

    public function create($projectId) {

        date_default_timezone_set('America/Sao_Paulo');

        $project = Project::where('id',Hashids::decode($projectId))->first();

        $userProject = UserProject::where([
            ['project', $project['id']],
            ['tipo', 'producer']
        ])->first();

        $user = User::find($userProject['user']);

        if(!$project['automatic_affiliation']){

            $notification = Notification::where([
                ['notifiable_id',$user['id']],
                ['type','Modules\Notificacoes\Notifications\NovaSolicitacaoAfiliacao']
            ])
            ->whereNull('read_at')
            ->first();

            if($notification){
                $data = json_decode($notification['data']);
                $notification->update([
                    'data' => json_encode(['qtd' => preg_replace("/[^0-9]/", "", $data->qtd) + 1 ])
                ]);
            }
            else{
                $user->notify(new NovaSolicitacaoAfiliacao());
            }

            AffiliateRequest::create([
                'user'      => \Auth::user()->id,
                'project'   => $project['id'],
                'status'    => 'Pendente'
            ]);

            \Session::flash('success', "Solicitação de afiliação enviada para o produtor do projeto!");
            return redirect()->route('affiliates.my_affiliations');
        }

        $company = Company::where('user', \Auth::user()->id)->first();

        $affiliate = Affiliate::create([
            'user'        => \Auth::user()->id,
            'project'     => $project['id'],
            'percentage' => $project['percentage_affiliates'],
            'company'     => @$company->id
        ]);

        $notification = Notification::where([
            ['notifiable_id',$user['id']],
            ['type','Modules\Notificacoes\Notifications\NovaAfiliacao']
        ])
        ->whereNull('read_at')
        ->first();

        if($notification){
            $data = json_decode($notification['data']);
            $notification->update([
                'data' => json_encode(['qtd' => preg_replace("/[^0-9]/", "", $data->qtd) + 1 ])
            ]);
        }
        else{
            $user->notify(new NovaAfiliacao());
        }

        return redirect()->route('affiliates.minhasafiliacoes');
    }

    public function confirmAffiliation(Request $request) {

        $requestData = $request->all();

        $affiliationRequest = AffiliationRequest::where('id',Hashids::decode($requestData['id']))->first();

        $project = Project::where('id',$affiliationRequest['project'])->first();

        $user = User::find($affiliationRequest['user']);

        $company = Company::where('user', $user['id'])->first();

        $affiliate = Affiliate::create([
            'user'        => $user['id'],
            'project'     => $project['id'],
            'percentage'  => $project['percentage_affiliates'],
            'company'     => @$company->id
        ]);

        $affiliationRequest->update([
            'status' => 'Confirmada'
        ]);

        $notification = Notification::where([
            ['notifiable_id',$user['id']],
            ['type','Modules\Notificacoes\Notifications\AfiliacaoAprovada']
        ])
        ->whereNull('read_at')
        ->first();

        if($notification){
            $data = json_decode($notification['data']);
            $notification->update([
                'data' => json_encode(['qtd' => preg_replace("/[^0-9]/", "", $data->qtd) + 1 ])
            ]);
        }
        else{
            $user->notify(new AfiliacaoAprovada());
        }

        return response()->json('Sucesso');
    } 

    public function delete(Request $request){

        $requestData = $request->all();

        $affiliate = Affiliate::where('id',Hashids::decode($requestData['affiliate']))->first();
        $affiliate->delete();

        return response()->json('sucesso');
    }

    public function myAffiliates(){

        return view('affiliates::my_affiliates');
    }

    public function myAffiliations(){
 
        $myAffiliations = Affiliate::where('user',\Auth::user()->id)->get()->toArray();

        $projects = [];

        if(count($myAffiliations) > 0){
            foreach($myAffiliations as $affiliation){

                $project             = Project::find($affiliation['project']);
                $p['affiliation_id'] = Hashids::encode($affiliation['id']);
                $p['photo']          = $project['photo'];
                $p['name']           = $project['name'];
                $p['description']    = $project['description'];
                $projects[]          = $p;

            }
        }

        return view('affiliates::my_affiliations',[
            'projects' => $projects
        ]);
    }

    public function details($affiliationId){

        $affiliate = Affiliate::where('id',Hashids::decode($affiliationId))->first();
        $idAfiliado = Hashids::encode($affiliate->id);

        $project = Project::find($affiliate['project']);

        $companies = Company::where('user',\Auth::user()->id)->get()->toArray();

        $userProject = UserProject::where([
            ['project',$project['id']],
            ['tipo','producer']
        ])->first();
        $user = User::find($userProject['user']);

        $extraMaterials = ExtraMaterials::where('project',$project['id'])->get()->toArray();

        return view('affiliates::affiliation_details',[
            'affiliate_id'     => $idAfiliado,
            'project'          => $project,
            'producer'         => $user['name'],
            'companies'        => $companies,
            'affiliate'        => $affiliate,
            'extras_materials' => $extraMaterials,
        ]);
    }

    public function myAffiliatesData(){

        $userProjects = UserProject::where([
            ['user',\Auth::user()->id],
            ['type','producer']
        ])->pluck('project')->toArray();

        $myAffiliates = \DB::table('affiliates as affiliate')
            ->leftJoin('users as user','affiliate.user','user.id')
            ->leftJoin('projects as project','affiliate.project','project.id')
            ->whereIn('project.id',$userProjects)
            ->whereNull('affiliate.deleted_at')
            ->select([
                'affiliate.id',
                'affiliate.deleted_at',
                'user.name',
                'affiliate.percentage',
                'project.name',
        ]);

        return Datatables::of($myAffiliates)
        ->addColumn('detalhes', function ($affiliate) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_afiliado' data-placement='top' data-toggle='tooltip' title='Detalhes' afiliado='".Hashids::encode($affiliate->id)."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                            Detalhes
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_remover_afiliado' style='margin-left:10px'>
                        <a class='remover_afiliado btn btn-outline btn-danger' data-placement='top' data-toggle='tooltip' title='Remover afiliado' afiliado='".Hashids::encode($affiliate->id)."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                            Remover afiliado
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function pendingAffiliations(){

        $userProjects = UserProject::where([
            ['user',\Auth::user()->id],
            ['type','producer']   
        ])->pluck('project')->toArray();

        $affiliationsRequests = \DB::table('affiliate_requests as affiliate_request')
            ->leftJoin('users as user','affiliate_request.user','user.id')
            ->leftJoin('projects as project','affiliate_request.project','project.id')
            ->whereIn('affiliate_request.project',$userProjects)
            ->whereNull('affiliate_request.deleted_at')
            ->where('affiliate_request.status','pending')
            ->select([
                'affiliate_request.id',
                'user.name',
                'project.percentage_affiliates',
                'affiliate_request.created_at as data_solicitacao',
                'project.name',
        ]);

        return Datatables::of($affiliationsRequests)
        ->editColumn('data_solicitacao', function($affiliate){
            return Carbon::parse($affiliate->data_solicitacao)->format('d/m/Y H:i');
        })
        ->addColumn('detalhes', function ($affiliationRequest) {
            return "<span>
                        <a class='btn btn-outline btn-success confirmar_afiliacao' data-placement='top' data-toggle='tooltip' title='Confirmar' affiliate_request='".Hashids::encode($affiliationRequest->id)."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                            Confirmar afiliação
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_cancelar_solicitacao'>
                        <a class='cancelar_solicitacao btn btn-outline btn-danger' data-placement='top' data-toggle='tooltip' title='cancelar solicitação' affiliate_request='".Hashids::encode($affiliationRequest->id)."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                            Negar solicitação
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);

    }

    public function myPendingAffiliations(){

        $affiliationsRequests = \DB::table('affiliate_requests as affiliate_request')
            ->leftJoin('projects as project','project.id','=','affiliate_request.project')
            ->whereNull('affiliate_request.deleted_at')
            ->where('affiliate_request.user',\Auth::user()->id)
            ->whereIn('affiliate_request.status',['pending','denied'])
            ->select([
                'affiliate_request.id',
                'affiliate_request.project',
                'affiliate_request.status',
                'project.name',
                'affiliate_request.created_at as data_solicitacao',
        ]);

        return Datatables::of($affiliationsRequests)
        ->editColumn('data_solicitacao', function($affiliationRequest){
            return Carbon::parse($affiliationRequest->data_solicitacao)->format('d/m/Y H:i');
        })
        ->addColumn('detalhes', function ($affiliationRequest) {
            return "<span data-toggle='modal' data-target='#modal_cancelar_solicitacao'>
                        <a class='cancelar_solicitacao btn btn-outline btn-danger' data-placement='top' data-toggle='tooltip' title='cancelar solicitação' affiliate_request='".Hashids::encode($affiliationRequest->id)."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                            Cancelar solicitação
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);

    }

    public function projectAffiliations($projectId){

        $project = Project::where('id',$projectId)->first();

        $affiliates = Affiliate::where('project',$projectId)->get()->toArray();

        foreach($affiliates as &$affiliate){
            $user = User::find($affiliate['user']);
            $affiliate['name'] = $user['name'];
        }

        $view = view('affiliates::afiliados_project',[
            'project'   => $project,
            'afiliados' => $affiliates
        ]);

        return response()->json($view->render());

    }

    public function setAffiliationCompany(Request $request){

        $requestData = $request->all();

        $affiliate = Affiliate::where('id',$requestData['affiliate'])->first();
        $affiliate->update($requestData);

        return response()->json('sucesso');
    }

    public function cancelRequest(Request $request){

        $requestData = $request->all();

        $request = AffiliationRequest::where('id',Hashids::decode($requestData['id_solicitacao']))->first();
        $request->delete();

        return response()->json('sucesso');
    }

    public function denyRequest(Request $request){

        $requestData = $request->all();

        $request = AffiliationRequest::where('id',Hashids::decode($requestData['id_solicitacao']))->first();

        $request->update([
            'status' => 'denied'
        ]);

        return response()->json('sucesso');
    }

}
