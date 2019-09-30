<?php

namespace Modules\Plans\Http\Controllers;

use App\Entities\ZenviaSms;
use Auth;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Project;
use Modules\Core\Helpers\CaminhoArquivosHelper;
use Modules\Core\Services\ProductService;
use Modules\Plans\Http\Requests\PlanStoreRequest;
use Modules\Plans\Http\Requests\PlanUpdateRequest;
use Modules\Plans\Transformers\PlansResource;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;

class PlansController extends Controller
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {

    }
}
