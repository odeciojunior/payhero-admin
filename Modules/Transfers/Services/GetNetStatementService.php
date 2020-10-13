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

        $transactions = $data->list_transactions ?? [];

        $transactions = array_map(function ($item) {

            if (isset($item->summary) && isset($item->details) && is_array($item->details)) {

                $summary = $item->summary;
                $details = $item->details;

                $orderId = $summary->order_id;
                $arrayOrderId = explode('-', $orderId);
                $id = current(Hashids::connection('sale_id')->decode($arrayOrderId[0]));

                $transactionDate = $summary->transaction_date ?? '';

                $installmentDate = $details[0]->installment_date ?? '';
                $installmentAmount = $details[0]->installment_amount ?? '';
                $paymentDate = $details[0]->payment_date ?? '';
                $bank = $details[0]->bank ?? '';
                $agency = $details[0]->agency ?? '';
                $accountNumber = $details[0]->account_number ?? '';
                $subSellerRateAmount = $details[0]->subseller_rate_amount ?? 0;
                $subSellerRatePercentage = $details[0]->subseller_rate_percentage ?? 0;

                try {

                    $transactionDate = Carbon::parse($transactionDate)->format('d/m/Y H:i');
                } catch (Exception $exception) {

                }

                foreach (['installmentDate', 'paymentDate'] as $date) {

                    try {

                        ${$date} = Carbon::parse(${$date})->format('d/m/Y');
                    } catch (Exception $exception) {

                    }
                }

                /*$statement = new GetNetStatement();

                $statement->setId($id)
                    ->setOrderId($orderId)
                    ->setTransactionDate($transactionDate)
                    ->setInstallmentDate($installmentDate)
                    ->setPaymentDate($paymentDate)
                    ->setInstallmentAmount($installmentAmount)
                    ->setSubSellerRateAmount($subSellerRateAmount)
                    ->setSubSellerRatePercentage($subSellerRatePercentage)
                    ->setBank($bank)
                    ->setAgency($agency)
                    ->setAccountNumber($accountNumber)
                    ->setProduct('');*/

                $statement = [
                    'id' => $id,
                    'orderId' => Hashids::connection('sale_id')->encode(737634),
                    //'orderId' => $arrayOrderId[0],
                    'transactionDate' => $transactionDate,
                    'installmentDate' => $installmentDate,
                    'paymentDate' => $paymentDate,
                    'installmentAmount' => FoxUtils::formatMoney($installmentAmount),
                    'subSellerRateAmount' => $subSellerRateAmount,
                    'subSellerRatePercentage' => $subSellerRatePercentage,
                    'bank' => $bank,
                    'agency' => $agency,
                    'accountNumber' => $accountNumber,
                    'product' => '',
                ];

                $this->data[] = $statement;
            }
        }, $transactions);

        return $this->data;
    }
}
