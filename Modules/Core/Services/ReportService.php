<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use http\Env\Response;
use Illuminate\Http\Request;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Sale;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\Checkout;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\Cashback;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\DiscountCoupon;
use Modules\Core\Entities\Gateway;
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
        } elseif ($date['startDate'] != $date['endDate']) {
            $data       = null;
            $startDate  = Carbon::createFromFormat('Y-m-d', $date['startDate'], 'America/Sao_Paulo');
            $endDate    = Carbon::createFromFormat('Y-m-d', $date['endDate'], 'America/Sao_Paulo');
            $diffInDays = $endDate->diffInDays($startDate);
            if ($projectId) {
                if ($diffInDays <= 20) {
                    return $this->getByDays($date, $projectId, $currency);
                } elseif ($diffInDays > 20 && $diffInDays <= 40) {
                    return $this->getByTwentyDays($date, $projectId, $currency);
                } elseif ($diffInDays > 40 && $diffInDays <= 60) {
                    return $this->getByFortyDays($date, $projectId, $currency);
                } elseif ($diffInDays > 60 && $diffInDays <= 140) {
                    return $this->getByWeek($date, $projectId, $currency);
                } elseif ($diffInDays > 140) {
                    return $this->getByMonth($date, $projectId, $currency);
                }
            } else {
                return [
                    'label_list'       => ['', ''],
                    'credit_card_data' => [0, 0],
                    'boleto_data'      => [0, 0],
                    'pix_data'         => [0, 0],
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
            ->leftJoin('transactions as transaction', function ($join) use ($userCompanies) {
                $join->on('transaction.sale_id', '=', 'sales.id');
                $join->whereIn('transaction.company_id', $userCompanies);
            })
            ->where('sales.owner_id', auth()->user()->account_owner_id)
            ->where('sales.project_id', $projectId)
            ->whereDate('sales.start_date', $data['startDate'])
            ->groupBy('hour', 'sales.payment_method')
            ->get()->toArray();

        $creditCardData = [];
        $boletoData     = [];
        $pixData     = [];

        foreach ($labelList as $label) {
            $creditCardValue = 0;
            $boletoValue     = 0;
            $pixValue     = 0;

            foreach ($orders as $order) {
                if ($order['hour'] == preg_replace("/[^0-9]/", "", $label)) {
                    if ($order['payment_method'] == Sale::CREDIT_CARD_PAYMENT) {
                        $creditCardValue = substr(intval($order['value']), 0, -2);
                    } elseif ($order['payment_method'] == Sale::BOLETO_PAYMENT) {
                        $boletoValue = substr(intval($order['value']), 0, -2);
                    } else { // PIX
                        $pixValue = substr(intval($order['value']), 0, -2);
                    }
                }
            }

            array_push($creditCardData, $creditCardValue);
            array_push($boletoData, $boletoValue);
            array_push($pixData, $pixValue);
        }

        return [
            'label_list'       => $labelList,
            'credit_card_data' => $creditCardData,
            'boleto_data'      => $boletoData,
            'pix_data'         => $pixData,
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
            $companyModel   = new Company();
            $saleModel      = new Sale();
            $affiliateModel = new Affiliate();

            $labelList    = [];
            $dataFormated = Carbon::parse($data['startDate']);
            $endDate      = Carbon::parse($data['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d-m'));
                $dataFormated = $dataFormated->addDays(1);
            }

            $userId          = auth()->user()->account_owner_id;
            $data['endDate'] = date('Y-m-d', strtotime($data['endDate'] . ' + 1 day'));

            $userCompanies = $companyModel->where('user_id', $userId)->pluck('id')->toArray();

            $affiliate = $affiliateModel->where([
                                                    ['user_id', $userId],
                                                    ['project_id', $projectId],
                                                ])->first();

            $orders = $saleModel
                ->select(\DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
                ->leftJoin('transactions as transaction', function ($join) use ($userCompanies) {
                    $join->on('transaction.sale_id', '=', 'sales.id');
                    $join->whereIn('transaction.company_id', $userCompanies);
                })
                ->where('sales.project_id', $projectId)
                ->whereBetween('start_date', [$data['startDate'], date('Y-m-d', strtotime($data['endDate'] . ' + 1 day'))])
                ->groupBy('date', 'sales.payment_method');

            if (!empty($affiliate)) {
                $orders->where('sales.affiliate_id', $affiliate->id);
            } else {
                $orders->where('sales.owner_id', $userId);
            }
            $orders         = $orders->get()->toArray();
            $creditCardData = [];
            $boletoData     = [];
            $pixData     = [];

            foreach ($labelList as $label) {
                $creditCardValue = 0;
                $boletoValue     = 0;
                $pixValue     = 0;
                foreach ($orders as $order) {
                    if (Carbon::parse($order['date'])->format('d-m') == $label) {
                        if ($order['payment_method'] == Sale::CREDIT_CARD_PAYMENT) {
                            $creditCardValue = substr(intval($order['value']), 0, -2);
                        } elseif ($order['payment_method'] == Sale::BOLETO_PAYMENT) {
                            $boletoValue = substr(intval($order['value']), 0, -2);
                        } else { // PIX
                            $pixValue = substr(intval($order['value']), 0, -2);
                        }
                    }
                }

                array_push($creditCardData, $creditCardValue);
                array_push($boletoData, $boletoValue);
                array_push($pixData, $pixValue);
            }

            return [
                'label_list'       => $labelList,
                'credit_card_data' => $creditCardData,
                'boleto_data'      => $boletoData,
                'pix_data'         => $pixData,
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
            $companyModel   = new Company();
            $saleModel      = new Sale();
            $affiliateModel = new Affiliate();

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
            $userId = auth()->user()->account_owner_id;

            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'));

            $userCompanies = $companyModel->where('user_id', $userId)->pluck('id')->toArray();

            $affiliate = $affiliateModel->where([
                                                    ['user_id', $userId],
                                                    ['project_id', $projectId],
                                                ])->first();

            $orders = $saleModel
                ->select(\DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
                ->leftJoin('transactions as transaction', function ($join) use ($userCompanies) {
                    $join->on('transaction.sale_id', '=', 'sales.id');
                    $join->whereIn('transaction.company_id', $userCompanies);
                })
                ->where('sales.project_id', $projectId)
                ->whereBetween('start_date', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date', 'sales.payment_method');

            if (!empty($affiliate)) {
                $orders->where('sales.affiliate_id', $affiliate->id);
            } else {
                $orders->where('sales.owner_id', $userId);
            }
            $orders         = $orders->get()->toArray();
            $creditCardData = [];
            $boletoData     = [];
            $pixData     = [];
            foreach ($labelList as $label) {
                $creditCardValue = 0;
                $boletoValue     = 0;
                $pixValue     = 0;

                foreach ($orders as $order) {
                    if (
                        (Carbon::parse($order['date'])
                               ->subDays(1)->format('d/m') == $label) || (Carbon::parse($order['date'])
                                                                                ->format('d/m') == $label)
                    ) {
                        if ($order['payment_method'] == Sale::CREDIT_CARD_PAYMENT) {
                            $creditCardValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        } elseif ($order['payment_method'] == Sale::BOLETO_PAYMENT) {
                            $boletoValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        } else { // PIX
                            $pixValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        }
                    }
                }

                array_push($creditCardData, substr($creditCardValue, 0, -2));
                array_push($boletoData, substr($boletoValue, 0, -2));
                array_push($pixData, substr($pixValue, 0, -2));
            }

            return [
                'label_list'       => $labelList,
                'credit_card_data' => $creditCardData,
                'boleto_data'      => $boletoData,
                'pix_data'         => $pixData,
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
            $companyModel   = new Company();
            $saleModel      = new Sale();
            $affiliateModel = new Affiliate();

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
            $userId          = auth()->user()->account_owner_id;
            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . '+ 1 day'));
            $userCompanies   = $companyModel->where('user_id', $userId)->pluck('id')
                                            ->toArray();

            $affiliate = $affiliateModel->where([
                                                    ['user_id', $userId],
                                                    ['project_id', $projectId],
                                                ])->first();

            $orders = $saleModel
                ->select(\DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
                ->leftJoin('transactions as transaction', function ($join) use ($userCompanies) {
                    $join->on('transaction.sale_id', '=', 'sales.id');
                    $join->whereIn('transaction.company_id', $userCompanies);
                })
                ->where('sales.project_id', $projectId)
                ->whereBetween('start_date', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date', 'sales.payment_method');
            if (!empty($affiliate)) {
                $orders->where('sales.affiliate_id', $affiliate->id);
            } else {
                $orders->where('sales.owner_id', $userId);
            }
            $orders         = $orders->get()->toArray();
            $creditCardData = [];
            $boletoData     = [];
            $pixData     = [];

            foreach ($labelList as $label) {
                $creditCardValue = 0;
                $boletoValue     = 0;
                $pixValue     = 0;

                foreach ($orders as $order) {
                    for ($x = 1; $x <= 3; $x++) {
                        if ((Carbon::parse($order['date'])->addDays($x)->format('d/m') == $label)) {
                            if ($order['payment_method'] == Sale::CREDIT_CARD_PAYMENT) {
                                $creditCardValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            } elseif ($order['payment_method'] == Sale::BOLETO_PAYMENT) {
                                $boletoValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            } else { // PIX
                                $pixValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            }
                        }
                    }
                }

                array_push($creditCardData, substr($creditCardValue, 0, -2));
                array_push($boletoData, substr($boletoValue, 0, -2));
                array_push($pixData, substr($pixValue, 0, -2));
            }

            return [
                'label_list'       => $labelList,
                'credit_card_data' => $creditCardData,
                'boleto_data'      => $boletoData,
                'pix_data'         => $pixData,
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
            $saleModel      = new Sale();
            $companyModel   = new Company();
            $affiliateModel = new Affiliate();

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
            $userId          = auth()->user()->account_owner_id;
            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'));

            $userCompanies = $companyModel->where('user_id', $userId)->pluck('id')->toArray();

            $affiliate = $affiliateModel->where([
                                                    ['user_id', $userId],
                                                    ['project_id', $projectId],
                                                ])->first();

            $orders = $saleModel
                ->select(\DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
                ->leftJoin('transactions as transaction', function ($join) use ($userCompanies) {
                    $join->on('transaction.sale_id', '=', 'sales.id');
                    $join->whereIn('transaction.company_id', $userCompanies);
                })
                //                ->where('sales.owner_id', $userId)
                ->where('sales.project_id', $projectId)
                ->whereBetween('start_date', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date', 'sales.payment_method');
            if (!empty($affiliate)) {
                $orders->where('sales.affiliate_id', $affiliate->id);
            } else {
                $orders->where('sales.owner_id', $userId);
            }
            $orders         = $orders->get()->toArray();
            $creditCardData = [];
            $boletoData     = [];
            $pixData     = [];

            foreach ($labelList as $label) {
                $creditCardValue = 0;
                $boletoValue     = 0;
                $pixValue     = 0;

                foreach ($orders as $order) {
                    for ($x = 1; $x <= 6; $x++) {
                        if ((Carbon::parse($order['date'])->addDays($x)->format('d/m') == $label)) {
                            if ($order['payment_method'] == Sale::CREDIT_CARD_PAYMENT) {
                                $creditCardValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            } elseif ($order['payment_method'] == Sale::BOLETO_PAYMENT) {
                                $boletoValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            } else { //PIX
                                $pixValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            }
                        }
                    }
                }
                array_push($creditCardData, substr($creditCardValue, 0, -2));
                array_push($boletoData, substr($boletoValue, 0, -2));
                array_push($pixData, substr($pixValue, 0, -2));
            }

            return [
                'label_list'       => $labelList,
                'credit_card_data' => $creditCardData,
                'boleto_data'      => $boletoData,
                'pix_data'         => $pixData,
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
    private function getByMonth($date, $projectId, $currency) //
    {
        try {
            $companyModel   = new Company();
            $saleModel      = new Sale();
            $affiliateModel = new Affiliate();

            $labelList    = [];
            $dataFormated = Carbon::parse($date['startDate']);
            $endDate      = Carbon::parse($date['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('m/y'));
                $dataFormated = $dataFormated->addMonths(1);
            }

            $userId          = auth()->user()->account_owner_id;
            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'));

            $userCompanies = $companyModel->where('user_id', $userId)->pluck('id')->toArray();

            $affiliate = $affiliateModel->where([
                                                    ['user_id', $userId],
                                                    ['project_id', $projectId],
                                                ])->first();

            $orders = $saleModel
                ->select(\DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
                ->leftJoin('transactions as transaction', function ($join) use ($userCompanies) {
                    $join->on('transaction.sale_id', '=', 'sales.id');
                    $join->whereIn('transaction.company_id', $userCompanies);
                })
                //                ->where('sales.owner_id', $userId)
                ->where('sales.project_id', $projectId)
                ->whereBetween('start_date', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date', 'sales.payment_method');

            if (!empty($affiliate)) {
                $orders->where('sales.affiliate_id', $affiliate->id);
            } else {
                $orders->where('sales.owner_id', $userId);
            }
            $orders = $orders->get()->toArray();

            $creditCardData = [];
            $boletoData     = [];
            $pixData     = [];

            foreach ($labelList as $label) {
                $creditCardValue = 0;
                $boletoValue     = 0;
                $pixValue     = 0;

                foreach ($orders as $order) {
                    if (Carbon::parse($order['date'])->format('m/y') == $label) {
                        if ($order['payment_method'] == Sale::CREDIT_CARD_PAYMENT) {
                            $creditCardValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        } elseif ($order['payment_method'] == Sale::BOLETO_PAYMENT) {
                            $boletoValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        } else { // PIX
                            $pixValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        }
                    }
                }
                array_push($creditCardData, substr($creditCardValue, 0, -2));
                array_push($boletoData, substr($boletoValue, 0, -2));
                array_push($pixData, substr($pixValue, 0, -2));
            }

            return [
                'label_list'       => $labelList,
                'credit_card_data' => $creditCardData,
                'boleto_data'      => $boletoData,
                'pix_data'         => $pixData,
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
        } elseif ($date['startDate'] != $date['endDate']) {
            $data       = null;
            $startDate  = Carbon::createFromFormat('Y-m-d', $date['startDate'], 'America/Sao_Paulo');
            $endDate    = Carbon::createFromFormat('Y-m-d', $date['endDate'], 'America/Sao_Paulo');
            $diffInDays = $endDate->diffInDays($startDate);
            if ($projectId) {
                if ($diffInDays <= 20) {
                    return $this->getCheckoutsByDays($date, $projectId);
                } elseif ($diffInDays > 20 && $diffInDays <= 40) {
                    return $this->getCheckoutsByTwentyDays($date, $projectId);
                } elseif ($diffInDays > 40 && $diffInDays <= 60) {
                    return $this->getCheckoutsByFortyDays($date, $projectId);
                } elseif ($diffInDays > 60 && $diffInDays <= 140) {
                    return $this->getCheckoutsByWeek($date, $projectId);
                } elseif ($diffInDays > 140) {
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
            $affiliateModel = new Affiliate();

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
            $userId = auth()->user()->account_owner_id;
            $affiliate = $affiliateModel->where([
                                                    ['user_id', $userId],
                                                    ['project_id', $projectId],
                                                ])->first();

            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'));

            $orders = $checkoutModel
                ->select(\DB::raw('count(*) as count, DATE(created_at) as date'))
                ->where('project_id', $projectId)
                ->whereBetween('created_at', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date');

            if (!empty($affiliate)) {
                $orders->where('affiliate_id', $affiliate->id);
            }

            $orders = $orders->get()->toArray();

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
            $affiliateModel = new Affiliate();

            $labelList    = [];
            $dataFormated = Carbon::parse($date['startDate']);
            $endDate      = Carbon::parse($date['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('m/y'));
                $dataFormated = $dataFormated->addMonths(1);
            }

            $userId = auth()->user()->account_owner_id;
            $affiliate = $affiliateModel->where([
                                                    ['user_id', $userId],
                                                    ['project_id', $projectId],
                                                ])->first();

            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'));

            $orders = $checkoutModel
                ->select(\DB::raw('count(*) as count, DATE(created_at) as date'))
                ->where('project_id', $projectId)
                ->whereBetween('created_at', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date');

            if (!empty($affiliate)) {
                $orders->where('affiliate_id', $affiliate->id);
            }

            $orders = $orders->get()->toArray();

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
            $affiliateModel = new Affiliate();

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

            $userId = auth()->user()->account_owner_id;
            $affiliate = $affiliateModel->where([
                                                    ['user_id', $userId],
                                                    ['project_id', $projectId],
                                                ])->first();

            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . '+ 1 day'));

            $orders = $checkoutModel
                ->select(\DB::raw('count(*) as count, DATE(created_at) as date'))
                ->where('project_id', $projectId)
                ->whereBetween('created_at', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date');

            if (!empty($affiliate)) {
                $orders->where('affiliate_id', $affiliate->id);
            }

            $orders = $orders->get()->toArray();

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
            $affiliateModel = new Affiliate();

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
            $userId = auth()->user()->account_owner_id;
            $affiliate = $affiliateModel->where([
                                                    ['user_id', $userId],
                                                    ['project_id', $projectId],
                                                ])->first();
            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'));

            $orders = $checkoutModel
                ->select(\DB::raw('count(*) as count, DATE(created_at) as date'))
                ->where('project_id', $projectId)
                ->whereBetween('created_at', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                ->groupBy('date');

            if (!empty($affiliate)) {
                $orders->where('affiliate_id', $affiliate->id);
            }

            $orders = $orders->get()->toArray();

            $checkoutData = [];
            foreach ($labelList as $label) {
                $checkoutValue = 0;
                foreach ($orders as $order) {
                    if (
                        (Carbon::parse($order['date'])->subDay()
                               ->format('d/m') == $label) || (Carbon::parse($order['date'])->format('d/m') == $label)
                    ) {
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
            $affiliateModel = new Affiliate();

            $labelList    = [];
            $dataFormated = Carbon::parse($data['startDate']);
            $endDate      = Carbon::parse($data['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d-m'));
                $dataFormated = $dataFormated->addDays(1);
            }

            $data['endDate'] = date('Y-m-d', strtotime($data['endDate'] . ' + 1 day'));

            $userId = auth()->user()->account_owner_id;
            $affiliate = $affiliateModel->where([
                                                    ['user_id', $userId],
                                                    ['project_id', $projectId],
                                                ])->first();

            $orders = $checkoutModel
                ->select(\DB::raw('count(*) as count, DATE(created_at) as date'))
                ->where('project_id', $projectId)
                ->whereBetween('created_at', [$data['startDate'], date('Y-m-d', strtotime($data['endDate'] . ' + 1 day'))])
                ->groupBy('date');

            if (!empty($affiliate)) {
                $orders->where('affiliate_id', $affiliate->id);
            }
            $orders = $orders->get()->toArray();

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

        $checkoutModel  = new Checkout();
        $affiliateModel = new Affiliate();

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
        $userId = auth()->user()->account_owner_id;
        $affiliate = $affiliateModel->where([
                                                ['user_id', $userId],
                                                ['project_id', $projectId],
                                            ])->first();

        $orders = $checkoutModel
            ->select(\DB::raw('count(*) as count, HOUR(created_at) as hour'))
            ->where('project_id', $projectId)
            ->whereDate('created_at', $data['startDate'])
            ->groupBy('hour');

        if (!empty($affiliate)) {
            $orders->where('affiliate_id', $affiliate->id);
        }

        $orders = $orders->get()->toArray();

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

    public function getFinacialProjectionByDays($companyId, $currency): array
    {
        try {
            $transactionModel = new Transaction();
            $labelList        = [];
            $dataFormated     = Carbon::today()->addDay();
            $endDate          = Carbon::today()->addDays(20);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d-m'));
                $dataFormated = $dataFormated->addDays(1);
            }

            $transactions = $transactionModel
                ->select(\DB::raw('(SUM(transactions.value) - SUM(CASE WHEN transactions.status_enum = 12 THEN anticipated_transactions.value ELSE 0 END)) as value, DATE(release_date) as date'))
                ->leftJoin('anticipated_transactions', 'transactions.id', 'anticipated_transactions.transaction_id')
                ->where('company_id', $companyId)
                ->whereIn('type', collect([2, 3, 4, 5]))
                ->whereIn('status_enum', collect([$transactionModel->present()->getStatusEnum('paid'), $transactionModel->present()->getStatusEnum('anticipated')]))
                ->whereBetween('release_date', [
                    Carbon::now()->addDay()->format('Y-m-d'), Carbon::now()->addDays(20)->format('Y-m-d'),
                ])
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
                'currency'         => $currency,
            ];
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * @param $companyId
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function getDashboardChartData($companyId)
    {
        try {
            $labelList    = [];
            $dataFormated = Carbon::now()->subMonth()->subDays(5);
            $endDate      = Carbon::now();

            while ($endDate->greaterThanOrEqualTo($dataFormated)) {
                array_push($labelList, $endDate->format('d/m'));
                $endDate = $endDate->subDays(5);
                if ($endDate->diffInDays($dataFormated) < 1) {
                    $endDate = $endDate->subDays($endDate->diffInDays($dataFormated));
                    array_push($labelList, $endDate->format('d/m'));
                    break;
                }
            }

            $startDate = Carbon::now()->subMonth()->subDays(5)->format('Y-m-d');
            $endDate   = Carbon::now()->addDay()->format('Y-m-d');

            $orders = Sale::select(\DB::raw('count(*) as count, DATE(sales.end_date) as date, SUM(transaction.value) as value'))
                ->leftJoin('transactions as transaction', function ($join) use ($companyId) {
                    $join->on('transaction.sale_id', '=', 'sales.id');
                    $join->where('transaction.company_id', $companyId);
                })
                ->where('sales.status', Sale::STATUS_APPROVED)
                ->whereBetween('end_date', [$startDate, $endDate])
                ->groupBy('date');

            $orders         = $orders->get()->toArray();
            $labelList      = array_reverse($labelList);
            $valueData      = [];
            foreach ($labelList as $key => $label) {
                $valueData[$key] = 0;

                foreach ($orders as $order) {
                    if (
                        ($label == Carbon::parse($order['date'])->format('d/m')) ||
                        (Carbon::createFromFormat('d/m', $label)->subDay()->format('d/m') == Carbon::parse($order['date'])->format('d/m')) ||
                        (Carbon::createFromFormat('d/m', $label)->subDays(2)->format('d/m') == Carbon::parse($order['date'])->format('d/m')) ||
                        (Carbon::createFromFormat('d/m', $label)->subDays(3)->format('d/m') == Carbon::parse($order['date'])->format('d/m')) ||
                        (Carbon::createFromFormat('d/m', $label)->subDays(4)->format('d/m') == Carbon::parse($order['date'])->format('d/m'))
                    ) {
                        if ($order['value'] >= 100) {
                            $order['value'] = (int) substr($order['value'], 0, -2);
                        }

                        $valueData[$key] += (int) FoxUtils::onlyNumbers($order['value']);
                    }
                }
            }

            return [
                'label_list' => $labelList,
                'value_data' => $valueData,
                'currency'   => 'R$',
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados');
            report($e);

            return
                [
                    'message' => 'Não foi possível verificar todos os valores totais de venda'
                ];
        }
    }

    // FINANCES --------------------------------------------------------------------------------------
    public function getResumeCommissions($filters)
    {
        try {
            return '';

        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumePendings($filters)
    {
        try {
            $relationsArray = [
                'sale',
                'sale.project',
                'sale.customer',
            ];

            $transactionModel = new Transaction();

            $transactions = $transactionModel->with($relationsArray)
            ->where('user_id', auth()->user()->account_owner_id)
            ->join('sales', 'sales.id', 'transactions.sale_id')
            ->where(
                'transactions.status_enum',
                '=',
                $transactionModel->present()->getStatusEnum('paid')
            )
            ->whereNull('invitation_id');

            // Filtro Company
            if (!empty($filters["company"])) {
                $companyId = Hashids::decode($filters["company"]);
                $transactions->where('company_id', $companyId);
            }

            $transactions->whereNull('withdrawal_id');

            // Filtros - INICIO
            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

            // Filtro de Data
            $transactions->whereHas(
                'sale',
                function ($querySale) use ($dateRange) {
                    $querySale->whereBetween('start_date', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);
                }
            )
            ->selectRaw('transactions.*, sales.start_date')
            ->orderByDesc('sales.start_date');

            // Projeto
            if (!empty($filters["project"])) {
                $projectId = Hashids::decode($filters["project"]);
                $transactions->whereHas(
                    'sale',
                    function ($querySale) use ($projectId) {
                        $querySale->where('sales.project_id', $projectId);
                    }
                );
            }

            $resume = $transactions->without(['sale'])
            ->select(DB::raw("sum((sales.sub_total + sales.shipment_value) - (ifnull(sales.shopify_discount, 0) + sales.automatic_discount) / 100) as total"))
            ->first();

            return number_format($resume->total, 2, ',', '.');
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumeCashbacks($filters)
    {
        try {
            $cashbackModel = new Cashback();
            $companyModel = new Company();

            if (empty($filters["company"])) {
                $userCompanies = $companyModel
                ->where('user_id', auth()->user()->account_owner_id)
                ->get()
                ->pluck('id')
                ->toArray();

            } else {
                $userCompanies = [];
                $companies = explode(',', $filters["company"]);

                foreach($companies as $company){
                    array_push($userCompanies, current(Hashids::decode($company)));
                }
            }

            $cashbacks = $cashbackModel
            ->join('sales', 'sales.id', 'cashbacks.sale_id')
            ->whereIn('company_id', [3469]);

            if (!empty($filters["project"])) {
                $projectIds =[];
                $projects = explode(',', $filters["project"]);

                foreach($projects as $project){
                    array_push($projectIds, current(Hashids::decode($project)));
                }

                $cashbacks->whereHas(
                    'sale',
                    function ($querySale) use ($projectIds) {
                        $querySale->whereIn('project_id', $projectIds);
                    }
                );
            }

            // periodo obrigatorio
            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

            $cashbacks
            ->whereHas(
                'sale',
                function ($querySale) use ($dateRange) {
                    $querySale->whereBetween('start_date', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);
                }
            )
            ->selectRaw('cashbacks.*, sales.start_date')
            ->orderByDesc('sales.start_date');

            $resume = $cashbacks->select(DB::raw("sum(cashbacks.value) / 100 as cashback"))->first();

            return number_format($resume->cashback, 2, ',', '.');

        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // SALES --------------------------------------------------------------------------------------
    public function getResumeSales($filters)
    {
       try {
            $userId = auth()->user()->account_owner_id;
            $statusId = Sale::STATUS_APPROVED;

            $projectId = '';
            if (!empty($filters["project"])) {
                $projectId = 'AND project_id = ' . Hashids::decode($filters["project"]);
            }

            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

            $query = 'SELECT COUNT(*) as sales FROM sales WHERE owner_id = '.$userId.' AND status = '.$statusId.' AND (start_date BETWEEN "'.$dateRange[0].' 00:00:00" AND "'.$dateRange[1].' 23:59:59")' .$projectId;

            $dbResults = DB::select($query);

           return $dbResults[0]->sales;
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumeTypePayments($filters)
    {
        try {
            $total = $this->getResumeTypePaymentsSum($filters);

            $totalCreditCard = $this->getResumeTypePaymentsSum($filters, Sale::CREDIT_CARD_PAYMENT);
            $percentageCreditCard = $totalCreditCard > 0 ? number_format(($totalCreditCard * 100) / $total, 2, '.', ',') : 0;

            $totalBoleto = $this->getResumeTypePaymentsSum($filters, Sale::BOLETO_PAYMENT);
            $percentageBoleto = $totalBoleto > 0 ? number_format(($totalBoleto * 100) / $total, 2, '.', ',') : 0;

            $totalPix = $this->getResumeTypePaymentsSum($filters, Sale::PIX_PAYMENT);
            $percentagePix = $totalPix > 0 ? number_format(($totalPix * 100) / $total, 2, '.', ',') : 0;

            return [
                'total' => number_format($total, 2, ',', '.'),
                'credit_card' => [
                    'value' => number_format($totalCreditCard, 2, ',', '.'),
                    'percentage' => round($percentageCreditCard, 1, PHP_ROUND_HALF_UP).'%'
                ],
                'boleto' => [
                    'value' => number_format($totalBoleto, 2, ',', '.'),
                    'percentage' => round($percentageBoleto, 1, PHP_ROUND_HALF_UP).'%'
                ],
                'pix' => [
                    'value' => number_format($totalPix, 2, ',', '.'),
                    'percentage' => round($percentagePix, 1, PHP_ROUND_HALF_UP).'%'
                ]
            ];

        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumeTypePaymentsSum($filters, $typePayment = null)
    {
        $userCompanies = [];
        if (empty($filters["company"])) {
            $userCompanies = Company::where('user_id', auth()->user()->account_owner_id)->get()->pluck('id')->toArray();
        } else {
            $userCompanies = [];

            $companies = explode(',', $filters["company"]);
            foreach($companies as $company) {
                array_push($userCompanies, current(Hashids::decode($company)));
            }
        }

        $typePaymentWhere = '';
        if (!empty($typePayment)) {
            $typePaymentWhere = 'AND sales.payment_method = '.$typePayment;
        } else {
            $typePaymentWhere = '';
        }

        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $query = 'SELECT SUM((sales.sub_total + sales.shipment_value) - (IFNULL(sales.shopify_discount, 0) + sales.automatic_discount) / 100) as total
        FROM transactions INNER JOIN sales ON sales.id = transactions.sale_id
        WHERE transactions.company_id in ('.implode(',', $userCompanies).') AND (sales.start_date BETWEEN "'.$dateRange[0].' 00:00:00" AND "'.$dateRange[1].' 23:59:59")
        AND sales.status = 1 AND sales.deleted_at IS NULL AND transactions.deleted_at IS NULL AND transactions.type <> 8 '.$typePaymentWhere.' AND transactions.invitation_id IS NULL';

        $dbResults = DB::select($query);

        return $dbResults[0]->total;
    }

    public function getResumeProducts()
    {
        try {

        } catch(Exception $e) {

        }
    }

    // MARKETING --------------------------------------------------------------------------------------
    public function getResumeCoupons()
    {
        try {

        } catch(Exception $e) {

        }
    }

    public function getResumeRegions()
    {
        try {

        } catch(Exception $e) {

        }
    }

    public function getResumeOrigins()
    {
        try {

        } catch(Exception $e) {

        }
    }
}
