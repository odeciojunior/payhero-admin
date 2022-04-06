<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Modules\Core\Entities\Gateway;
use PDF;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\CompanyService;
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

            $id = current(Hashids::connection('sale_id')->decode($hashid));

            $arrDatewaysIds = foxutils()->isProduction() ?
            [Gateway::ASAAS_PRODUCTION_ID, Gateway::GETNET_PRODUCTION_ID, Gateway::GERENCIANET_PRODUCTION_ID, Gateway::SAFE2PAY_PRODUCTION_ID] :
            [Gateway::ASAAS_SANDBOX_ID, Gateway::GETNET_SANDBOX_ID, Gateway::GERENCIANET_SANDBOX_ID, Gateway::SAFE2PAY_SANDBOX_ID];

            $transaction = Transaction::with([
                'sale',
                'company'
            ])->where('sale_id', $id)
                ->whereIn('gateway_id', $arrDatewaysIds)
                ->where('type', Transaction::TYPE_PRODUCER)
                ->whereHas('sale', function ($query) {
                    $query
                    ->where('payment_method', Sale::CREDIT_CARD_PAYMENT)
                    ->orWhere('payment_method',  Sale::BOLETO_PAYMENT)
                    ->orWhere('payment_method',  Sale::PIX_PAYMENT);
                })->first();

            if(empty($transaction) || empty($transaction->company)){
                throw new Exception('Não foi possivel continuar, entre em contato com o suporte!');
            }

            $service = Gateway::getServiceById($transaction->gateway_id);
            $pdf = $service->refundReceipt($hashid,$transaction);
            return $pdf->stream('comprovante.pdf');

        } catch (\Exception $e) {
            report($e);
            abort(404);
        }
    }
}


