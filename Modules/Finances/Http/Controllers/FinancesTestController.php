<?php

namespace Modules\Finances\Http\Controllers;

use App\Entities\Sale;
use App\Entities\Transaction;
use App\Entities\Transfer;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class FinancesTestController
 * @package Modules\Finances\Http\Controllers
 */
class FinancesTestController extends Controller
{
    public function index()
    {
        $dataValue = [
            'type' => 'payment',

            'data' => [
                'id'     => '111888723',
                'status' => 'approved',
            ],
        ];

        return redirect()->route('mercadopago', $dataValue);
    }
}


