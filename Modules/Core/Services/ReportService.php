<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Core\Entities\Sale;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\Checkout;
use Illuminate\Support\Facades\Auth;
use Modules\Reports\Transformers\SalesByOriginResource;
use Modules\Core\Entities\Transaction;

class ReportService
{
    /**
     * @param $date
     * @param $projectId
     * @param $currency
     * @return array|null
     */
    public function getChartData($date, $projectId, $currency)
    {
        if ($date['startDate'] == $date['endDate']) {
            return $this->getByHours($date, $projectId, $currency);
        } else if ($date['startDate'] != $date['endDate']) {
            $data       = null;
            $startDate  = Carbon::createFromFormat('Y-m-d', $date['startDate'], 'America/Sao_Paulo');
            $endDate    = Carbon::createFromFormat('Y-m-d', $date['endDate'], 'America/Sao_Paulo');
            $diffInDays = $endDate->diffInDays($startDate);
            if ($projectId) {
                if ($diffInDays <= 20) {
                    return $this->getByDays($date, $projectId, $currency);
                } else if ($diffInDays > 20 && $diffInDays <= 40) {
                    return $this->getByTwentyDays($date, $projectId, $currency);
                } else if ($diffInDays > 40 && $diffInDays <= 60) {
                    return $this->getByFortyDays($date, $projectId, $currency);
                } else if ($diffInDays > 60 && $diffInDays <= 140) {
                    return $this->getByWeek($date, $projectId, $currency);
                } else if ($diffInDays > 140) {
                    return $this->getByMonth($date, $projectId, $currency);
                }
            } else {

                return [
                    'label_list'       => ['', ''],
                    'credit_card_data' => [0, 0],
                    'boleto_data'      => [0, 0],
                    'currency'         => $currency,
                ];
            }
        }
    }

    /**
     * @param $data
     * @param $projectId
     * @param $currency
     * @return array
     */
    private function getByHours($data, $projectId, $currency)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $companyModel = new Company();
        $saleModel    = new Sale();

        if (Carbon::parse($data['startDate'])->format('m/d/y') == Carbon::now()->format('m/d/y')) {

            $labelList   = [];
            $currentHour = date('H');
            $startHour   = 0;
            while ($startHour <= $currentHour) {
                array_push($labelList, $startHour . 'h');
                $startHour++;
            }
        } else {
            $labelList = [
                '0h', '1h', '2h', '3h', '4h', '5h', '6h', '7h', '8h', '9h', '10h', '11h',
                '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h', '21h', '22h', '23h',
            ];
        }

        $userCompanies = $companyModel
            ->where('user_id', auth()->user()->account_owner_id)
            ->pluck('id')
            ->toArray();

        $orders = $saleModel
            ->select(\DB::raw('count(*) as count, HOUR(sales.start_date) as hour, SUM(transaction.value) as value, sales.payment_method'))
            ->leftJoin('transactions as transaction', function($join) use ($userCompanies) {
                $join->on('transaction.sale_id', '=', 'sales.id');
                $join->whereIn('transaction.company_id', $userCompanies);
            })
            ->where('sales.owner_id', auth()->user()->account_owner_id)
            ->where('sales.project_id', $projectId)
            //                       ->where('sales.status', 1)
            ->whereDate('sales.start_date', $data['startDate'])
            ->groupBy('hour', 'sales.payment_method')
            ->get()->toArray();

        $creditCardData = [];
        $boletoData     = [];

        foreach ($labelList as $label) {
            $creditCardValue = 0;
            $boletoValue     = 0;
            foreach ($orders as $order) {
                if ($order['hour'] == preg_replace("/[^0-9]/", "", $label)) {
                    if ($order['payment_method'] == 1) {
                        $creditCardValue = substr(intval($order['value']), 0, -2);
                    } else {
                        $boletoValue = substr(intval($order['value']), 0, -2);
                    }
                }
            }
            array_push($creditCardData, $creditCardValue);
            array_push($boletoData, $boletoValue);
        }

