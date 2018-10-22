<?php

namespace Modules\Pixels\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Pixel;
use Yajra\DataTables\Facades\DataTables;

class PixelsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index() {

        return view('pixels::index'); 
    }

    /**
     * Display a form to store new users.
     * @return Response
     */
    public function cadastro() {

        return view('pixels::cadastro');
    }

    public function cadastrarPixel(Request $request){

        $dados = $request->all();

        Pixel::create($dados);

        return redirect()->route('pixels');
    }

    public function editarPixel($id){

        $pixel = Pixel::find($id);

        return view('pixels::editar',[
            'pixel' => $pixel,
        ]);

    }

    public function updatePixel(Request $request){

        $dados = $request->all();

        Pixel::find($dados['id'])->update($dados);

        return redirect()->route('pixels');
    }

    public function deletarPixel($id){

        Pixel::find($id)->delete();

        return redirect()->route('pixels');

    }

    /**
     * Return data for datatable
     */
    public function dadosPixels() {

        $pixels = \DB::table('pixels as pixel')
            ->get([
                'pixel.id',
                'pixel.nome',
                'pixel.cod_pixel',
                'pixel.plataforma',
                'pixel.status',
        ]);

        return Datatables::of($pixels)
        ->addColumn('detalhes', function ($pixel) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_pixel' data-placement='top' data-toggle='tooltip' title='Detalhes' pixel='".$pixel->id."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/pixels/editar/$pixel->id' class='btn btn-outline btn-primary editar_pixel' data-placement='top' data-toggle='tooltip' title='Editar' pixel='".$pixel->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_pixel' data-placement='top' data-toggle='tooltip' title='Excluir' pixel='".$pixel->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }


    public function getDetalhesPixel(Request $request){

        $dados = $request->all();

        $pixel = Pixel::find($dados['id_pixel']);

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Nome:</b></td>";
        $modal_body .= "<td>".$pixel->nome."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Código:</b></td>";
        $modal_body .= "<td>".$pixel->cod_pixel."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Plataforma:</b></td>";
        $modal_body .= "<td>".$pixel->plataforma."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Status:</b></td>";
        if($pixel->status)
            $modal_body .= "<td>Ativo</td>";
        else
            $modal_body .= "<td>Inativo</td>";
        $modal_body .= "</tr>";
        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $modal_body .= "</div>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
    }
}
