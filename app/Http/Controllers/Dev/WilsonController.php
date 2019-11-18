<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\SaleService;

/**
 * Class WilsonController
 * @package App\Http\Controllers\Dev
 */
class WilsonController extends Controller
{
    public function wilsonFunction()
    {
        $sale        = Sale::where('id', 11012)->first();
        $saleService = new SaleService();
        $response    = (object) [
            'status'         => 'success',
            'message'        => 'Venda cancelada com sucesso!',
            'status_gateway' => 'successed',
            'status_sale'    => 'paid',
            'response'       => [],
        ];

        $saleService->updateSaleRefunded($sale, 300, $response);
        dd('deu bom ?');
    }
}


