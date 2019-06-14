<?php

namespace Modules\Pixels\Http\Controllers;

use App\Entities\Pixel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class PixelsController extends Controller
{
    public function index(Request $request)
    {

        $dados = $request->all();

        $pixels = \DB::table('pixels as pixel');

        if (isset($dados['projeto'])) {
            $pixels = $pixels->where('pixel.project', '=', Hashids::decode($dados['projeto']));
        } else {
            return response()->json('projeto não encontrado');
        }

        $pixels = $pixels->get([
                                   'pixel.id',
                                   'pixel.name',
                                   'pixel.code',
                                   'pixel.platform',
                                   'pixel.status',
                               ]);

        return Datatables::of($pixels)
                         ->addColumn('detalhes', function($pixel) {
                             return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_pixel' data-placement='top' data-toggle='tooltip' title='Detalhes' pixel='" . Hashids::encode($pixel->id) . "'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a class='btn btn-outline btn-primary editar_pixel' data-placement='top' data-toggle='tooltip' title='Editar' pixel='" . Hashids::encode($pixel->id) . "'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_pixel' data-placement='top' data-toggle='tooltip' title='Excluir' pixel='" . Hashids::encode($pixel->id) . "'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
                         })
                         ->rawColumns(['detalhes'])
                         ->make(true);
    }

    public function store(Request $request)
    {

        $dados = $request->all();

        $dados['project'] = Hashids::decode($dados['projeto'])[0];

        Pixel::create($dados);

        return response()->json('Sucesso');
    }

    public function update(Request $request)
    {

        $dados = $request->all();

        $pixel = Pixel::find(Hashids::decode($dados['pixelData']['id']))->first();

        $pixel->update($dados['pixelData']);

        return response()->json('Sucesso');
    }

    public function delete($id)
    {

        $pixel = Pixel::where('id', Hashids::decode($id))->first();

        $pixel->delete();

        return response()->json('sucesso');
    }

    public function details(Request $request)
    {

        $dados = $request->all();

        $pixel = Pixel::where('id', Hashids::decode($dados['id_pixel']))->first();

        $modalBody = '';

        $modalBody .= "<div class='col-xl-12 col-lg-12'>";
        $modalBody .= "<table class='table table-bordered table-hover table-striped'>";
        $modalBody .= "<thead>";
        $modalBody .= "</thead>";
        $modalBody .= "<tbody>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Nome:</b></td>";
        $modalBody .= "<td>" . $pixel->nome . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Código:</b></td>";
        $modalBody .= "<td>" . $pixel->cod_pixel . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Plataforma:</b></td>";
        $modalBody .= "<td>" . $pixel->plataforma . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Status:</b></td>";
        if ($pixel->status)
            $modalBody .= "<td>Ativo</td>";
        else
            $modalBody .= "<td>Inativo</td>";
        $modalBody .= "</tr>";
        $modalBody .= "</thead>";
        $modalBody .= "</table>";
        $modalBody .= "</div>";
        $modalBody .= "</div>";

        return response()->json($modalBody);
    }

    public function create()
    {
        try {
            dd('aldso');
            $view = view('pixels::create');

            return response()->json($view->render());
        } catch (Exception $e) {
            Log::error('Erro ao tentar acessar tela de cadastro (PixelsController - create)');
            report($e);
        }
    }

    public function edit(Request $request)
    {

        $dados = $request->all();

        $pixel = Pixel::where('id', Hashids::decode($dados['id']))->first();

        $form = view('pixels::edit', [
            'pixel' => $pixel,
        ]);

        return response()->json($form->render());
    }
}
