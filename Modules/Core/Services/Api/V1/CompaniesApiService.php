<?php

namespace Modules\Core\Services\Api\V1;

use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\User;
use Vinkla\Hashids\Facades\Hashids;

class CompaniesApiService
{
    public static function prepareRequestData(array $request): array
    {
        return [];
    }

    public static function uploadDocuments(string $id, array $request): array
    {
        return [];
    }
}
