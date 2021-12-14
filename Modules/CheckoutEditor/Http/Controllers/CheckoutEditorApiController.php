<?php

namespace Modules\CheckoutEditor\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\CheckoutEditor\Transformers\CheckoutConfigResource;
use Modules\Core\Entities\CheckoutConfig;
use Modules\Core\Entities\Company;
use Modules\Core\Services\AmazonFileService;
use Intervention\Image\Facades\Image;
use Modules\Products\Http\Requests\UpdateCheckoutConfigRequest;

class CheckoutEditorApiController extends Controller
{
    public function show($projectId)
    {
        try {
            $projectId = hashids_decode($projectId);

            $config = CheckoutConfig::where('project_id', $projectId)->first();

            return new CheckoutConfigResource($config);

        } catch (\Exception $e) {
            return foxutils()->isProduction()
                ? response()->json(['message' => 'Erro ao obter as configurações do checkout'])
                : response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, UpdateCheckoutConfigRequest $request)
    {
        try {
            $amazonFileService = app(AmazonFileService::class);

            $id = hashids_decode($id);

            $data = $request->all();

            $config = CheckoutConfig::find($id);

            $logo = $request->file('logo');
            if (!empty($logo)) {
                $amazonFileService->deleteFile($config->checkout_logo);
                $img = Image::make($logo->getPathname());

                $img->resize(null,300, function ($constraint) {
                        $constraint->aspectRatio();
                    });

                $img->save($logo->getPathname());

                $amazonPathLogo = $amazonFileService->uploadFile('uploads/user/' . hashids_encode(auth()->user()->account_owner_id) . '/public/projects/' . hashids_encode($id) . '/logo', $logo);
                $data['checkout_logo'] = $amazonPathLogo;
            }

            $data['company_id'] = hashids_decode($data['company_id']);

            if($data['company_id'] !== $config->company_id) {
                $company = Company::find($data['company_id']);
                $data['pix_enabled'] = $company->has_pix_key &&  $company->pix_key_situation === 'VERIFIED' && $data['pix_enabled'];
            }

            $config::update($data);

            return new CheckoutConfigResource($config);
        } catch (\Exception $e) {
            return foxutils()->isProduction()
                ? response()->json(['message' => 'Erro ao atualizar as configurações do checkout'])
                : response()->json(['message' => $e->getMessage()]);
        }
    }
}
