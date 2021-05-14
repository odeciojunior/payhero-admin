<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Entities\Gateway;
use PDF;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\GetnetBackOfficeService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Vinkla\Hashids\Facades\Hashids;

class SalesController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('sales::index');
    }

    /**
     * @param $filename
     * @return BinaryFileResponse
     */
    public function download($filename)
    {
        $file_path = storage_path('app/' . $filename);
        if (file_exists($file_path)) {
            return response()->download($file_path, $filename, [
                'Content-Length: ' . filesize($file_path)
            ]);
            //->deleteFileAfterSend(true);
        } else {
            abort(404);
        }
    }

    public function refundReceipt($hashid)
    {
        try {

            $getnetService = new GetnetBackOfficeService();

            $id = current(Hashids::connection('sale_id')->decode($hashid));
            $transaction = Transaction::with([
                'sale',
                'company'
            ])->where('sale_id', $id)
                ->whereIn('gateway_id', [Gateway::GETNET_SANDBOX_ID, Gateway::GETNET_PRODUCTION_ID])
                ->where('type', (new Transaction())->present()->getType('producer'))
                ->whereHas('sale', function ($query) {
                    $query->where('payment_method', 1);
                })->first();

            $company = (object)$transaction->company->toArray();
            $result = $getnetService->setStatementSubSellerId($company->subseller_getnet_id)
                ->setStatementSaleHashId($hashid)
                ->getStatement();
            $result = json_decode($result);
            $sale = end($result->list_transactions);

            $sale->flag = strtoupper($transaction->sale->flag) ?? null;

            $pdf = PDF::loadView('sales::refundreceipt', compact('company', 'sale'));

            return $pdf->stream('comprovante.pdf');

        } catch (\Exception $e) {
            abort(404);
        }
    }
}


