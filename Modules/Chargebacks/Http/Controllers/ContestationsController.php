<?php

namespace Modules\Chargebacks\Http\Controllers;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Chargebacks\Transformers\ContestationResource;
use Modules\Chargebacks\Transformers\SaleContestationFileResource;
use Modules\Core\Entities\SaleContestation;
use Modules\Core\Entities\SaleContestationFile;
use Modules\Core\Services\ChargebackService;
use Modules\Core\Services\ContestationService;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class ContestationsController extends Controller
{
    public function index()
    {
        return view("chargebacks::contestations-index");
    }
}
