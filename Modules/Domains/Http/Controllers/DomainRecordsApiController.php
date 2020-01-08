<?php

namespace Modules\Domains\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\DomainRecord;
use Modules\Core\Services\CloudFlareService;
use Modules\Domains\Http\Requests\DomainRecordsRequest;
use Modules\Domains\Transformers\DomainRecordsIndexResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class DomainRecordsApiController
 * @package Modules\Domains\Http\Controllers
 */
class DomainRecordsApiController extends Controller
{
    /**
     * @param $projectId
     * @param $domainId
     * @return JsonResponse|DomainRecordsIndexResource
     */
    public function index($projectId, $domainId)
    {
        try {
            $domainModel = new Domain();

            activity()->on($domainModel)->tap(function(Activity $activity) use ($domainId) {
                $activity->log_name   = 'visualization';
                $activity->subject_id = current(Hashids::decode($domainId));
            })->log('Visualizou tela de registros entradas DNS para dominio: ' . $domainId);

            $domainId = current(Hashids::decode($domainId));

            if ($domainId) {
                //hash ok

                $domainProject = $domainModel->with(['project'])->find($domainId);

                $domain = $domainModel->with([
                                                 'domainsRecords' => function($query) use ($domainProject) {
                                                     $query->orWhere(function($queryWhere) use ($domainProject) {
                                                         $queryWhere->where('type', 'A');
                                                         $queryWhere->where('name', $domainProject->name);
                                                     })->orderBy('id', 'desc');
                                                 },
                                             ])->find($domainId);

                if (Gate::allows('edit', [$domainProject->project])) {
                    //se tem permissao para editar o projeto, pode editar um dominio ligado a ele

                    if ($domain) {
                        return DomainRecordsIndexResource::make([
                                                                    'domain'        => $domain,
                                                                    'domainRecords' => $domain->domainsRecords,

                                                                ]);
                    } else {
                        return response()->json(['message' => 'Domínio não encontrado'], 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para editar este domínio'], 400);
                }
            } else {
                //hash nao ok
                return response()->json(['message' => 'Domínio não encontrado'], 400);
            }
        } catch (Exception $e) {
            Log::warning("Erro ao buscar dados records (DomainRecordsApiController - index - project: " . $projectId . ' domain: ' . $domainId . ')');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                    ], 400);
        }
    }

