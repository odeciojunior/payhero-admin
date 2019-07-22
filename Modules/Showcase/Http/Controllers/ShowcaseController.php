<?php

namespace Modules\Showcase\Http\Controllers;

use App\Entities\Plan;
use App\Entities\User;
use App\Entities\Project;
use App\Entities\Affiliate;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use App\Entities\AffiliateRequest;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;

class ShowcaseController extends Controller {

    public function index() {

        $affiliateRequestModel = new AffiliateRequest();
        $userProjectModel      = new UserProject();
        $affiliateModel        = new Affiliate();
        $projectModel          = new Project();
        $userModel             = new User();
        $planModel             = new Plan();

        $userAffiliations = $affiliateModel->where('user',\Auth::user()->id)->pluck('project')->toArray();

        $availableProjects = $userProjectModel->where([
            ['user','!=',\Auth::user()->id],
            ['type','producer']
        ])->pluck('project')->toArray();

        $pendingAffiliations = $affiliateRequestModel->where([
            ['user', \Auth::user()->id],
            ['status','pending']
        ])->pluck('project')->toArray();

        $projects = $projectModel->select('id','photo','name','description','percentage_affiliates')
                            ->whereIn('id', $availableProjects)
                            ->whereNotIn('id',$userAffiliations)
                            ->whereNotIn('id',$pendingAffiliations)
                            ->where('visibility','public')
                            ->get()->toArray();

        foreach($projects as &$project){

            $userProject = $userProjectModel->where([
                ['project',$project['id']],
                ['type','producer']
            ])->first();

            $user = $userModel->find($userProject['user']);
            $project['producer'] = $user['name'];
            $plan = $planModel->where('project',$project['id'])->max('price');

            $highestCommission = number_format($plan * 0.90, 2);

            $highestCommission = str_replace(',','',$highestCommission);

            $highestCommission = number_format($highestCommission * $project['percentage_affiliates'] / 100 ,2);

            $project['highest_commission'] = str_replace('.',',',$highestCommission);

            $project['id'] = Hashids::encode($project['id']);
        }

        return view('showcase::index',[
            'projects' => $projects
        ]); 
    }

}


