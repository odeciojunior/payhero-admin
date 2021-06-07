<?php

namespace Modules\Projects\Http\Controllers;

use Throwable;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\Factory;
use Modules\Core\Services\ProjectService;
use Modules\Projects\Transformers\ProjectsSelectResource;

class ProjectsController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('projects::index');
    }

    /**
     * @return Factory|View
     */
    public function create()
    {
        return view('projects::create');
    }

    /**
     * @return Factory|View
     */
    public function show()
    {
        return view('projects::project');
    }

    /**
     * @return Factory|View
     */
    public function showAffiliate()
    {
        return view('projects::projectaffiliate');
    }

}
