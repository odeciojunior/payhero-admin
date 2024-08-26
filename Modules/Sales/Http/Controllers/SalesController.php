<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\SaleService;
use PDF;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Vinkla\Hashids\Facades\Hashids;

class SalesController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view("sales::index");
    }

    /**
     * @param $filename
     * @return BinaryFileResponse
     */
    public function download($filename)
    {
        $file_path = storage_path("app/" . $filename);
        if (file_exists($file_path)) {
            return response()->download($file_path, $filename, ["Content-Length: " . filesize($file_path)]);
            //->deleteFileAfterSend(true);
        } else {
            abort(404);
        }
    }

    public function refundReceipt($hashid)
    {
        try {
            $id = current(Hashids::connection("sale_id")->decode($hashid));
            $arrDatewaysIds = foxutils()->isProduction()
                ? [
                    Gateway::SAFE2PAY_PRODUCTION_ID,
                    Gateway::IUGU_PRODUCTION_ID,
                    Gateway::ABMEX_PRODUCTION_ID,
                    Gateway::SIMPAY_PRODUCTION_ID,
                    Gateway::PAYUP_PRODUCTION_ID,
                    Gateway::MALGA_PRODUCTION_ID,
                ]
                : [
                    Gateway::SAFE2PAY_SANDBOX_ID,
                    Gateway::IUGU_SANDBOX_ID,
                    Gateway::ABMEX_SANDBOX_ID,
                    Gateway::SIMPAY_SANDBOX_ID,
                    Gateway::PAYUP_SANDBOX_ID,
                    Gateway::MALGA_SANDBOX_ID,
                ];

            $transaction = Transaction::with(["sale", "company"])
                ->where("sale_id", $id)
                ->whereIn("gateway_id", $arrDatewaysIds)
                ->where("type", Transaction::TYPE_PRODUCER)
                ->whereHas("sale", function ($query) {
                    $query
                        ->where("payment_method", Sale::CREDIT_CARD_PAYMENT)
                        ->orWhere("payment_method", Sale::BILLET_PAYMENT)
                        ->orWhere("payment_method", Sale::PIX_PAYMENT);
                })
                ->first();

            if (empty($transaction) || empty($transaction->company)) {
                throw new Exception("Não foi possivel continuar, entre em contato com o suporte!");
            }

            $pdf = SaleService::refundReceipt($hashid, $transaction);
            return $pdf->stream("comprovante.pdf");
        } catch (\Exception $e) {
            report($e);
            abort(404);
        }
    }
}