        return [
            'label_list'       => $labelList,
            'credit_card_data' => $creditCardData,
            'boleto_data'      => $boletoData,
            'currency'         => $currency,
        ];
    }

    /**
     * @param $data
     * @param $projectId
     * @param $currency
     * @param $diffInDays
     * @return array
     */
    private function getByDays($data, $projectId, $currency)
    {
        try {
            $companyModel = new Company();
            $saleModel    = new Sale();

            $labelList    = [];
            $dataFormated = Carbon::parse($data['startDate']);
            $endDate      = Carbon::parse($data['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d-m'));
                $dataFormated = $dataFormated->addDays(1);
            }

            $data['endDate'] = date('Y-m-d', strtotime($data['endDate'] . ' + 1 day'));

            $userCompanies = $companyModel->where('user_id', auth()->user()->account_owner_id)->pluck('id')->toArray();

            $orders = $saleModel
                ->select(\DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
                ->leftJoin('transactions as transaction', function($join) use ($userCompanies) {
                    $join->on('transaction.sale_id', '=', 'sales.id');
                    $join->whereIn('transaction.company_id', $userCompanies);
                })
                ->where('sales.owner_id', auth()->user()->account_owner_id)
                ->where('sales.project_id', $projectId)
                //                           ->where('sales.status', 1)
                ->whereBetween('start_date', [$data['startDate'], date('Y-m-d', strtotime($data['endDate'] . ' + 1 day'))])
                ->groupBy('date', 'sales.payment_method')
                ->get()->toArray();

            $creditCardData = [];
            $boletoData     = [];

            foreach ($labelList as $label) {
                $creditCardValue = 0;
                $boletoValue     = 0;
                foreach ($orders as $order) {
                    if (Carbon::parse($order['date'])->format('d-m') == $label) {
                        if ($order['payment_method'] == 1) {
                            $creditCardValue = substr(intval($order['value']), 0, -2);
                        } else {
                            $boletoValue = substr(intval($order['value']), 0, -2);
                        }
                    }
                }
                array_push($creditCardData, $creditCardValue);
                array_push($boletoData, $boletoValue);
            }

            return [
                'label_list'       => $labelList,
                'credit_card_data' => $creditCardData,
                'boleto_data'      => $boletoData,
                'currency'         => $currency,
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados');
            report($e);
        }
    }

    /**
     * @param $date
     * @param $projectId
     * @param $currency
     * @param $diffInDays
     * @return array
     */
    private function getByTwentyDays($date, $projectId, $currency)
    {
        try {

            $companyModel = new Company();
            $saleModel    = new Sale();

            $labelList    = [];
            $dataFormated = Carbon::parse($date['startDate'])->addDays(1);
            $endDate      = Carbon::parse($date['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays(2);
                if ($dataFormated->diffInDays($endDate) < 2 && $dataFormated->diffInDays($endDate) > 0) {
                    array_push($labelList, $dataFormated->format('d/m'));
                    $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                    array_push($labelList, $dataFormated->format('d/m'));
                    break;
                }
            }
            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'));

            $userCompanies = $companyModel->where('user_id', auth()->user()->account_owner_id)->pluck('id')->toArray();

            $orders = $saleModel
                ->select(\DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
                ->leftJoin('transactions as transaction', function($join) use ($userCompanies) {
                    $join->on('transaction.sale_id', '=', 'sales.id');
                    $join->whereIn('transaction.company_id', $userCompanies);
                })
                ->where('sales.owner_id', auth()->user()->account_owner_id)
                ->where('sales.project_id', $projectId)
                ->whereBetween('start_date', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date', 'sales.payment_method')
                ->get()->toArray();

            $creditCardData = [];
            $boletoData     = [];
            foreach ($labelList as $label) {
                $creditCardValue = 0;
                $boletoValue     = 0;
                foreach ($orders as $order) {
                    if ((Carbon::parse($order['date'])
                               ->subDays(1)->format('d/m') == $label) || (Carbon::parse($order['date'])
                                                                                ->format('d/m') == $label)) {

                        if ($order['payment_method'] == 1) {
                            $creditCardValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        } else {
                            $boletoValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        }
                    }
                }
                array_push($creditCardData, substr(intval($creditCardValue), 0, -2));
                array_push($boletoData, substr(intval($boletoValue), 0, -2));
            }

            return [
                'label_list'       => $labelList,
                'credit_card_data' => $creditCardData,
                'boleto_data'      => $boletoData,
                'currency'         => $currency,
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados');
            report($e);
        }
    }

    /**
     * @param $date
     * @param $projectId
     * @param $currency
     * @param $diffInDays
     * @return array
     */
    private function getByFortyDays($date, $projectId, $currency)
    {
        try {
            $companyModel = new Company();
            $saleModel    = new Sale();

            $labelList    = [];
            $dataFormated = Carbon::parse($date['startDate'])->addDays(2);
            $endDate      = Carbon::parse($date['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays(3);
                if ($dataFormated->diffInDays($endDate) < 3) {
                    array_push($labelList, $dataFormated->format('d/m'));
                    $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                    array_push($labelList, $dataFormated->format('d/m'));
                    break;
                }
            }

            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . '+ 1 day'));
            $userCompanies   = $companyModel->where('user_id', auth()->user()->account_owner_id)->pluck('id')
                                            ->toArray();

            $orders = $saleModel
                ->select(\DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
                ->leftJoin('transactions as transaction', function($join) use ($userCompanies) {
                    $join->on('transaction.sale_id', '=', 'sales.id');
                    $join->whereIn('transaction.company_id', $userCompanies);
                })
                ->where('sales.owner_id', auth()->user()->account_owner_id)
                ->where('sales.project_id', $projectId)
                ->whereBetween('start_date', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date', 'sales.payment_method')
                ->get()->toArray();

            $creditCardData = [];
            $boletoData     = [];
            foreach ($labelList as $label) {
                $creditCardValue = 0;
                $boletoValue     = 0;

                foreach ($orders as $order) {
                    for ($x = 1; $x <= 3; $x++) {
                        if ((Carbon::parse($order['date'])->addDays($x)->format('d/m') == $label)) {

                            if ($order['payment_method'] == '1') {
                                $creditCardValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            } else {
                                $boletoValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            }
                        }
                    }
                }

                array_push($creditCardData, substr(intval($creditCardValue), 0, -2));
                array_push($boletoData, substr(intval($boletoValue), 0, -2));
            }

            return [
                'label_list'       => $labelList,
                'credit_card_data' => $creditCardData,
                'boleto_data'      => $boletoData,
                'currency'         => $currency,
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados ReportsController - getByFortyDays');
            report($e);
        }
    }

    /**
     * @param $date
     * @param $projectId
     * @param $currency
     * @param $diffInDays
     * @return array
     */
    private function getByWeek($date, $projectId, $currency)
    {
        try {
            $saleModel    = new Sale();
            $companyModel = new Company();

            $labelList    = [];
            $dataFormated = Carbon::parse($date['startDate'])->addDays(6);
            $endDate      = Carbon::parse($date['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays(7);
                if ($dataFormated->diffInDays($endDate) < 7) {
                    array_push($labelList, $dataFormated->format('d/m'));
                    $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                    array_push($labelList, $dataFormated->format('d/m'));
                    break;
                }
            }
            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'));

            $userCompanies = $companyModel->where('user_id', auth()->user()->account_owner_id)->pluck('id')->toArray();

            $orders = $saleModel
                ->select(\DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
                ->leftJoin('transactions as transaction', function($join) use ($userCompanies) {
                    $join->on('transaction.sale_id', '=', 'sales.id');
                    $join->whereIn('transaction.company_id', $userCompanies);
                })
                ->where('sales.owner_id', auth()->user()->account_owner_id)
                ->where('sales.project_id', $projectId)
                ->whereBetween('start_date', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date', 'sales.payment_method')
                ->get()->toArray();

            $creditCardData = [];
            $boletoData     = [];
            foreach ($labelList as $label) {
                $creditCardValue = 0;
                $boletoValue     = 0;
                foreach ($orders as $order) {
                    for ($x = 1; $x <= 6; $x++) {
                        if ((Carbon::parse($order['date'])->addDays($x)->format('d/m') == $label)) {

                            if ($order['payment_method'] == 1) {
                                $creditCardValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            } else {
                                $boletoValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            }
                        }
                    }
                }
                array_push($creditCardData, substr(intval($creditCardValue), 0, -2));
                array_push($boletoData, substr(intval($boletoValue), 0, -2));
            }

            return [
                'label_list'       => $labelList,
                'credit_card_data' => $creditCardData,
                'boleto_data'      => $boletoData,
                'currency'         => $currency,
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados');
            report($e);
        }
    }

    /**
     * @param $date
     * @param $projectId
     * @param $currency
     * @param $diffInDays
     * @return array
     */
    private function getByMonth($date, $projectId, $currency)
    {
        try {
            $companyModel = new Company();
            $saleModel    = new Sale();

            $labelList    = [];
            $dataFormated = Carbon::parse($date['startDate']);
            $endDate      = Carbon::parse($date['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('m/y'));
                $dataFormated = $dataFormated->addMonths(1);
            }

            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'));

            $userCompanies = $companyModel->where('user_id', auth()->user()->account_owner_id)->pluck('id')->toArray();

            $orders = $saleModel
                ->select(\DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
                ->leftJoin('transactions as transaction', function($join) use ($userCompanies) {
                    $join->on('transaction.sale_id', '=', 'sales.id');
                    $join->whereIn('transaction.company_id', $userCompanies);
                })
                ->where('sales.owner_id', auth()->user()->account_owner_id)
                ->where('sales.project_id', $projectId)
                ->whereBetween('start_date', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date', 'sales.payment_method')
                ->get()->toArray();

            $creditCardData = [];
            $boletoData     = [];
            foreach ($labelList as $label) {
                $creditCardValue = 0;
                $boletoValue     = 0;
                foreach ($orders as $order) {
                    if (Carbon::parse($order['date'])->format('m/y') == $label) {

                        if ($order['payment_method'] == 1) {
                            $creditCardValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            $creditCardValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        } else {
                            $boletoValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        }
                    }
                }
                array_push($creditCardData, substr(intval($creditCardValue), 0, -2));
                array_push($boletoData, substr(intval($boletoValue), 0, -2));
            }

            return [
                'label_list'       => $labelList,
                'credit_card_data' => $creditCardData,
                'boleto_data'      => $boletoData,
                'currency'         => $currency,
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados');
            report($e);
        }
    }

    /**
     * @param $date
     * @param $projectId
     * @return array|null
     */
    public function getChartDataCheckouts($date, $projectId)
    {
        if ($date['startDate'] == $date['endDate']) { 
            return $this->getCheckoutsByHours($date, $projectId);
        } else if ($date['startDate'] != $date['endDate']) {
            $data       = null;
            $startDate  = Carbon::createFromFormat('Y-m-d', $date['startDate'], 'America/Sao_Paulo');
            $endDate    = Carbon::createFromFormat('Y-m-d', $date['endDate'], 'America/Sao_Paulo');
            $diffInDays = $endDate->diffInDays($startDate);
            if ($projectId) {
                if ($diffInDays <= 20) {
                    return $this->getCheckoutsByDays($date, $projectId);
                } else if ($diffInDays > 20 && $diffInDays <= 40) {
                    return $this->getCheckoutsByTwentyDays($date, $projectId);
                } else if ($diffInDays > 40 && $diffInDays <= 60) {
                    return $this->getCheckoutsByFortyDays($date, $projectId);
                } else if ($diffInDays > 60 && $diffInDays <= 140) {
                    return $this->getCheckoutsByWeek($date, $projectId);
                } else if ($diffInDays > 140) {
                    return $this->getCheckoutsByMonth($date, $projectId);
                }
            } else {

                return [
                    'label_list'    => ['', ''],
                    'checkout_data' => [0, 0],
                ];
            }
        }
    }

     /**
     * @param $date
     * @param $projectId
     * @return array
     */
    private function getCheckoutsByWeek($date, $projectId)
    {
        try {
            $checkoutModel = new Checkout();

            $labelList    = [];
            $dataFormated = Carbon::parse($date['startDate'])->addDays(6);
            $endDate      = Carbon::parse($date['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays(7);
                if ($dataFormated->diffInDays($endDate) < 7) {
                    array_push($labelList, $dataFormated->format('d/m'));
                    $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                    array_push($labelList, $dataFormated->format('d/m'));
                    break;
                }
            }
            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'));

            $orders = $checkoutModel
                ->select(\DB::raw('count(*) as count, DATE(created_at) as date'))
                ->where('project_id', $projectId)
                ->whereBetween('created_at', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date')
                ->get()->toArray();

            $checkoutData = [];
            foreach ($labelList as $label) {
                $checkoutValue = 0;
                foreach ($orders as $order) {
                    for ($x = 1; $x <= 6; $x++) {
                        if ((Carbon::parse($order['date'])->addDays($x)->format('d/m') == $label)) {
                            $checkoutValue += $order['count'];
                        }
                    }
                }
                array_push($checkoutData, $checkoutValue);
            }

            return [
                'label_list'    => $labelList,
                'checkout_data' => $checkoutData,
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados');
            report($e);
        }
    }

    /**
     * @param $date
     * @param $projectId
     * @return array
     */
    private function getCheckoutsByMonth($date, $projectId)
    {
        try {

            $checkoutModel = new Checkout();

            $labelList    = [];
            $dataFormated = Carbon::parse($date['startDate']);
            $endDate      = Carbon::parse($date['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('m/y'));
                $dataFormated = $dataFormated->addMonths(1);
            }

            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'));

            $orders = $checkoutModel
                ->select(\DB::raw('count(*) as count, DATE(created_at) as date'))
                ->where('project_id', $projectId)
                ->whereBetween('created_at', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date')
                ->get()->toArray();
                // dd($orders);

            $checkoutData = [];
            foreach ($labelList as $label) {
                $checkoutValue = 0;
                foreach ($orders as $order) {
                    if (Carbon::parse($order['date'])->format('m/y') == $label) {
                        $checkoutValue += $order['count'];
                    }
                }
                array_push($checkoutData, $checkoutValue);
            }

            return [
                'label_list'    => $labelList,
                'checkout_data' => $checkoutData,
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados');
            report($e);
        }
    }

     /**
     * @param $date
     * @param $projectId
     * @return array
     */
    private function getCheckoutsByFortyDays($date, $projectId)
    {
        try {

            $checkoutModel = new Checkout();

            $labelList    = [];
            $dataFormated = Carbon::parse($date['startDate'])->addDays(2);
            $endDate      = Carbon::parse($date['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays(3);
                if ($dataFormated->diffInDays($endDate) < 3) {
                    array_push($labelList, $dataFormated->format('d/m'));
                    $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                    array_push($labelList, $dataFormated->format('d/m'));
                    break;
                }
            }

            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . '+ 1 day'));

            $orders = $checkoutModel
                ->select(\DB::raw('count(*) as count, DATE(created_at) as date'))
                ->where('project_id', $projectId)
                ->whereBetween('created_at', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date')
                ->get()->toArray();

            $checkoutData = [];
            foreach ($labelList as $label) {
                $checkoutValue = 0;

                foreach ($orders as $order) {
                    for ($x = 1; $x <= 3; $x++) {
                        if ((Carbon::parse($order['date'])->addDays($x)->format('d/m') == $label)) {
                            $checkoutValue += $order['count'];
                        }
                    }
                }
                array_push($checkoutData, $checkoutValue);
            }

            return [
                'label_list'    => $labelList,
                'checkout_data' => $checkoutData,
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados ReportsController - getCheckoutsByFortyDays');
            report($e);
        }
    }

     /**
     * @param $date
     * @param $projectId
     * @return array
     */
    private function getCheckoutsByTwentyDays($date, $projectId)
    {
        try {

            $checkoutModel = new Checkout();

            $labelList    = [];
            $dataFormated = Carbon::parse($date['startDate'])->addDays(1);
            $endDate      = Carbon::parse($date['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays(2);
                if ($dataFormated->diffInDays($endDate) < 2 && $dataFormated->diffInDays($endDate) > 0) {
                    array_push($labelList, $dataFormated->format('d/m'));
                    $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                    array_push($labelList, $dataFormated->format('d/m'));
                    break;
                }
            }
            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'));

            $orders = $checkoutModel
                ->select(\DB::raw('count(*) as count, DATE(created_at) as date'))
                ->where('project_id', $projectId)
                ->whereBetween('created_at', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date')
                ->get()->toArray();

            $checkoutData = [];
            foreach ($labelList as $label) {
                $checkoutValue = 0;
                foreach ($orders as $order) {
                    if ((Carbon::parse($order['date'])->subDays(1)->format('d/m') == $label) || (Carbon::parse($order['date'])->format('d/m') == $label)) {
                        $checkoutValue += $order['count'];
                    }
                }
                array_push($checkoutData, $checkoutValue);
            }

            return [
                'label_list'    => $labelList,
                'checkout_data' => $checkoutData,
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados');
            report($e);
        }
    }

     /**
     * @param $data
     * @param $projectId
     * @return array
     */
    private function getCheckoutsByDays($data, $projectId)
    {
        try {
            $checkoutModel = new Checkout();

            $labelList    = [];
            $dataFormated = Carbon::parse($data['startDate']);
            $endDate      = Carbon::parse($data['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d-m'));
                $dataFormated = $dataFormated->addDays(1);
            }

            $data['endDate'] = date('Y-m-d', strtotime($data['endDate'] . ' + 1 day'));

            $orders = $checkoutModel
                ->select(\DB::raw('count(*) as count, DATE(created_at) as date'))
                ->where('project_id', $projectId)
                ->whereBetween('created_at', [$data['startDate'], date('Y-m-d', strtotime($data['endDate'] . ' + 1 day'))])
                ->groupBy('date')
                ->get()->toArray();

            $checkoutData = [];

            foreach ($labelList as $label) {
                $checkoutValue = 0;
                foreach ($orders as $order) {
                    if (Carbon::parse($order['date'])->format('d-m') == $label) {
                        $checkoutValue = $order['count'];
                    }
                }
                array_push($checkoutData, $checkoutValue);
            }

            return [
                'label_list'    => $labelList,
                'checkout_data' => $checkoutData,
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados');
            report($e);
        }
    }

     /**
     * @param $data
     * @param $projectId
     * @return array
     */
    private function getCheckoutsByHours($data, $projectId)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $checkoutModel = new Checkout();

        if (Carbon::parse($data['startDate'])->format('m/d/y') == Carbon::now()->format('m/d/y')) {

            $labelList   = [];
            $currentHour = date('H');
            $startHour   = 0;
            while ($startHour <= $currentHour) {
                array_push($labelList, $startHour . 'h');
                $startHour++;
            }
        } else {
            $labelList = [
                '0h', '1h', '2h', '3h', '4h', '5h', '6h', '7h', '8h', '9h', '10h', '11h',
                '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h', '21h', '22h', '23h',
            ];
        }

        $orders = $checkoutModel
            ->select(\DB::raw('count(*) as count, HOUR(created_at) as hour'))
            ->where('project_id', $projectId)
            ->whereDate('created_at', $data['startDate'])
            ->groupBy('hour')
            ->get()->toArray();

        $checkoutData = [];
        foreach ($labelList as $label) {
            $checkoutValue = 0;
            foreach ($orders as $order) {
                if ($order['hour'] == preg_replace("/[^0-9]/", "", $label)) {
                    $checkoutValue = $order['count'];
                }
            }
            array_push($checkoutData, $checkoutValue);
        }

        return [
            'label_list'    => $labelList,
            'checkout_data' => $checkoutData,
        ];
    }

    /**
     * @param $companyId
     * @param $currency
     * @return array
     */
    public function getFinacialProjectionByDays($companyId, $currency)
    {
        try {
            $transactionModel = new Transaction();
            $labelList    = [];
            $dataFormated = Carbon::today()->addDay();
            $endDate      = Carbon::today()->addDays(20);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d-m'));
                $dataFormated = $dataFormated->addDays(1);
            }

            $transactions = $transactionModel
                ->select(\DB::raw('SUM(value) as value, DATE(release_date) as date'))
                ->where('company_id', $companyId)
                ->whereIn('type', collect([2,3,4,5]))
                ->where('status', 'paid')
                ->whereBetween('release_date', [Carbon::now()->addDay()->format('Y-m-d'), Carbon::now()->addDays(20)->format('Y-m-d')])
                ->groupBy('date')
                ->get()->toArray();

            $transactionData = [];

            foreach ($labelList as $label) {
                $transactionValue = 0;
                foreach ($transactions as $transaction) {
                    if (Carbon::parse($transaction['date'])->format('d-m') == $label) {
                        $transactionValue = $transaction['value'];
                    }
                }
                array_push($transactionData, $transactionValue);
            }

            return [
                'label_list'       => $labelList,
                'transaction_data' => $transactionData,
                'currency'         => $currency
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados');
            report($e);
        }
    }

}

