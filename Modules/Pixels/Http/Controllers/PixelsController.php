<?php

namespace Modules\Pixels\Http\Controllers;

use App\Entities\Pixel;
use App\Entities\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Pixels\Http\Requests\PixelStoreRequest;
use Modules\Pixels\Http\Requests\PixelUpdateRequest;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class PixelsController extends Controller
{
    private $pixel;
    private $project;

    private function getPixel()
    {
        if (!$this->pixel) {
            $this->pixel = app(Pixel::class);
        }

        return $this->pixel;
    }

    private function getProject()
    {
        if (!$this->project) {
            $this->project = Project::class;
        }

        return $this->project;
    }

    public function index(Request $request)
    {
        try {
            $data = $request->all();

            if (isset($data['projeto'])) {
                $projectId = $data['projeto'];

                $projectId = Hashids::decode($projectId)[0];
                $pixels    = $this->getPixel()->where('project', $projectId)->get();
            } else {
                return response()->json('projeto não encontrado');
            }

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
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar pixels (PixelsController - index)');
            report($e);
        }
    }

    public function store(PixelStoreRequest $request)
    {
        try {
            $validator = $request->validated();

            if (!$validator) {
                return response()->json('erro');
            }

            $validator['project'] = Hashids::decode($validator['project'])[0];

            $pixel = $this->getPixel()->create($validator);
            if ($pixel) {
                return response()->json('Pixel Configurado com sucesso!');
            }

            return response()->json('erro');
        } catch (Exception $e) {
            Log::warning('Erro tentar salvar pixel (PixelsController - store)');
            report($e);
        }
    }

    public function update(Request $request)
    {
        try {
            $data = $request->input('pixelData');
            if (empty($data['name']) || empty($data['code']) || empty($data['platform'])) {
                return response()->json('Erro');
            }
            $pixelId      = Hashids::decode($data['id'])[0];
            $pixel        = $this->getPixel()->find($pixelId);
            $pixelUpdated = $pixel->update($data);
            if ($pixelUpdated) {
                return response()->json('Sucesso');
            }

            return response()->json('Erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar fazer update dos dados do pixel (PixelsController - update)');
            report($e);
        }
    }

    public function destroy($id)
    {
        try {
            if ($id) {
                $pixelId      = Hashids::decode($id)[0];
                $pixelDeleted = $this->getPixel()->find($pixelId)->delete();
                if ($pixelDeleted) {
                    return response()->json('sucesso');
                }

                return response()->json('erro');
            }

            return response()->json('erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar excluir pixel (PixelsController - destroy)');
            report($e);
        }
    }

    public function show(Request $request)
    {
        try {
            $data = $request->all();

            if (isset($data['pixelId'])) {
                $pixelId = Hashids::decode($data['pixelId'])[0];

                $pixel = $this->getPixel()->where('id', $pixelId)->first();

                $modalBody = '';

                $modalBody .= "<div class='col-xl-12 col-lg-12'>";
                $modalBody .= "<table class='table table-bordered table-hover table-striped'>";
                $modalBody .= "<thead>";
                $modalBody .= "</thead>";
                $modalBody .= "<tbody>";
                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Nome:</b></td>";
                $modalBody .= "<td>" . $pixel->name . "</td>";
                $modalBody .= "</tr>";
                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Código:</b></td>";
                $modalBody .= "<td>" . $pixel->code . "</td>";
                $modalBody .= "</tr>";
                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Plataforma:</b></td>";
                $modalBody .= "<td>" . $pixel->platform . "</td>";
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

            return response()->json('Erro ao buscar pixel');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar detalhes do pixel (PixelController - show)');
            report($e);
        }
    }

    public function create()
    {
        try {
            $view = view('pixels::create');

            return response()->json($view->render());
        } catch (Exception $e) {
            Log::error('Erro ao tentar acessar tela de cadastro (PixelsController - create)');
            report($e);
        }
    }

    public function edit(Request $request)
    {
        try {
            $pixelId = $request->input('id');
            $pixel   = $this->getPixel()->find(Hashids::decode($pixelId)[0]);
            if ($pixel) {
                $form = view('pixels::edit', ['pixel' => $pixel]);

                return response()->json($form->render());
            }

            return response()->json('erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar pixel (PixelsController - edit)');
            report($e);
        }
    }
}
