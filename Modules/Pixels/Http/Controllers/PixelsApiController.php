<?php

namespace Modules\Pixels\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Pixel;
use Modules\Core\Entities\Project;
use Modules\Pixels\Http\Requests\PixelStoreRequest;
use Modules\Pixels\Http\Requests\PixelUpdateRequest;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Pixels\Transformers\PixelsResource;

/**
 * Class PixelsApiController
 * @package Modules\Pixels\Http\Controllers
 */
class PixelsApiController extends Controller
{
    /**
     * @param $projectId
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index($projectId)
    {
        try {
            if (!empty($projectId)) {
                $pixelModel   = new Pixel();
                $projectModel = new Project();

                activity()->on($pixelModel)->tap(function(Activity $activity) use ($projectId) {
                    $activity->log_name = 'visualization';
                })->log('Visualizou tela todos os pixels para o projeto: ' . $projectId);

                $project = $projectModel->find(current(Hashids::decode($projectId)));
                if (Gate::allows('edit', [$project])) {
                    $pixels = $pixelModel->where('project_id', $project->id);

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
     * @param $projectId
     * @return JsonResponse
     */
    public function store(PixelStoreRequest $request, $projectId)
    {
        try {
            $pixelModel   = new Pixel();
            $projectModel = new Project();

            $validator = $request->validated();

            if (!$validator || !isset($projectId)) {
                return response()->json('Parametros invalidos', 400);
            }

            $validator['project_id'] = current(Hashids::decode($projectId));

            $project = $projectModel->find($validator['project_id']);

            if (Gate::allows('edit', [$project])) {

                if ($validator['platform'] != 'outbrain' || $validator['platform'] != 'taboola') {
                    $order             = ['UA-', 'AW-'];
                    $validator['code'] = str_replace($order, '', $validator['code']);
                }

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
     * @param $projectId
     * @param $id
     * @return JsonResponse
     */
    public function update(PixelUpdateRequest $request, $projectId, $id)
    {
        $validated = $request->validated();
        try {
            if (isset($validated) && isset($id) && isset($projectId)) {
                $pixelModel   = new Pixel();
                $projectModel = new Project();

                $pixel   = $pixelModel->find(current(Hashids::decode($id)));
                $project = $projectModel->find(current(Hashids::decode($projectId)));

                if (Gate::allows('edit', [$project])) {

                    if ($validated['platform'] != 'outbrain' || $validated['platform'] != 'taboola') {
                        $order             = ['UA-', 'AW-', 'AG-'];
                        $validated['code'] = str_replace($order, '', $validated['code']);
                    }

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

            return response()->json(['message' => 'Pixel nao encontrado'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar fazer update dos dados do pixel (PixelsController - update)');
            report($e);

            return response()->json(['message' => 'Erro ao tentar atualizar dados!'], 400);
        }
    }

    /**
     * @param $projectId
     * @param $id
     * @return JsonResponse
     */
    public function destroy($projectId, $id)
    {
        try {
            if (isset($id) && isset($projectId)) {
                $pixelModel   = new Pixel();
                $projectModel = new Project();

                $pixel     = $pixelModel->find(current(Hashids::decode($id)));
                $projectId = current(Hashids::decode($projectId));
                $project   = $projectModel->find($projectId);

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

            return response()->json(['message' => 'Pixel nao encontrado'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar excluir pixel (PixelsController - destroy)');
            report($e);

            return response()->json(['message' => 'Erro ao tentar excluir pixel'], 400);
        }
    }

    /**
     * @param $projectId
     * @param $id
     * @return JsonResponse
     */
    public function show($projectId, $id)
    {
        try {
            if (isset($id) && isset($projectId)) {
                $pixelModel   = new Pixel();
                $projectModel = new Project();

                activity()->on($pixelModel)->tap(function(Activity $activity) {
                    $activity->log_name = 'visualization';
                })->log('Visualizou tela detalhes do pixel: ' . $id);

                $pixel   = $pixelModel->find(current(Hashids::decode($id)));
                $project = $projectModel->find(current(Hashids::decode($projectId)));

                if (Gate::allows('edit', [$project])) {

                    if ($pixel) {
                        $pixel->makeHidden(['id', 'project_id', 'campaing_id']);

                        return response()->json($pixel);
                    } else {
                        return response()->json('Erro ao buscar pixel', 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para visualizar pixels'], 403);
                }
            }

            return response()->json('Pixel nao encontrado', 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar detalhes do pixel (PixelController - show)');
            report($e);

            return response()->json('Erro ao buscar pixel', 400);
        }
    }

    /**
     * @param $projectId
     * @param $id
     * @return JsonResponse
     */
    public function edit($projectId, $id)
    {
        try {
            if (isset($projectId) && isset($id)) {
                $pixelModel   = new Pixel();
                $projectModel = new Project();

                activity()->on($pixelModel)->tap(function(Activity $activity) use ($id) {
                    $activity->log_name   = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                })->log('Visualizou tela editar pixel: ' . $id);

                $pixel   = $pixelModel->find(current(Hashids::decode($id)));
                $project = $projectModel->find(current(Hashids::decode($projectId)));

                if (Gate::allows('edit', [$project])) {

                    if ($pixel) {
                        $pixel->makeHidden(['id', 'project_id', 'campaing_id']);

                        return response()->json($pixel);
                    } else {
                        return response()->json('Erro ao buscar pixel', 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para editar pixels'], 403);
                }
            }

            return response()->json('Erro ao buscar pixel', 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar pixel (PixelsController - edit)');
            report($e);

            return response()->json('Erro ao buscar pixel', 400);
        }
    }
}
