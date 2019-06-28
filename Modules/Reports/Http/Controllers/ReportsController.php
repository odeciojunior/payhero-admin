<?php

namespace Modules\Reports\Http\Controllers;

use App\Entities\Project;
use App\Entities\Sale;
use App\Entities\UserProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReportsController extends Controller
{
    /**
     * @var UserProject
     */
    private $projectsModel;
    /**
     * @var Project
     */
    private $userProjectModel;
    /**
     * @var Sale
     */
    private $salesModel;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    public function getUserProjects()
    {
        if (!$this->userProjectModel) {

            $this->userProjectModel = app(UserProject::class);
        }

        return $this->userProjectModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    public function getProjects()
    {
        if (!$this->projectsModel) {
            $this->projectsModel = app(Project::class);
        }

        return $this->projectsModel;
    }

    public function getSales()
    {
        if (!$this->salesModel) {
            $this->salesModel = app(Sale::class);
        }

        return $this->salesModel;
    }

    public function index(Request $request)
    {

        $user         = auth()->user();
        $userProjects = $this->getUserProjects()->with(['projectId'])->where('user', $user->id)->get();

        $projects = [];
        foreach ($userProjects as $userProject) {
            if (isset($userProject->projectId)) {
                $projects [] = $userProject->projectId;
            }
        }

        $dataSearch = $request->all();
        if (isset($dataSearch['project'])) {

        } else {
            $projectSearch = $projects[0];
            $dataStart     = date('Y-m-d H:m:s', strtotime(Carbon::now()));
            $dataEnd       = date('Y-m-d H:m:s', strtotime(Carbon::now()->subDay(150)));
        }

        $sales = $this->getSales()->where('project', $projectSearch->id)->where([
                                                                                    ['owner', auth()->user()->id],
                                                                                ])
                      ->whereBetween('start_date', [$dataStart, $dataEnd])->get();

        dd($sales, $dataEnd);

        return view('reports::index', compact(['projects']));
    }
    /*public function search(Request $request)
    {
        dd($request->all());
    }*/
}
