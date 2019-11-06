<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Companies\Transformers\CompanyResource;
use Modules\Core\Entities\Company;

/**
 * Class CompaniesService
 * @package Modules\Core\Services
 */
class CompanyService
{
    /**
     * @param bool $paginate
     * @return array|AnonymousResourceCollection
     * @var Company $companies
     */
    public function getCompaniesUser($paginate = false)
    {
        try {

            $companyModel = new Company();

            $companies = $companyModel->with('user')->where('user_id', auth()->user()->account_owner);

            if ($paginate) {
                return CompanyResource::collection($companies->paginate(10));
            } else {
                return CompaniesSelectResource::collection($companies->get());
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar companies (CompaniesService - getCompaniesUser)');
            report($e);

            return [];
        }
    }
}
