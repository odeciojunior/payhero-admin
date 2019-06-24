<?php

namespace Modules\Pixels\Http\Controllers;

use App\Entities\Pixel;
use App\Entities\Project;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Pixels\Http\Requests\PixelStoreRequest;
use Modules\Pixels\Http\Requests\PixelUpdateRequest;
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

    public function update(PixelUpdateRequest $request, $id)
    {
        $validated = $request->validated();
        try {
            if (isset($validated) && isset($id)) {
                $pixelId      = Hashids::decode($id)[0];
                $pixel        = $this->getPixel()->find($pixelId);
                $pixelUpdated = $pixel->update($validated);
                if ($pixelUpdated) {
                    return response()->json('Sucesso', 200);
                }
            }

            return response()->json(['message' => 'Erro ao tentar atualizar dados!'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar fazer update dos dados do pixel (PixelsController - update)');
            report($e);
        }
    }

    public function destroy($id)
    {
        try {
            if (isset($id)) {
                $pixelId      = Hashids::decode($id)[0];
                $pixelDeleted = $this->getPixel()->find($pixelId)->delete();
                if ($pixelDeleted) {
                    return response()->json('sucesso', 200);
                }

                return response()->json('erro');
            }

            return response()->json('erro', 422);
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
                $pixel   = $this->getPixel()->find($pixelId);
                if ($pixel) {
                    return view("pixels::details", ['pixel' => $pixel]);
                }
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
            $pixelId = $request->input('pixelId');
            $pixel   = $this->getPixel()->find(Hashids::decode($pixelId)[0]);
            if ($pixel) {
                return view("pixels::edit", ['pixel' => $pixel]);
            }

            return response()->json('erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar pixel (PixelsController - edit)');
            report($e);
        }
    }
}
