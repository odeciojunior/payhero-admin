<?php

namespace Modules\Mobile\Http\Controllers\Apis\v10;

use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Project;
use Modules\Core\Services\ProjectService;

/**
 * Class ProjectApiService
 * @package Modules\Mobile\Http\Controllers\Apis\v10
 */
class ProjectApiService
{

    /**
     * ProjectApiService constructor.
     */
    public function __construct()
    { }


    public function getProjects()
    {
        try {

            $projectService = new ProjectService();
            $projectModel   = new Project();

            $projectStatus = [
                $projectModel->present()->getStatus('active'),
            ];

            $projects = $projectService->getUserProjects(true, $projectStatus);

            return response()->json(compact('projects'), 200);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados empresas (ProjectsApiController - getProjects)');
            report($e);

            return response()->json([
                'message' => 'Ocorreu um erro, ao buscar dados das empresas',
            ], 400);
        }
    }

}
