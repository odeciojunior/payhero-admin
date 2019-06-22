<?php

namespace Modules\Pixels\Http\Controllers;

use App\Entities\Pixel;
use App\Entities\Project;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Pixels\Http\Requests\PixelStoreRequest;
use Modules\Pixels\Transformers\PixelsResource;
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

            if ($request->has('project')) {
                $projectId = current(Hashids::decode($request->input('project')));
                $pixels    = $this->getPixel()->where('project', $projectId)->get();

                return PixelsResource::collection($pixels);
            }
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

            $validator['project'] = current(Hashids::decode($validator['project']));

            $pixel = $this->getPixel()->create($validator);
            dd($pixel);

            if ($pixel) {
                return response()->json('Pixel Configurado com sucesso!', 200);
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
            //            $view = view('pixels::create');

            //            return response()->json($view->render());
            return view('pixels::create');
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
