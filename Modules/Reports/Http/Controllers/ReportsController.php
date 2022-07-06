<?php

namespace Modules\Reports\Http\Controllers;

use DateTime;
use Exception;
use Carbon\Carbon;
use Matrix\Builder;
use Illuminate\Http\Request;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Modules\Core\Entities\UserProject;
use JMS\Serializer\Tests\Fixtures\Discriminator\Car;
use Modules\Reports\Transformers\SalesByOriginResource;

class ReportsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function resume()
    {
        return view('reports::resume');
    }

    public function sales()
    {
        return view('reports::sales');
    }

    public function finances()
    {
        return view('reports::finances');
    }

    public function marketing()
    {
        return view('reports::marketing');
    }

    public function checkouts()
    {
        return view('reports::checkouts');
    }

    public function projections()
    {
        return view('reports::projections');
    }

    public function coupons()
    {
        return view('reports::coupons');
    }

    public function pending()
    {
        return view('reports::pending');
    }

    public function blockedbalance()
    {
        return view('reports::blockedbalance');
    }
}
