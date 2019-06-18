<?php

namespace Modules\Plans\Http\Controllers;

use App\Entities\Gift;
use App\Entities\Plan;
use App\Entities\PlanGift;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class PlansController extends Controller
{
    public function index(Request $request)
    {


        $requestData = $request->all();

        $plans = \DB::table('plans as plan')
                    ->whereNull('deleted_at');

        if (isset($requestData['projeto'])) {
            $plans = $plans->where('plan.project', '=', Hashids::decode($requestData['projeto']));
        } else {
            return response()->json('projeto não encontrado');
        }

        $plans = $plans->get([
                                 'plan.id',
                                 'plan.name',
                                 'plan.description',
                                 'plan.code',
                                 'plan.price',
                             ]);

        return Datatables::of($plans)
                         ->addColumn('detalhes', function($plan) {
                             return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_plano' data-placement='top' data-toggle='tooltip' title='Detalhes' plano='" . Hashids::encode($plan->id) . "'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a class='btn btn-outline btn-primary editar_plano' data-placement='top' data-toggle='tooltip' title='Editar' plano='" . Hashids::encode($plan->id) . "'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_plano' data-placement='top' data-toggle='tooltip' title='Excluir' plano='" . Hashids::encode($plan->id) . "'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
                         })
                         ->rawColumns(['detalhes'])
                         ->make(true);
    }

    public function store(Request $request)
    {

        $requestData            = $request->all();
        $requestData['project'] = Hashids::decode($requestData['projeto'])[0];

        $userProjeto = UserProject::where([
                                              ['projeto', $requestData['project']],
                                              ['tipo', 'producer'],
                                          ])->first();

        $requestData['company'] = $userProjeto->company;
        $requestData['price']   = $this->getValue($requestData['price']);

        $planCode = false;

        while ($planCode == false) {

            $code = $this->randString(3) . rand(100, 999);
            $plan = Plan::where('code', $code)->first();
            if ($plan == null) {
                $planCode            = true;
                $requestData['code'] = $code;
            }
        }

        $plan = Plan::create($requestData);

        $photo = $request->file('foto_plano_cadastrar');

        if ($photo != null) {
            $photoName = 'plano_' . $plan->id . '_.' . $photo->getClientOriginalExtension();

            Storage::delete('public/upload/plano/' . $photoName);

            $photo->move(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO, $photoName);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $photoName);

            $img->crop($requestData['foto_plano_cadastrar_w'], $requestData['foto_plano_cadastrar_h'], $requestData['foto_plano_cadastrar_x1'], $requestData['foto_plano_cadastrar_y1']);

            $img->resize(200, 200);

            Storage::delete('public/upload/plano/' . $photoName);

            $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $photoName);

            $plan->update([
                              'foto' => $photoName,
                          ]);
        }

        $qtdProduto = 1;

        while (isset($requestData['produto_' . $qtdProduto]) && $requestData['produto_' . $qtdProduto] != '') {

            ProductPlan::create([
                                    'product'        => $requestData['produto_' . $qtdProduto],
                                    'plan'           => $plan->id,
                                    'product_amount' => $requestData['produto_qtd_' . $qtdProduto++],
                                ]);
        }

        $giftIndex = 1;

        while (isset($requestData['brinde_' . $giftIndex]) && $requestData['brinde_' . $giftIndex] != '') {

            PlanGift::create([
                                 'brinde' => $requestData['brinde_' . $giftIndex++],
                                 'plano'  => $plan->id,
                             ]);
        }

        return response()->json('sucesso');
    }

    public function update(Request $request)
    {

        $requestData = $request->all();

        unset($requestData['projeto']);

        $requestData['price'] = $this->getValue($requestData['price']);

        $plan = Plan::where('id', Hashids::decode($requestData['id']))->first();

        $plan->update($requestData);

        $photo = $request->file('foto_plano_editar');

        if ($photo != null) {
            $photoName = 'plano_' . $plan['id'] . '_.' . $photo->getClientOriginalExtension();

            Storage::delete('public/upload/plano/' . $photoName);

            $photo->move(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO, $photoName);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $photoName);

            $img->crop($requestData['foto_plano_editar_w'], $requestData['foto_plano_editar_h'], $requestData['foto_plano_editar_x1'], $requestData['foto_plano_editar_y1']);

            $img->resize(200, 200);

            Storage::delete('public/upload/plano/' . $photoName);

            $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $photoName);

            $plan->update([
                              'foto' => $photoName,
                          ]);
        }

        $produtosPlanos = ProductPlan::where('plano', $plan['id'])->get()->toArray();
        if (count($produtosPlanos) > 0) {
            foreach ($produtosPlanos as $produto_plano) {
                ProductPlan::find($produto_plano['id'])->delete();
            }
        }

        $plansBrindes = PlanGift::where('plano', $plan['id'])->get()->toArray();
        if (count($plansBrindes) > 0) {
            foreach ($plansBrindes as $plan_brinde) {
                PlanGift::find($plan_brinde['id'])->delete();
            }
        }

        $qtdProduto = 1;
        while (isset($requestData['produto_' . $qtdProduto]) && $requestData['produto_' . $qtdProduto] != '') {

            ProductPlan::create([
                                    'product'        => $requestData['produto_' . $qtdProduto],
                                    'plan'           => $plan->id,
                                    'product_amount' => $requestData['produto_qtd_' . $qtdProduto++],
                                ]);
        }

        $qtdBrinde = 1;

        while (isset($requestData['brinde_' . $qtdBrinde]) && $requestData['brinde_' . $qtdBrinde] != '') {

            PlanGift::create([
                                 'gift' => $requestData['brinde_' . $qtdBrinde++],
                                 'plan' => $plan->id,
                             ]);
        }

        return response()->json('sucesso');
    }

    public function delete(Request $request)
    {

        $requestData = $request->all();

        $servico_sms = ZenviaSms::where('plano', $requestData['id'])->first();

        if ($servico_sms != null) {
            return response()->json('Impossível excluir, possui serviço de sms integrado.');
        }

        $plan = Plan::where('id', Hashids::decode($requestData['id']))->first();

        $produtosPlanos = ProductPlan::where('plano', $plan['id'])->get()->toArray();
        if (count($produtosPlanos) > 0) {
            foreach ($produtosPlanos as $produto_plano) {
                ProductPlan::find($produto_plano['id'])->delete();
            }
        }

        $plansBrindes = PlanGift::where('plano', $plan['id'])->get()->toArray();
        if (count($plansBrindes) > 0) {
            foreach ($plansBrindes as $plan_brinde) {
                PlanGift::find($plan_brinde['id'])->delete();
            }
        }

        $plan->delete();

        return response()->json('sucesso');
    }

    public function details(Request $request)
    {

        $requestData = $request->all();

        $plan = Plan::where('id', Hashids::decode($requestData['id_plano']))->first();

        $modalBody = '';

        $modalBody .= "<div class='col-xl-12 col-lg-12'>";
        $modalBody .= "<table class='table table-bordered table-hover table-striped'>";
        $modalBody .= "<thead>";
        $modalBody .= "</thead>";
        $modalBody .= "<tbody>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Nome:</b></td>";
        $modalBody .= "<td>" . $plan->name . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Descrição:</b></td>";
        $modalBody .= "<td>" . $plan->description . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Código identificador:</b></td>";
        $modalBody .= "<td>" . $plan->code . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Status:</b></td>";
        if ($plan->status == 1)
            $modalBody .= "<td>Ativo</td>";
        else
            $modalBody .= "<td>Inativo</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Preço:</b></td>";
        $modalBody .= "<td>" . $plan->price . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Possui frete:</b></td>";
        if ($plan->shipment == 1)
            $modalBody .= "<td>Sim</td>";
        else
            $modalBody .= "<td>Não</td>";
        $modalBody .= "</tr>";

        $produtosPlano = ProductPlan::where('plano', $plan->id)->get()->toArray();

        if (count($produtosPlano) > 0) {

            $modalBody .= "<tr class='text-center'>";
            $modalBody .= "<td colspan='2'><b>Produtos do plano:</b></td>";
            $modalBody .= "</tr>";

            foreach ($produtosPlano as $produtoPlano) {

                $produto   = Produto::find($produtoPlano['produto']);
                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Produto:</b></td>";
                $modalBody .= "<td>" . $produto->name . "</td>";
                $modalBody .= "</tr>";
                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Quantidade:</b></td>";
                $modalBody .= "<td>" . $produtoPlano['product_amount'] . "</td>";
                $modalBody .= "</tr>";
            }
        }

        $planBrindes = PlanGift::where('plano', $plan->id)->get()->toArray();

        if (count($planBrindes) > 0) {

            $modalBody .= "<tr class='text-center'>";
            $modalBody .= "<td colspan='2'><b>Brindes do plano:</b></td>";
            $modalBody .= "</tr>";

            foreach ($planBrindes as $planBrinde) {

                $brinde = Gift::find($planBrinde['brinde']);

                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Brinde:</b></td>";
                $modalBody .= "<td>" . $brinde->descricao . "</td>";
                $modalBody .= "</tr>";
            }
        }

        $modalBody .= "</thead>";
        $modalBody .= "</table>";
        $modalBody .= "<div class='text-center'>";
        if (!$plan->shopify_id) {
            $modalBody .= "<img src='" . url(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $plan->foto) . "?dummy=" . uniqid() . "' style='height: 250px'>";
        } else {
            $modalBody .= "<img src='" . $plan->foto . "' style='height: 250px'>";
        }
        $modalBody .= "</div>";
        $modalBody .= "</div>";

        return response()->json($modalBody);
    }

    function randString($size)
    {

        $basic = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $return = "";

        for ($count = 0; $size > $count; $count++) {

            $return .= $basic[rand(0, strlen($basic) - 1)];
        }

        return $return;
    }

    function getValue($str)
    {

        if ($str == '') {
            return '0.00';
        }

        if (strstr($str, ",")) {
            $str = str_replace(".", "", $str);
            $str = str_replace(",", ".", $str);
        }

        $arrayValor = explode('.', $str);

        if (count($arrayValor) == 1) {
            $str = $str . '.00';
        } else {
            if (strlen($arrayValor['1']) == 1) {
                $str .= '0';
            }
        }

        return $str;
    }

    public function create(Request $request)
    {

        $requestData = $request->all();

        $transportadoras = Transportadora::all();

        $produtos = Produto::where('user', \Auth::user()->id)->get()->toArray();

        $brindes = Gift::where('projeto', $requestData['projeto'])->get()->toArray();

        $requestData_hotzapp = DadosHotZapp::all();

        $form = view('planos::create', [
            'transportadoras' => $transportadoras,
            'produtos'        => $produtos,
            'brindes'         => $brindes,
            'dados_hotzapp'   => $requestData_hotzapp,
        ]);

        return response()->json($form->render());
    }

    public function edit(Request $request)
    {

        $requestData = $request->all();

        $plan = Plan::where('id', Hashids::decode($requestData['id']))->first();

        $idPlano = Hashids::encode($plan->id);

        $transportadoras = Transportadora::all();

        $produtos = Produto::where('user', \Auth::user()->id)->get()->toArray();

        $brindes = Gift::where('projeto', $requestData['projeto'])->get()->toArray();

        $requestData_hotzapp = DadosHotZapp::all();

        $produtosPlanos = ProductPlan::where('plano', $plan['id'])->get()->toArray();

        $planBrindes = PlanGift::where('plano', $plan['id'])->get()->toArray();

        $caminho_foto = url(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $plan['foto'] . "?dummy=" . uniqid());

        $form = view('planos::edit', [
            'id_plano'        => $idPlano,
            'plano'           => $plan,
            'transportadoras' => $transportadoras,
            'foto'            => $caminho_foto,
            'produtos_planos' => $produtosPlanos,
            'produtos'        => $produtos,
            'brindes'         => $brindes,
            'planoBrindes'    => $planBrindes,
        ]);

        return response()->json($form->render());
    }
}
