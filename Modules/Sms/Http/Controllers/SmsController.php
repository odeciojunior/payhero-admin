<?php

namespace Modules\Sms\Http\Controllers;

use Exception;
use DateTimeZone;
use Zenvia\Model\Sms;
use App\Entities\Plan;
use Zenvia\Model\SmsFacade;
use App\Entities\ZenviaSms;
use App\Entities\SmsMessage;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Zenvia\Zenvia;
use Yajra\DataTables\Facades\DataTables;
use Modules\Sms\Transformers\SmsResource;
use NotificationChannels\Zenvia\ZenviaChannel;
use NotificationChannels\Zenvia\ZenviaMessage;
use Modules\Sms\Http\Requests\ZenviaSmsStoreRequest;
use Modules\Sms\Http\Requests\ZenviaSmsUpdateRequest;

class SmsController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $data = $request->all();
            $zenviaSmsModel = new ZenvaSms();

            if (isset($data['project'])) {
                $projectId = Hashids::decode($data['project'])[0];
                $sms       = $zenviaSmsModel->where('project', $projectId)->get();

                return SmsResource::collection($sms);
            } else {
                return response()->json('projeto não encontrado', 406);
            }
        } catch (Exception $e) {
            Log::warning("Erro ao tentar acessar dados (SmsController - index)");
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        try {
            return view('sms::create');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar redirecionar para pagina de cadastro de sms (SmsController - create)');
            report($e);
        }
    }

    /**
     * @param ZenviaSmsStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ZenviaSmsStoreRequest $request)
    {
        try {
            $data            = $request->all();
            $data['project'] = Hashids::decode($data['project'])[0];

            if ($data['time'] == '') {
                $data['time'] = '0';
            }

            if ($data['time'] == '1') {
                if ($data['period'] == 'minutes') {
                    $data['period'] = 'minute';
                } else if ($data['period'] == 'hours') {
                    $data['period'] = 'hour';
                } else if ($data['period'] == 'days') {
                    $data['period'] = "day";
                }
            }

            $zenviaModel = new ZenviaSms();
            $zenviaSmsSaved = $zenviaModel->create($data);

            if ($zenviaSmsSaved) {
                return response()->json('Sucesso', 200);
            }

            return response()->json('Erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar salvar sms (SmsController - store)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        try {
            $data = $request->all();
            if (isset($data['smsId'])) {
                $smsId = Hashids::decode($data['smsId'])[0];
                $zenviaModel = new ZenviaSms();
                $sms   = $zenviaModel->find($smsId);

                if ($sms) {
                    return view('sms::details', ['sms' => $sms]);
                }
            }

            return response()->json('Erro ao buscar Notificação');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar dados (SmsController - show)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function edit(Request $request)
    {
        try {
            $data = $request->all();
            if (isset($data['smsId'])) {
                $sms = Hashids::decode($data['smsId'])[0];
                $zenviaModel = new ZenviaSms();
                $sms = $zenviaModel->find($sms);

                return view('sms::edit', ['sms' => $sms, 'sms_id' => $data['smsId']]);
            }

            return response()->json('Dados não encontrados');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela de editar sms');
            report($e);
        }
    }

    /**
     * @param ZenviaSmsUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ZenviaSmsUpdateRequest $request, $id)
    {
        try {
            $dataValidated = $request->validated();
            $smsId         = Hashids::decode($id)[0];

            if ($dataValidated['time'] == '') {
                $dataValidated['time'] = '0';
            }

            if ($dataValidated['time'] == '1') {
                if ($dataValidated['period'] == 'minutes') {
                    $dataValidated['period'] = 'minute';
                } else if ($dataValidated['period'] == 'hours') {
                    $dataValidated['period'] = 'hour';
                } else if ($dataValidated['period'] == 'days') {
                    $dataValidated['period'] = 'day';
                }
            }

            $zenviaModel = new ZenviaSms();
            $sms        = $zenviaModel->find($smsId);
            $smsUpdated = $zenviaModel->update($dataValidated);
            if ($smsUpdated) {
                return response()->json('Sucesso', 200);
            }

            return response()->json('Erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar atualizar sms (SmsController - update)');
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
            $zenviaSmsId      = Hashids::decode($id)[0];
            $zenviaSms        = $this->getZenviaSms()->find($zenviaSmsId);
            $zenviaSmsDeleted = $zenviaSms->delete();
            if ($zenviaSmsDeleted) {
                return response()->json('Sucesso', 200);
            }

            return response()->json('Erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar deletar zenviSms (SmsController - destroy)');
            report($e);
        }
    }


    public function editarSms($id)
    {
        $zenviaSmsModel = new ZenviaSms();
        $sms = $zenviaSmsModel->find($id);

        return view('sms::editar', [
            'pixel' => $pixel,
        ]);
    }

    public function getFormAddSms(Request $request)
    {

        $dados = $request->all();

        $form = view('sms::cadastro');

        return response()->json($form->render());
    }
}

