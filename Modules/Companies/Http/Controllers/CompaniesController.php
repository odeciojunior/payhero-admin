<?php

namespace Modules\Companies\Http\Controllers;

use Exception;
use Throwable;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Request;
use Modules\Companies\Http\Requests\CompanyCreateFormRequest;

/**
 * Class CompaniesController
 * @package Modules\Companies\Http\Controllers
 */
class CompaniesController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('companies::index');
    }

    /**
     * @return Factory|View
     */
    public function create()
    {
        return view('companies::create');
    }

    /**
     *
     */
    public function show()
    {

    }

    /**
     * @param $encodedId
     * @return Factory|View
     */
    public function edit($encodedId)
    {

        if(Request::input('type') == 2){ // pessoa jurídica

            return view('companies::edit_cnpj');
        }

        elseif(Request::input('type') == 1){ // pessoa física

            return view('companies::edit_cpf');
        }

        return "";
    }

}


