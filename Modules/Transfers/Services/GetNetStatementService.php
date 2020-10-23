<?php


namespace Modules\Transfers\Services;


use Carbon\Carbon;
use Exception;
use Modules\Core\Services\FoxUtils;
use stdClass;
use Vinkla\Hashids\Facades\Hashids;

class GetNetStatementService
{

    protected $data = [];

    public function performStatement(stdClass $data)
    {
        $transactions = array_reverse($data->list_transactions) ?? [];

        $transactions = array_map(function ($item) {
            if (isset($item->summary) && isset($item->details) && is_array($item->details)) {
                $summary = $item->summary;
                $details = $item->details;

                $orderId = $summary->order_id;
                $arrayOrderId = explode('-', $orderId);
                $id = current(Hashids::connection('sale_id')->decode($arrayOrderId[0]));

                $transactionDate = $summary->transaction_date ?? '';

                $installmentDate = $details[0]->installment_date ?? '';
                $installmentAmount = $details[0]->installment_amount ?? 0;
                $paymentDate = $details[0]->payment_date ?? '';
                $subSellerRateConfirmDate = $details[0]->subseller_rate_confirm_date ?? '';
                $subSellerRateAmount = $details[0]->subseller_rate_amount ?? 0;
                $subSellerRatePercentage = $details[0]->subseller_rate_percentage ?? 0;

                try {
                    $transactionDate = Carbon::parse($transactionDate)->format('d/m/Y');
                } catch (Exception $exception) {
                }

                foreach (['installmentDate', 'paymentDate'] as $date) {
                    try {
                        ${$date} = Carbon::parse(${$date})->format('d/m/Y');
                    } catch (Exception $exception) {
                    }
                }

                $statement = (object) [
                    //'id' => $id,
                    //'orderId' => Hashids::connection('sale_id')->encode(737634),
                    'orderId' => $arrayOrderId[0],
                    'transactionDate' => $transactionDate,
                    'installmentDate' => $installmentDate,
                    'paymentDate' => $paymentDate,
                    'installmentAmount' => FoxUtils::formatMoney($installmentAmount / 100),
                    'subSellerRateAmount' => FoxUtils::formatMoney($subSellerRateAmount / 100),
                    'subSellerRatePercentage' => $subSellerRatePercentage,
                    'subSellerRateConfirmDate' => $subSellerRateConfirmDate,
                ];

                $this->data[] = $statement;
            }
        }, $transactions);

        return $this->data;
    }
}
