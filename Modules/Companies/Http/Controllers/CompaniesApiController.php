<?php

namespace Modules\Companies\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;
use Modules\Companies\Transformers\CompanyResource;

/**
 * Class CompaniesController
 * @package Modules\Companies\Http\Controllers
 */
class CompaniesApiController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {

        return view('companies::create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|CompanyResource
     */
    public function store(Request $request)
    {

    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Throwable
     */
    public function getCreateForm(Request $request)
    {

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {

    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {

    }
}