    /**
     * @param DomainRecordsRequest $request
     * @return JsonResponse
     */
    public function store(DomainRecordsRequest $request)
    {
        try {
            $domainModel       = new Domain();
            $domainRecordModel = new DomainRecord();
            $cloudFlareService = new CloudFlareService();

            DB::beginTransaction();
            $requestData = $request->validated();

            $domain = $domainModel->with(['domainsRecords', 'project'])
                                  ->find(current(Hashids::decode($requestData['domain'])));

            if (!empty($domain)) {
                $cloudFlareService->setZone($domain->name);

                if ($requestData['name-register'] == '' || $requestData['name-register'] == '@') {
                    $subdomain = $domain->name;
                } else {
                    $subdomain = $requestData['name-register'];
                }

                $subdomain = str_replace("http://", "", $subdomain);
                $subdomain = str_replace("https://", "", $subdomain);
                $subdomain = "http://" . $subdomain;
                $subdomain = parse_url($subdomain, PHP_URL_HOST);

                if ((strpos($subdomain, '.') === false) || ($subdomain == $domain->name) || (strpos($domain->name, $subdomain)  === false)) {
                    //dominio não tem "ponto" ou é igual ao dominio
                    if ($domain->domainsRecords->where('type', $requestData['type-register'])->where('name', $subdomain)
                                               ->where('content', $requestData['type-register'])->count() == 0) {
                        // não existe record

                        if (isset($requestData['priority']) && is_numeric($requestData['priority'])) {
                            $priority = intval($requestData['priority']);
                        } else {
                            $priority = 1;
                        }

                        $proxy = false;

                        if ($requestData['type-register'] == 'MX') {
                            $cloudRecordId = $cloudFlareService->addRecord($requestData['type-register'], $subdomain, $requestData['value-record'], 0, false, $priority);
                        } else if ($requestData['type-register'] == 'TXT') {
                            $cloudRecordId = $cloudFlareService->addRecord($requestData['type-register'], $subdomain, $requestData['value-record'], 0, false);
                        } else {
                            $proxy         = $requestData['proxy'] == '1' ? true : false;
                            $cloudRecordId = $cloudFlareService->addRecord($requestData['type-register'], $subdomain, $requestData['value-record'], 0, $proxy, $priority);
                        }

                        if (!empty($cloudRecordId)) {
                            $newRecord = $domainRecordModel->create([
                                                                        'domain_id'            => $domain->id,
                                                                        'cloudflare_record_id' => $cloudRecordId,
                                                                        'type'                 => $requestData['type-register'],
                                                                        'name'                 => $requestData['name-register'],
                                                                        'content'              => $requestData['value-record'],
                                                                        'system_flag'          => 0,
                                                                        'priority'             => $priority,
                                                                        'proxy'                => $proxy,
                                                                    ]);

                            DB::commit();

                            return response()->json([
                                                        'message' => 'DNS cadastrado com sucesso',
                                                    ], 200);
                        } else {
                            //dominio já cadastrado
                            DB::rollBack();

                            return response()->json(['message' => 'Erro ao cadastrar domínios'], 400);
                        }
                    } else {
                        // dominio ja cadastrado
                        DB::rollBack();

                        return response()->json([
                                                    'message' => 'Este domínio já esta cadastrado',
                                                ], 400);
                    }
                } else {
                    // dominio nao permitido
                    DB::rollBack();

                    return response()->json(['message' => 'Domínio não permitido: ' . $subdomain], 400);
                }
            } else {
                DB::rollBack();

                return response()->json([
                                            'message' => 'Ocorreu um erro, dominio nao encontrado!',
                                        ], 400);
            }
        } catch (Exception $e) {
            DB::rollBack();

            report($e);
            Log::warning('Erro ao tentar salvar record (DomainRecordsApiController - store)');

            return response()->json([
                                        'message' => 'Erro ao tentar cadastrar entrada, tente novamente mais tarde',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @param $project
     * @param $domain
     * @param $domainRecord
     * @return JsonResponse
     */
    public function update(Request $request, $project, $domain, $domainRecord)
    {
        try {

            $domainModel       = new Domain();
            $domainRecordModel = new DomainRecord();
            $cloudFlareService = new CloudFlareService();

            $domain       = $domainModel->find(current(Hashids::decode($domain)));
            $domainRecord = $domainRecordModel->find(current(Hashids::decode($domainRecord)));

            if (Gate::allows('edit', [$domainRecord->domain->project])) {
            } else {
                return response()->json([
                                            'message' => 'Sem permissão para remover a entrada',
                                        ], 400);
            }

            if (!$domainRecord->system_flag) {
                if ($domainRecord->type == 'MX' || $domainRecord->type == 'TXT') {
                    $proxy = false;
                } else {
                    if ($request->input('proxy') == '1') {
                        $proxy = true;
                    } else {
                        $proxy = false;
                    }
                }

                $data = [
                    'type'    => $domainRecord->type,
                    'name'    => $domainRecord->name,
                    'content' => $domainRecord->content,
                    'proxied' => $proxy,
                ];

                $response = $cloudFlareService->updateRecordDetails($domain->cloudflare_domain_id, $domainRecord->cloudflare_record_id, $data);
                if ($response->success) {
                    $domainRecord->update([
                                              'proxy' => $data['proxied'],
                                          ]);

                    return response()->json([
                                                'message' => 'Proxy atualizado com sucesso!',
                                                'data'    => [
                                                    'domain' => $domain->id_code,
                                                ],
                                            ], 200);
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                            ], 400);
                }
            } else {
                return response()->json([
                                            'message' => 'Você não tem permissão para alterar este proxy',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao atualizar entrada dns personalizada (DomainRecordsApiController - update)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                    ], 400);
        }
    }

    /**
     * @param $project
     * @param $domain
     * @param $record
     * @return JsonResponse
     */
    public function destroy($project, $domain, $record)
    {
        try {
            if (!empty($project) && !empty($domain) && !empty($record)) {
                $domainRecordModel = new DomainRecord();
                $cloudFlareService = new CloudFlareService();

                $recordId = current(Hashids::decode($record));
                if (empty($recordId)) {
                    return response()->json([
                                                'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                            ], 400);
                }

                $record = $domainRecordModel->with(['domain', 'domain.project'])->find($recordId);

                if (Gate::allows('edit', [$record->domain->project])) {
                    $cloudFlareService->setZone($record->domain->name);

                    if ($cloudFlareService->deleteRecord($record->cloudflare_record_id)) {
                        // zona deletada
                        $recordsFind    = $domainRecordModel->where('id', $record->id)->first();
                        $recordsDeleted = $recordsFind->delete();
                        if ($recordsDeleted) {
                            return response()->json([
                                                        'message' => 'DNS removido com sucesso!',
                                                    ], 200);
                        } else {
                            return response()->json([
                                                        'message' => 'Não foi possível deletar!',
                                                    ], 400);
                        }
                    } else {
                        return response()->json([
                                                    'message' => 'Não foi possível deletar!',
                                                ], 400);
                    }
                } else {
                    return response()->json([
                                                'message' => 'Sem permissão para remover a entrada',
                                            ], 400);
                }
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('DomainRecordsApiController destroy - erro ao deletar record');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um problema, tente novamente mais tarde',
                                    ], 400);
        }
    }
}
