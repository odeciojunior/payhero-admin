<?php

namespace Modules\Finances\Http\Controllers;

use App\Entities\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use PagarMe\Client;

class WithdrawalController extends Controller
{
    /**
     * @var Company
     */
    private $company;

    /**
     * WithdrawalController constructor.
     * @param Company $company
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (getenv('PAGAR_ME_PRODUCAO') == 'true') {
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        } else {
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $userCompanies = $this->company->where('user', auth()->user()->id)->get()->toArray();

        $selectedCompany = false;
        $companies       = [];

        foreach ($userCompanies as $company) {

            $companies[] = [
                'id'   => $company['id'],
                'name' => $company['fantasy_name'],
            ];

            if (!$selectedCompany) {
                $company_ativa   = $company['id'];
                $selectedCompany = true;
            }
        }

        return view('finances::index', [
            'company'   => $selectedCompany,
            'companies' => $companies,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('finances::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('finances::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('finances::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
