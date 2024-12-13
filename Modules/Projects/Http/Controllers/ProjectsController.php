<?php

declare(strict_types=1);

namespace Modules\Projects\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\Factory;

class ProjectsController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view("projects::index");
    }

    /**
     * @return Factory|View
     */
    public function create()
    {
        return view("projects::create");
    }

    /**
     * @return Factory|View
     */
    public function show()
    {
        return view("projects::project");
    }

    /**
     * @return Factory|View
     */
    public function showAffiliate()
    {
        return view("projects::projectaffiliate");
    }
}
