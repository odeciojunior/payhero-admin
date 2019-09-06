<?php

namespace Modules\Companies\Http\Controllers;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Modules\Companies\Transformers\CompanyResource;

/**
 * Class CompaniesController
 * @package Modules\Companies\Http\Controllers
 */
class CompaniesApiController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $companyModel = new Company();

        $companies = $companyModel->with('user')
                                  ->where('user_id', auth()->user()->id)
                                  ->paginate(15);
        return CompanyResource::collection($companies);
    }

    /**
     * @return Factory|View
     */
    public function create()
    {

        return view('companies::create');
    }

    /**
     * @param Request $request
     * @return JsonResponse|CompanyResource
     */
    public function store(Request $request)
    {

    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Throwable
     */
    public function getCreateForm(Request $request)
    {

    }

    /**
     * @param $id
     * @return Factory|View
     */
    public function edit($id)
    {

    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request)
    {

    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function delete($id)
    {

    }
}


