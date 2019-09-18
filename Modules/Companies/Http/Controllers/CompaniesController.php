<?php

namespace Modules\Companies\Http\Controllers;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Modules\Companies\Http\Requests\CompanyCreateFormRequest;
use Throwable;

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
     * @param $encodedId
     * @return Factory|View
     */
    public function edit($encodedId)
    {
        return view('companies::edit', compact('encodedId'));
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


