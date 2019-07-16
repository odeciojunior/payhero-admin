<?php

namespace Modules\Apps\Http\Controllers;

use Illuminate\Http\Request;
use App\Entities\Transaction;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class AppsController extends Controller {

    public function index() {

        return view('apps::index');

        // $sales = Transaction::leftJoin('sales as sale', function($join) {
        //                     $join->on('transactions.sale', '=', 'sale.id');
        //                     $join->where('transactions.company', '=', '12');
        //                })
        //                ->whereBetween('sale.end_date', ['2019-01-01', date('Y-m-d', strtotime('2019-07-09' . ' + 1 day'))])
        //                ->get()->toArray();

        // $value = 0;

        // foreach($sales as $sale){

        //     $value += $sale['value'];
        // }

        // dd($value);
    }

}
