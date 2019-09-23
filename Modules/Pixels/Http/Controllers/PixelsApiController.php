<?php

namespace Modules\Pixels\Http\Controllers;


use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Pixel;
use Modules\Core\Entities\Project;
use Modules\Pixels\Http\Requests\PixelStoreRequest;
use Modules\Pixels\Http\Requests\PixelUpdateRequest;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Pixels\Transformers\PixelsResource;

class PixelsApiController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('project') && !empty($request->input('project'))) {
                $pixelModel = new \Modules\Core\Entities\Pixel();
                $projectModel = new Project();

                $projectId = current(Hashids::decode($request->input('project')));
                $project = $projectModel->find($projectId);
                if (Gate::allows('edit', [$project])) {
                    $pixels = $pixelModel->where('project_id', $projectId);

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
     * @return JsonResponse
     */
    public function store(PixelStoreRequest $request)
    {
        try {
            $pixelModel = new Pixel();
            $projectModel = new Project();

            $validator = $request->validated();

            if (!$validator) {
                return response()->json('Parametros invalidos', 400);
            }

            $validator['project_id'] = current(Hashids::decode($validator['project_id']));

            $project = $projectModel->find($validator['project_id']);

            if (Gate::allows('edit', [$project])) {
                $pixel = $pixelModel->create($validator);

                if ($pixel) {
                    return response()->json('Pixel Configurado com sucesso!', 200);
                } else {
                    return response()->json('Erro ao criar pixel', 400);
                }
            } else {
                return response()->json(['message' => 'Sem permissão para salvar pixels'], 403);
            }
        } catch (Exception $e) {
            Log::warning('Erro tentar salvar pixel (PixelsController - store)');
            report($e);
            return response()->json('Erro ao criar pixel', 400);
        }
    }

    /**
     * @param PixelUpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(PixelUpdateRequest $request, $id)
    {
        $validated = $request->validated();
        try {
            if (isset($validated) && isset($id)) {
                $pixelModel = new Pixel();

                $pixelId = Hashids::decode($id)[0];
                $pixel = $pixelModel->with(['project'])->find($pixelId);
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
            return response()->json(['message' => 'Pixel nao encontrado'], 404);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar fazer update dos dados do pixel (PixelsController - update)');
            report($e);
            return response()->json(['message' => 'Erro ao tentar atualizar dados!'], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            if (isset($id)) {
                $pixelModel = new Pixel();

                $pixelId = Hashids::decode($id)[0];
                $pixel = $pixelModel->with(['project'])->find($pixelId);
                $project = $pixel->getRelation('project');

                if (Gate::allows('edit', [$project])) {
                    $pixelDeleted = $pixel->delete();
                    if ($pixelDeleted) {
                        return response()->json('sucesso', 200);
                    } else {
                        return response()->json(['message' => 'Erro ao tentar excluir pixel'], 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para remover pixels'], 403);
                }
            }
            return response()->json(['message' => 'Pixel nao encontrado'], 404);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar excluir pixel (PixelsController - destroy)');
            report($e);
            return response()->json(['message' => 'Erro ao tentar excluir pixel'], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        try {
            $pixelModel = new Pixel();

            $data = $request->all();
            if (isset($data['pixelId'])) {
                $pixelId = Hashids::decode($data['pixelId'])[0];
                $pixel = $pixelModel->with(['project'])->find($pixelId);
                $project = $pixel->getRelation('project');

                if (Gate::allows('edit', [$project])) {

                    if ($pixel) {
                        $pixel->makeHidden(['id', 'project_id', 'campaing_id'])->unsetRelation('project');
                        return response()->json($pixel);
                    } else {
                        return response()->json('Erro ao buscar pixel', 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para visualizar pixels'], 403);
                }
            }
            return response()->json('Pixel nao encontrado', 404);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar detalhes do pixel (PixelController - show)');
            report($e);
            return response()->json('Erro ao buscar pixel', 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request)
    {
        try {
            $pixelModel = new Pixel();

            $pixelId = $request->input('pixelId');
            $pixel = $pixelModel->with(['project'])->find(Hashids::decode($pixelId)[0]);
            $project = $pixel->getRelation('project');

            if (Gate::allows('edit', [$project])) {

                if ($pixel) {
                    $pixel->makeHidden(['id', 'project_id', 'campaing_id'])->unsetRelation('project');
                    return response()->json($pixel);
                    //return view("pixels::edit", ['pixel' => $pixel]);
                } else {
                    return response()->json('erro');
                }
            } else {
                return response()->json(['message' => 'Sem permissão para editar pixels'], 403);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar pixel (PixelsController - edit)');
            report($e);
            return response()->json('Erro ao buscar pixel', 400);
        }
    }
}
