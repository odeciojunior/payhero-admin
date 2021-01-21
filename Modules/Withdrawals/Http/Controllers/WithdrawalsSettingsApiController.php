<?php

namespace Modules\Withdrawals\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\Facades\Image;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\ProjectReviews;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Entities\WithdrawalSettings;
use Modules\Core\Events\WithdrawalRequestEvent;
use Modules\Core\Services\AmazonFileService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\ProjectReviews\Http\Requests\ProjectReviewsUpdateRequest;
use Modules\Withdrawals\Http\Requests\WithdrawalSettingsRequest;
use Modules\Withdrawals\Transformers\WithdrawalResource;
use Modules\Withdrawals\Transformers\WithdrawalSettingsResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class WithdrawalsSettingsApiController
{
    public function index(Request $request)
    {
        try {
            $withdrawalSettingsModel = new WithdrawalSettings();
            $companyModel = new Company();
            $companyId = current(Hashids::decode($request->company));

//            if (empty($request->input('page')) || $request->input('page') == '1') {
//                activity()->on($withdrawalSettingsModel)->tap(
//                    function (Activity $activity) {
//                        $activity->log_name = 'visualization';
//                    }
//                )->log('Visualizou tela configurações do saque automático');
//            }

            if (empty($companyId)) {
                return response()->json(['message' => 'Empresa não encontrada',], 404);
            }

            $company = $companyModel->find($companyId);

            if (!Gate::allows('edit', [$company])) {
                return response()->json(['message' => 'Sem permissão para visualizar saques',], 403);
            }

            $withdrawals = $withdrawalSettingsModel->where('company_id', $companyId)->orderBy('id', 'DESC');

            return WithdrawalSettingsResource::collection($withdrawals->paginate(10));
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao visualizar saques',], 400);
        }
    }

    /**
     * Show the specified resource.
     * @param string $companyId
     * @param string $settingsId
     * @return JsonResponse|WithdrawalSettingsResource
     */
    public function show($companyId, $settingsId = null)
    {
        try {
            $companyId = current(Hashids::decode($companyId));
            if ($companyId) {
                $withdrawalSettingsModel = new WithdrawalSettings();
                $settingsId = current(Hashids::decode($settingsId));
                if ($settingsId) {
                    $settings = $withdrawalSettingsModel->findOrFail($settingsId);
                } else {
                    $settings = $withdrawalSettingsModel->where('company_id', $companyId)->first() ?? null;
                }

                if ($settings) {
                    return new WithdrawalSettingsResource($settings);
                } else {
                    return response()->json(['message' => 'Nenhuma configuração de saques automáticos foi encontrada'], 404);
                }

            } else {
                return response()->json(['message' => 'Erro ao carregar configurações de saques automáticos da empresa'], 400);
            }
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 500);

        }
    }

    public function store(WithdrawalSettingsRequest $request): JsonResponse
    {
        try {
            $requestData = $request->validated();

            $withdrawalSettingsModel = new WithdrawalSettings();
            $companyModel = new Company();

            $data = $request->all();

            $company = $companyModel->find(current(Hashids::decode($data['company_id'])));

            if (!Gate::allows('edit', [$company])) {
                return response()->json(['message' => 'Sem permissão para salvar configurações de saques'], 403);
            }

            $companyService = new CompanyService();

            $withdrawalSettings = $withdrawalSettingsModel->create(
                [
                    'company_id' => $company->id,
                    'rule'       => $requestData['rule'],
                    'frequency'  => $requestData['frequency'],
                    'weekday'    => $requestData['weekday'],
                    'day'        => $requestData['day'],
                    'amount'     => $requestData['amount'],
                ]
            );

            return response()->json([
                'message' => 'Configurações de saques automáticos salvos com sucesso',
                'data'    => WithdrawalSettingsResource::make($withdrawalSettings)
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Ocorreu um erro, tente novamnte mais tarde!'], 500);
        }
    }

    /**
     * @param ProjectReviewsUpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ProjectReviewsUpdateRequest $request, $id)
    {
        $projectReviewModel = new ProjectReviews();
        $data = $request->validated();
        $reviewId = current(Hashids::decode($id));
        $review = $projectReviewModel->find($reviewId);
        $amazonFileService = app(AmazonFileService::class);

        if ($reviewId && !empty($review)) {
            $applyPlanArray = [];
            if (in_array('all', $data['apply_on_plans'])) {
                $applyPlanArray[] = 'all';
            } else {
                foreach ($data['apply_on_plans'] as $key => $value) {
                    $applyPlanArray[] = current(Hashids::decode($value));
                }
            }
            $applyPlanEncoded = json_encode($applyPlanArray);

            $reviewUpdated = $review->update([
                'name'           => $data['name'],
                'description'    => $data['description'],
                'stars'          => $data['stars'] ?? 0,
                'active_flag'    => $data['active_flag'] ?? 0,
                'apply_on_plans' => $applyPlanEncoded,
            ]);

            $amazonPath = null;
            $photo = $request->file('photo');
            if ($photo != null) {
                try {

                    if (!$data['photo_w'] || !$data['photo_h']) {
                        $data['photo_h'] = $data['photo_w'] = 200;
                    }

                    $img = Image::make($photo->getPathname());
                    $img->crop(
                        $data['photo_w'],
                        $data['photo_h'],
                        $data['photo_x1'],
                        $data['photo_y1']
                    );
                    $img->resize(200, 200);
                    $img->save($photo->getPathname());

                    $amazonFileService->setDisk('s3_plans_reviews');
                    $amazonPath = $amazonFileService->uploadFile(
                        'uploads/user/' . Hashids::encode(auth()->user()->account_owner_id) . '/plans-reviews/public/',
                        $photo,
                        $photo->getFilename(),
                        Hashids::encode($review->id) . '.' . $photo->extension(),
                        'public'
                    );

                    $review->photo = $amazonPath;
                    $review->save();
                } catch (Exception $e) {
                    report($e);
                }
            }

            if ($reviewUpdated) {
                return response()->json(['message' => 'Review atualizado com sucesso!'], 200);
            } else {
                return response()->json([
                    'message' => 'Erro ao atualizar review',
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'Review não encontrado',
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            if (empty($id)) {
                return response()->json([
                    'message' => 'Erro ao deletar review',
                ], 404);
            }

            $projectReviewModel = new ProjectReviews();
            $review = $projectReviewModel->find(current(Hashids::decode($id)));

            if (!empty($review)) {
                $reviewDeleted = $review->delete();
                if ($reviewDeleted) {
                    return response()->json(['message' => 'Review deletada com sucesso!']);
                }
            }

            return response()->json([
                'message' => 'Erro ao deletar review',
            ], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Erro ao deletar review',
            ], 400);
        }
    }
}
