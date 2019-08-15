<?php

namespace Modules\Pixels\Http\Controllers;

use App\Entities\Pixel;
use App\Entities\Project;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Pixels\Http\Requests\PixelStoreRequest;
use Modules\Pixels\Http\Requests\PixelUpdateRequest;
use Modules\Pixels\Transformers\PixelsResource;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;
use Exception;

/**
 * Class PixelsController
 * @package Modules\Pixels\Http\Controllers
 */
class PixelsController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->has('project') && !empty($request->input('project'))) {
                $pixelModel   = new Pixel();
                $projectModel = new Project();

                $projectId = current(Hashids::decode($request->input('project')));
                $project   = $projectModel->find($projectId);

                if (Gate::allows('edit', [$project])) {
                    $pixels = $pixelModel->where('project', $projectId);

                    return PixelsResource::collection($pixels->orderBy('id', 'DESC')->paginate(5));
                } else {
                    return response()->json([
                                                'message' => 'Sem permissão para listar pixels',
                                            ], 403);
                }
            } else {
                return response()->json([
                                            'message' => 'Erro ao listar dados de pixels',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar pixels (PixelsController - index)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao listar dados de pixels',
                                    ], 400);
        }
    }

    /**
     * @param PixelStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PixelStoreRequest $request)
    {
        try {
            $pixelModel   = new Pixel();
            $projectModel = new Project();

            $validator = $request->validated();

            if (!$validator) {
                return response()->json('erro');
            }

            $validator['project'] = current(Hashids::decode($validator['project']));

            $project = $projectModel->find($validator['project']);

            if (Gate::allows('edit', [$project])) {
                $pixel = $pixelModel->create($validator);

                if ($pixel) {
                    return response()->json('Pixel Configurado com sucesso!', 200);
                } else {
                    return response()->json('erro');
                }
            } else {
                return response()->json(['message' => 'Sem permissão para salvar pixels'], 403);
            }
        } catch (Exception $e) {
            Log::warning('Erro tentar salvar pixel (PixelsController - store)');
            report($e);
        }
    }

    /**
     * @param PixelUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PixelUpdateRequest $request, $id)
    {
        $validated = $request->validated();
        try {
            if (isset($validated) && isset($id)) {
                $pixelModel = new Pixel();

                $pixelId = Hashids::decode($id)[0];
                $pixel   = $pixelModel->with(['project'])->find($pixelId);
                $project = $pixel->getRelation('project');

                if (Gate::allows('edit', [$project])) {

                    $pixelUpdated = $pixel->update($validated);
                    if ($pixelUpdated) {
                        return response()->json('Sucesso', 200);
                    } else {
                        return response()->json(['message' => 'Erro ao tentar atualizar dados!'], 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para atualizar pixels'], 403);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar fazer update dos dados do pixel (PixelsController - update)');
            report($e);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            if (isset($id)) {
                $pixelModel = new Pixel();

                $pixelId = Hashids::decode($id)[0];
                $pixel   = $pixelModel->with(['project'])->find($pixelId);
                $project = $pixel->getRelation('project');

                if (Gate::allows('edit', [$project])) {
                    $pixelDeleted = $pixel->delete();
                    if ($pixelDeleted) {
                        return response()->json('sucesso', 200);
                    } else {
                        return response()->json('erro');
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para remover pixels'], 403);
                }
            }
        } catch
        (Exception $e) {
            Log::warning('Erro ao tentar excluir pixel (PixelsController - destroy)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        try {
            $pixelModel = new Pixel();

            $data = $request->all();
            if (isset($data['pixelId'])) {
                $pixelId = Hashids::decode($data['pixelId'])[0];
                $pixel   = $pixelModel->with(['project'])->find($pixelId);
                $project = $pixel->getRelation('project');

                if (Gate::allows('edit', [$project])) {

                    if ($pixel) {
                        return view("pixels::details", ['pixel' => $pixel]);
                    } else {
                        return response()->json('Erro ao buscar pixel');
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para visualizar pixels'], 403);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar detalhes do pixel (PixelController - show)');
            report($e);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        try {
            return view('pixels::create');
        } catch (Exception $e) {
            Log::error('Erro ao tentar acessar tela de cadastro (PixelsController - create)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        try {
            $pixelModel = new Pixel();

            $pixelId = $request->input('pixelId');
            $pixel   = $pixelModel->with(['project'])->find(Hashids::decode($pixelId)[0]);
            $project = $pixel->getRelation('project');

            if (Gate::allows('edit', [$project])) {

                if ($pixel) {
                    return view("pixels::edit", ['pixel' => $pixel]);
                } else {
                    return response()->json('erro');
                }
            } else {
                return response()->json(['message' => 'Sem permissão para editar pixels'], 403);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar pixel (PixelsController - edit)');
            report($e);
        }
    }
}
