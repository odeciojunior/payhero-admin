<?php

namespace Modules\Companies\Http\Controllers;

use Exception;
use App\Entities\Company;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Modules\Companies\Transformers\CompanyResource;

/**
 * Class CompaniesController
 * @package Modules\Companies\Http\Controllers
 */
class CompaniesApiController extends Controller
{
    /**
     * @var Company
     */
    private $companyModel;

    /**
     * @return Company|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getCompanyModel()
    {
        if (!$this->companyModel) {
            $this->companyModel = app(Company::class);
        }

        return $this->companyModel;
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $companies = $this->getCompanyModel()
                          ->with('user')
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

    /**
     * @return mixed
     * @throws Exception
     */
    public function getCompaniesData()
    {

        $companies = DB::table('companies as company')
                       ->select([
                                    'company.id',
                                    'company.cnpj',
                                    'company.fantasy_name',
                                ]);

        if (!auth()->user()->hasRole('administrador geral')) {
            $companies = $companies->where('user', auth()->user()->id);
        }

        return Datatables::of($companies)
                         ->editColumn('cnpj', function($company) {
                             if (strlen($company->cnpj) == '14')
                                 return vsprintf("%s%s.%s%s%s.%s%s%s/%s%s%s%s-%s%s", str_split($company->cnpj));
                             else if (strlen($company->cnpj) == '11')
                                 return vsprintf("%s%s%s.%s%s%s.%s%s%s-%s%s", str_split($company->cnpj));

                             return $company->cnpj;
                         })
                         ->addColumn('detalhes', function($company) {
                             return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_empresa' data-placement='top' data-toggle='tooltip' title='Detalhes' empresa='" . $company->id . "'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/empresas/editar/$company->id' class='btn btn-outline btn-primary editar_empresa' data-placement='top' data-toggle='tooltip' title='Editar' empresa='" . $company->id . "'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_empresa' data-placement='top' data-toggle='tooltip' title='Excluir' empresa='" . $company->id . "'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
                         })
                         ->rawColumns(['detalhes'])
                         ->make(true);
    }

}


