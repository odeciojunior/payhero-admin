<?php

namespace Modules\Sales\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Client;
use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Delivery;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Shipping;
use Modules\Core\Services\ProjectService;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\UserProject;
use Modules\Sales\Transformers\SalesResource;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Sales\Http\Requests\SaleUpdateRequest;
use Modules\Sales\Exports\Reports\SaleReportExport;
use Modules\Sales\Transformers\TransactionResource;

class SalesController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('sales::index');
    }
}


