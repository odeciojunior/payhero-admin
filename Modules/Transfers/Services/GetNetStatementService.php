<?php


namespace Modules\Transfers\Services;


use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\FoxUtils;
use stdClass;
use Vinkla\Hashids\Facades\Hashids;

class GetNetStatementService
{

    protected $data = [];

    public function performStatement(stdClass $data)
    {
        $transactions = array_reverse($data->list_transactions) ?? [];
        $transferPresent = (new Transfer())->present();

        $transactions = array_map(function ($item) use ($transferPresent) {
            if (isset($item->summary) && isset($item->details) && is_array($item->details)) {
                $summary = $item->summary;
                $details = $item->details;

                $orderId = $summary->order_id;
                $arrayOrderId = explode('-', $orderId);

                $transactionDate = $summary->transaction_date ?? '';

                $installmentDate = $details[0]->installment_date ?? '';
                $installmentAmount = $details[0]->installment_amount ?? 0;
                $paymentDate = Carbon::parse($details[0]->payment_date) ?? '';
                $subSellerRateClosingDate = $details[0]->subseller_rate_closing_date ?? '';
                $subSellerRateConfirmDate = $details[0]->subseller_rate_confirm_date ?? '';
                $subSellerRateAmount = $details[0]->subseller_rate_amount ?? 0;
                $subSellerRatePercentage = $details[0]->subseller_rate_percentage ?? 0;

                try {
                    if (request('statement_data_type') == 'liquidation_date') {
                        $transactionDate = Carbon::parse($transactionDate)->format('d/m/Y');
                    } else {
                        $transactionDate = Carbon::parse($transactionDate)->format('d/m/Y');
                    }
                } catch (Exception $exception) {
                }

                foreach (['installmentDate', 'paymentDate'] as $date) {
                    try {
                        ${$date} = Carbon::parse(${$date})->format('d/m/Y');
                    } catch (Exception $exception) {
                    }
                }
                if ($details[0]->release_status == 'N') {
                    $status = $transferPresent->getStatusGetnet('Aguardando postagem válida');
                } elseif ($details[0]->release_status == 'S' && Carbon::now()->lessThan(Carbon::createFromFormat('d/m/Y',
                        $paymentDate))) {
                    $status = $transferPresent->getStatusGetnet('Aguardando liquidação');
                } else {
                    $status = $transferPresent->getStatusGetnet('Pago');
                }

                $statement = (object)[
                    'orderId' => $arrayOrderId[0],
                    'transactionDate' => $transactionDate,
                    'installmentDate' => $installmentDate,
                    'paymentDate' => $paymentDate,
                    'installmentAmount' => FoxUtils::formatMoney($installmentAmount / 100),
                    'subSellerRateAmount' => FoxUtils::formatMoney($subSellerRateAmount / 100),
                    'subSellerRateSumTotalAmount' => $subSellerRateAmount,
                    'subSellerRatePercentage' => $subSellerRatePercentage,
                    'subSellerRateClosingDate' => Carbon::parse($subSellerRateClosingDate)->format('d/m/Y') ?? '',
                    'subSellerRateConfirmDate' => Carbon::parse($subSellerRateConfirmDate)->format('d/m/Y') ?? '',
                    'status' => $status
                ];


                if (request('status') == 'all' || !in_array(request('status'), ['all', 1, 2, 3])) {
                    $this->data[] = $statement;
                } elseif (request('status') == $status) {
                    $this->data[] = $statement;
                }
            }
        }, $transactions);

        return $this->data;
    }
}
