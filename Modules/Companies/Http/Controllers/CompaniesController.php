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
        //        dd("foi");
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
        if(Request::input('type') == 2){
            return view('companies::edit_cnpj');
        }
        else{
            return view('companies::edit_cpf');
        }
    }

    /**
     * @param CompanyCreateFormRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getCreateForm(CompanyCreateFormRequest $request)
    {
        try {
            if ($request->get('country') == 'usa') {
                $view = view('companies::create_american_company');
            } else {
                $view = view('companies::create_brazilian_company');
            }

            return response()->json($view->render());
        } catch (Exception $e) {
            Log::warning('Erro ao criar form de cadastro da empresa (CompaniesController - getCreateForm)');
            report($e);

            return response()->json('erro', 400);
        }
    }
}


