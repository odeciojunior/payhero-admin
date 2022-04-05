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
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Cashback;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\DiscountCoupon;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Reports\Transformers\SalesByOriginResource;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\CieloService;
use Modules\Core\Services\Gateways\GerencianetService;
use Modules\Core\Services\Gateways\GetnetService;
use Modules\Core\Services\Gateways\Safe2PayService;

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
            ->select(DB::raw('count(*) as count, HOUR(sales.start_date) as hour, SUM(transaction.value) as value, sales.payment_method'))
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
                ->select(DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
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
            $pixData        = [];

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
                ->select(DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
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
                ->select(DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
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
                ->select(DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
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
                ->select(DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
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
                ->select(DB::raw('count(*) as count, DATE(created_at) as date'))
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
                ->select(DB::raw('count(*) as count, DATE(created_at) as date'))
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
                ->select(DB::raw('count(*) as count, DATE(created_at) as date'))
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
                ->select(DB::raw('count(*) as count, DATE(created_at) as date'))
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
                ->select(DB::raw('count(*) as count, DATE(created_at) as date'))
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
            ->select(DB::raw('count(*) as count, HOUR(created_at) as hour'))
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
                ->select(DB::raw('(SUM(transactions.value) - SUM(CASE WHEN transactions.status_enum = 12 THEN anticipated_transactions.value ELSE 0 END)) as value, DATE(release_date) as date'))
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

            $orders = Sale::select(DB::raw('count(*) as count, DATE(sales.end_date) as date, SUM(transaction.value) as value'))
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

    public function getColors($index = null, $hex = false)
    {
        $colors = [ 'blue', 'purple', 'pink', 'orange', 'yellow', 'light-blue', 'light-green', 'grey' ];

        if ($hex == true) {
            $colors = [ '#2E85EC', '#FF7900', '#665FE8', '#F43F5E' ];
        }

        if (!empty($index) || $index >= 0) {
            return $colors[$index];
        }

        return $colors;
    }

    public function getSalesQueryBuilder($filters)
    {
        try {
            $companyModel = new Company();
            $transactionModel = new Transaction();

            $userId = auth()->user()->account_owner_id;
            if (empty($filters["company"])) {
                $userCompanies = $companyModel->where('user_id', $userId)
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

            $transactions = $transactionModel
            ->with('sale')
            ->whereIn('company_id', $userCompanies)
            ->join('sales', 'sales.id', 'transactions.sale_id')
            ->whereNull('invitation_id');

            if (!empty($filters["project"])) {
                $projectId = current(Hashids::decode($filters["project"]));

                $transactions->where('sales.project_id', $projectId);
            }

            $status = [1, 2, 4, 7, 8, 12, 20, 21, 22, 24];
            $transactions->whereIn('sales.status', $status);

            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
            $transactions->whereBetween('sales.start_date', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);

            $transactions
            ->selectRaw('transactions.*, sales.start_date')
            ->orderByDesc('sales.start_date');

            return $transactions;
        } catch (Exception $e) {
            report($e);
            return null;
        }
    }

    // FINANCES --------------------------------------------------------------------------------------
    public function getResumeCommissions($filters)
    {
        try {
            $companyModel = new Company();
            $affiliateModel = new Affiliate();
            $transactionModel = new Transaction();

            $userId = auth()->user()->account_owner_id;

            $userCompanies = $companyModel->where('user_id', $userId)
            ->get()
            ->pluck('id')
            ->toArray();

            $transactions = $transactionModel
            ->with('sale')
            ->whereIn('company_id', $userCompanies)
            ->join('sales', 'sales.id', 'transactions.sale_id')
            ->whereNull('invitation_id');

            if (!empty($filters["project"])) {
                $projectId = current(Hashids::decode($filters["project"]));
                $transactions->where('project_id', $projectId);

                $affiliate = $affiliateModel
                ->where('user_id', $userId)
                ->where('project_id', $projectId)
                ->first();

                if (!empty($affiliate)) {
                    $transactions->where('sales.affiliate_id', $affiliate->id);
                } else {
                    $transactions->where('sales.owner_id', $userId);
                }
            }

            $status = [
                Sale::STATUS_APPROVED,
                Sale::STATUS_PENDING,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_REFUNDED,
                Sale::STATUS_PARTIAL_REFUNDED,
                Sale::STATUS_IN_REVIEW,
                Sale::STATUS_CANCELED_ANTIFRAUD,
                Sale::STATUS_BILLET_REFUNDED,
                Sale::STATUS_IN_DISPUTE
            ];

            $transactions->whereIn('sales.status', $status);

            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
            $date['startDate'] = $dateRange[0];
            $date['endDate'] = $dateRange[1];

            if ($date['startDate'] == $date['endDate']) {
                return $this->getResumeCommissionsByHours($transactions, $filters);
            } elseif ($date['startDate'] != $date['endDate']) {
                $startDate  = Carbon::createFromFormat('Y-m-d', $date['startDate'], 'America/Sao_Paulo');
                $endDate    = Carbon::createFromFormat('Y-m-d', $date['endDate'], 'America/Sao_Paulo');
                $diffInDays = $endDate->diffInDays($startDate);

                if ($diffInDays <= 20) {
                    return $this->getResumeCommissionsByDays($transactions, $filters);
                } elseif ($diffInDays > 20 && $diffInDays <= 40) {
                    return $this->getResumeCommissionsByTwentyDays($transactions, $filters);
                } elseif ($diffInDays > 40 && $diffInDays <= 60) {
                    return $this->getResumeCommissionsByFortyDays($transactions, $filters);
                } elseif ($diffInDays > 60 && $diffInDays <= 140) {
                    return $this->getResumeCommissionsByWeeks($transactions, $filters);
                } elseif ($diffInDays > 140) {
                    return $this->getResumeCommissionsByMonths($transactions, $filters);
                }
            }
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumeCommissionsByHours($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        if (Carbon::parse($dateRange[0])->format('m/d/y') == Carbon::now()->format('m/d/y')) {
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

        $transactionStatus = [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ];
        $statusDispute = Sale::STATUS_IN_DISPUTE;

        $resume = $transactions
        ->whereIn('status_enum', $transactionStatus)
        ->where('sales.status', '<>', $statusDispute)
        ->whereDate('start_date', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59'])
        ->select(DB::raw('transactions.value as commission, HOUR(sales.start_date) as hour'))
        ->get();

        $comissionData = [];

        foreach ($labelList as $label) {
            $comissionValue = 0;

            foreach ($resume as $r) {
                if ($r->hour == preg_replace("/[^0-9]/", "", $label)) {
                    $comissionValue = intval(preg_replace("/[^0-9]/", "", $r->commission));
                }
            }

            array_push($comissionData, $comissionValue);
        }

        $total = number_format(array_sum($comissionData) / 100, 2, ',', '.');

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round((($comissionData[count($comissionData) - 1] / $comissionData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $comissionData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCommissionsByDays($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $labelList    = [];
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate      = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d-m'));
            $dataFormated = $dataFormated->addDays(1);
        }

        $transactionStatus = [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ];
        $statusDispute = Sale::STATUS_IN_DISPUTE;

        $resume = $transactions
        ->whereIn('status_enum', $transactionStatus)
        ->where('sales.status', '<>', $statusDispute)
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('transactions.value as commission, DATE(sales.start_date) as date'))
        ->get();

        $comissionData = [];

        foreach ($labelList as $label) {
            $comissionValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('d-m') == $label) {
                    $comissionValue = intval(preg_replace("/[^0-9]/", "", $r->commission));
                }
            }

            array_push($comissionData, $comissionValue);
        }

        $total = number_format(array_sum($comissionData) / 100, 2, ',', '.');

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round((($comissionData[count($comissionData) - 1] / $comissionData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $comissionData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCommissionsByTwentyDays($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList    = [];

        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $dataFormated = Carbon::parse($dateRange[0])->addDays(1);
        $endDate      = Carbon::parse($dateRange[1]);

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

        $transactionStatus = [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ];
        $statusDispute = Sale::STATUS_IN_DISPUTE;
        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $transactions
        ->whereIn('status_enum', $transactionStatus)
        ->where('sales.status', '<>', $statusDispute)
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('transactions.value as commission, DATE(sales.start_date) as date'))
        ->get();

        $comissionData = [];

        foreach ($labelList as $label) {
            $comissionDataValue = 0;

            foreach ($resume as $r) {
                if ((Carbon::parse($r->date)->subDays(1)->format('d/m') == $label) || (Carbon::parse($r->date)->format('d/m') == $label)) {
                    $comissionDataValue += intval(preg_replace("/[^0-9]/", "", $r->commission));
                }
            }

            array_push($comissionData, $comissionDataValue);
        }

        $total = number_format(array_sum($comissionData) / 100, 2, ',', '.');

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round((($comissionData[count($comissionData) - 1] / $comissionData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $comissionData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCommissionsByFortyDays($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(2);
        $endDate = Carbon::parse($dateRange[1]);

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

        $transactionStatus = [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ];
        $statusDispute = Sale::STATUS_IN_DISPUTE;
        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $transactions
        ->whereIn('status_enum', $transactionStatus)
        ->where('sales.status', '<>', $statusDispute)
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('transactions.value as commission, DATE(sales.start_date) as date'))
        ->get();

        $comissionData = [];

        foreach ($labelList as $label) {
            $comissionDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 3; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $comissionDataValue += intval(preg_replace("/[^0-9]/", "", $r->commission));
                    }
                }
            }

            array_push($comissionData, $comissionDataValue);
        }

        $total = number_format(array_sum($comissionData) / 100, 2, ',', '.');

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round((($comissionData[count($comissionData) - 1] / $comissionData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $comissionData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCommissionsByWeeks($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(6);
        $endDate = Carbon::parse($dateRange[1]);

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

        $transactionStatus = [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ];
        $statusDispute = Sale::STATUS_IN_DISPUTE;
        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $transactions
        ->whereIn('status_enum', $transactionStatus)
        ->where('sales.status', '<>', $statusDispute)
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('transactions.value as commission, DATE(sales.start_date) as date'))
        ->get();

        $comissionData = [];

        foreach ($labelList as $label) {
            $comissionDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 6; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $comissionDataValue += intval(preg_replace("/[^0-9]/", "", $r->commission));
                    }
                }
            }

            array_push($comissionData, $comissionDataValue);
        }

        $total = number_format(array_sum($comissionData) / 100, 2, ',', '.');

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round((($comissionData[count($comissionData) - 1] / $comissionData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $comissionData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCommissionsByMonths($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('m/y'));
            $dataFormated = $dataFormated->addMonths(1);
        }

        $transactionStatus = [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ];
        $statusDispute = Sale::STATUS_IN_DISPUTE;
        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $transactions
        ->whereIn('status_enum', $transactionStatus)
        ->where('sales.status', '<>', $statusDispute)
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('transactions.value as commission, DATE(sales.start_date) as date'))
        ->get();

        $comissionData = [];

        foreach ($labelList as $label) {
            $comissionDataValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('m/y') == $label) {
                    $comissionDataValue += intval(preg_replace("/[^0-9]/", "", $r->commission));
                }
            }

            array_push($comissionData, $comissionDataValue);
        }

        $total = number_format(array_sum($comissionData) / 100, 2, ',', '.');

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round((($comissionData[count($comissionData) - 1] / $comissionData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $comissionData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    // ----------------------------------------
    public function getResumePendings($filters)
    {
        try {
            $saleModel = new Sale();
            $affiliateModel = new Affiliate();

            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
            $userId = auth()->user()->account_owner_id;
            $status = Sale::STATUS_PENDING;

            $sales = $saleModel
            ->where('owner_id', $userId)
            ->where('status', $status);

            if (!empty($filters["project"])) {
                $projectId = current(Hashids::decode($filters["project"]));
                $sales->where('project_id', $projectId);

                $affiliate = $affiliateModel
                ->where('user_id', $userId)
                ->where('project_id', $projectId)
                ->first();

                if (!empty($affiliate)) {
                    $sales->where('affiliate_id', $affiliate->id);
                } else {
                    $sales->where('owner_id', $userId);
                }
            }

            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
            $date['startDate'] = $dateRange[0];
            $date['endDate'] = $dateRange[1];

            if ($date['startDate'] == $date['endDate']) {
                return $this->getResumePendingsByHours($sales, $filters);
            } elseif ($date['startDate'] != $date['endDate']) {
                $startDate  = Carbon::createFromFormat('Y-m-d', $date['startDate'], 'America/Sao_Paulo');
                $endDate    = Carbon::createFromFormat('Y-m-d', $date['endDate'], 'America/Sao_Paulo');
                $diffInDays = $endDate->diffInDays($startDate);

                if ($diffInDays <= 20) {
                    return $this->getResumePendingsByDays($sales, $filters);
                } elseif ($diffInDays > 20 && $diffInDays <= 40) {
                    return $this->getResumePendingsByTwentyDays($sales, $filters);
                } elseif ($diffInDays > 40 && $diffInDays <= 60) {
                    return $this->getResumePendingsByFortyDays($sales, $filters);
                } elseif ($diffInDays > 60 && $diffInDays <= 140) {
                    return $this->getResumePendingsByWeeks($sales, $filters);
                } elseif ($diffInDays > 140) {
                    return $this->getResumePendingsByMonths($sales, $filters);
                }
            }
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumePendingsByHours($sales, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        if (Carbon::parse($dateRange[0])->format('m/d/y') == Carbon::now()->format('m/d/y')) {
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

        $resume = $sales
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('sub_total, shipment_value, shopify_discount, automatic_discount, HOUR(start_date) as hour'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                if ($r->hour == preg_replace("/[^0-9]/", "", $label)) {
                    $sub_total = intval(preg_replace("/[^0-9]/", "", $r->sub_total));
                    $shipment_value = intval(preg_replace("/[^0-9]/", "", $r->shipment_value));
                    $shopify_discount = intval(preg_replace("/[^0-9]/", "", $r->shopify_discount));
                    $automatic_discount = intval(preg_replace("/[^0-9]/", "", $r->automatic_discount));

                    $saleDataValue += ($sub_total + $shipment_value) - ($shopify_discount + $automatic_discount);
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ',', '.');

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumePendingsByDays($sales, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $labelList    = [];
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate      = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d-m'));
            $dataFormated = $dataFormated->addDays(1);
        }

        $resume = $sales
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('sub_total, shipment_value, shopify_discount, automatic_discount, DATE(start_date) as date'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('d-m') == $label) {
                    $sub_total = intval(preg_replace("/[^0-9]/", "", $r->sub_total));
                    $shipment_value = intval(preg_replace("/[^0-9]/", "", $r->shipment_value));
                    $shopify_discount = intval(preg_replace("/[^0-9]/", "", $r->shopify_discount));
                    $automatic_discount = intval(preg_replace("/[^0-9]/", "", $r->automatic_discount));

                    $saleDataValue += ($sub_total + $shipment_value) - ($shopify_discount + $automatic_discount);
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ',', '.');

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumePendingsByTwentyDays($sales, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList    = [];

        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $dataFormated = Carbon::parse($dateRange[0])->addDays(1);
        $endDate      = Carbon::parse($dateRange[1]);

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

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $sales
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('sub_total, shipment_value, shopify_discount, automatic_discount, DATE(start_date) as date'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                if ((Carbon::parse($r->date)->subDays(1)->format('d/m') == $label) || (Carbon::parse($r->date)->format('d/m') == $label)) {
                    $sub_total = intval(preg_replace("/[^0-9]/", "", $r->sub_total));
                    $shipment_value = intval(preg_replace("/[^0-9]/", "", $r->shipment_value));
                    $shopify_discount = intval(preg_replace("/[^0-9]/", "", $r->shopify_discount));
                    $automatic_discount = intval(preg_replace("/[^0-9]/", "", $r->automatic_discount));

                    $saleDataValue += ($sub_total + $shipment_value) - ($shopify_discount + $automatic_discount);
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ',', '.');

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumePendingsByFortyDays($sales, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(2);
        $endDate = Carbon::parse($dateRange[1]);

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

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $sales
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('sub_total, shipment_value, shopify_discount, automatic_discount, DATE(start_date) as date'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 3; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $sub_total = intval(preg_replace("/[^0-9]/", "", $r->sub_total));
                        $shipment_value = intval(preg_replace("/[^0-9]/", "", $r->shipment_value));
                        $shopify_discount = intval(preg_replace("/[^0-9]/", "", $r->shopify_discount));
                        $automatic_discount = intval(preg_replace("/[^0-9]/", "", $r->automatic_discount));

                        $saleDataValue += ($sub_total + $shipment_value) - ($shopify_discount + $automatic_discount);
                    }
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ',', '.');

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumePendingsByWeeks($sales, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(6);
        $endDate = Carbon::parse($dateRange[1]);

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

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $sales
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('sub_total, shipment_value, shopify_discount, automatic_discount, DATE(start_date) as date'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 6; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $sub_total = intval(preg_replace("/[^0-9]/", "", $r->sub_total));
                        $shipment_value = intval(preg_replace("/[^0-9]/", "", $r->shipment_value));
                        $shopify_discount = intval(preg_replace("/[^0-9]/", "", $r->shopify_discount));
                        $automatic_discount = intval(preg_replace("/[^0-9]/", "", $r->automatic_discount));

                        $saleDataValue += ($sub_total + $shipment_value) - ($shopify_discount + $automatic_discount);
                    }
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ',', '.');

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumePendingsByMonths($sales, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('m/y'));
            $dataFormated = $dataFormated->addMonths(1);
        }

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $sales
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('sub_total, shipment_value, shopify_discount, automatic_discount, DATE(start_date) as date'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('m/y') == $label) {
                    $sub_total = intval(preg_replace("/[^0-9]/", "", $r->sub_total));
                    $shipment_value = intval(preg_replace("/[^0-9]/", "", $r->shipment_value));
                    $shopify_discount = intval(preg_replace("/[^0-9]/", "", $r->shopify_discount));
                    $automatic_discount = intval(preg_replace("/[^0-9]/", "", $r->automatic_discount));

                    $saleDataValue += ($sub_total + $shipment_value) - ($shopify_discount + $automatic_discount);
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ',', '.');

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    // -----------------------------------------
    public function getResumeCashbacks($filters)
    {
        try {
            $cashbackModel = new Cashback();
            $companyModel = new Company();

            $userId = auth()->user()->account_owner_id;

            $userCompanies = $companyModel->where('user_id', $userId)
            ->get()
            ->pluck('id')
            ->toArray();

            $cashbacks = $cashbackModel
            ->with('sale')
            ->whereIn('company_id', $userCompanies)
            ->join('sales', 'sales.id', 'cashbacks.sale_id');

            if (!empty($filters["project"])) {
                $projectId = current(Hashids::decode($filters["project"]));

                $cashbacks->where('sales.project_id', $projectId);
            }

            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
            $date['startDate'] = $dateRange[0];
            $date['endDate'] = $dateRange[1];

            if ($date['startDate'] == $date['endDate']) {
                return $this->getResumeCashbacksByHours($cashbacks, $filters);
            } elseif ($date['startDate'] != $date['endDate']) {
                $startDate  = Carbon::createFromFormat('Y-m-d', $date['startDate'], 'America/Sao_Paulo');
                $endDate    = Carbon::createFromFormat('Y-m-d', $date['endDate'], 'America/Sao_Paulo');
                $diffInDays = $endDate->diffInDays($startDate);

                if ($diffInDays <= 20) {
                    return $this->getResumeCashbacksByDays($cashbacks, $filters);
                } elseif ($diffInDays > 20 && $diffInDays <= 40) {
                    return $this->getResumeCashbacksByTwentyDays($cashbacks, $filters);
                } elseif ($diffInDays > 40 && $diffInDays <= 60) {
                    return $this->getResumeCashbacksByFortyDays($cashbacks, $filters);
                } elseif ($diffInDays > 60 && $diffInDays <= 140) {
                    return $this->getResumeCashbacksByWeeks($cashbacks, $filters);
                } elseif ($diffInDays > 140) {
                    return $this->getResumeCashbacksByMonths($cashbacks, $filters);
                }
            }
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumeCashbacksByHours($cashbacks, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        if (Carbon::parse($dateRange[0])->format('m/d/y') == Carbon::now()->format('m/d/y')) {
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

        $resume = $cashbacks
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('cashbacks.value as cashback, HOUR(sales.start_date) as hour'))
        ->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                if ($r->hour == preg_replace("/[^0-9]/", "", $label)) {
                    $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ',', '.');

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round((($cashbackData[count($cashbackData) - 1] / $cashbackData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $cashbackData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCashbacksByDays($cashbacks, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $labelList    = [];
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate      = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d-m'));
            $dataFormated = $dataFormated->addDays(1);
        }

        $resume = $cashbacks
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('cashbacks.value as cashback, DATE(sales.start_date) as date'))
        ->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('d-m') == $label) {
                    $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ',', '.');

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round((($cashbackData[count($cashbackData) - 1] / $cashbackData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $cashbackData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCashbacksByTwentyDays($cashbacks, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList    = [];

        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $dataFormated = Carbon::parse($dateRange[0])->addDays(1);
        $endDate      = Carbon::parse($dateRange[1]);

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

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $cashbacks
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('cashbacks.value as cashback, DATE(sales.start_date) as date'))
        ->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                if ((Carbon::parse($r->date)->subDays(1)->format('d/m') == $label) || (Carbon::parse($r->date)->format('d/m') == $label)) {
                    $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ',', '.');

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round((($cashbackData[count($cashbackData) - 1] / $cashbackData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $cashbackData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCashbacksByFortyDays($cashbacks, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(2);
        $endDate = Carbon::parse($dateRange[1]);

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

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $cashbacks
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('cashbacks.value as cashback, DATE(sales.start_date) as date'))
        ->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 3; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                    }
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ',', '.');

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round((($cashbackData[count($cashbackData) - 1] / $cashbackData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $cashbackData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCashbacksByWeeks($cashbacks, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(6);
        $endDate = Carbon::parse($dateRange[1]);

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

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $cashbacks
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('cashbacks.value as cashback, DATE(sales.start_date) as date'))
        ->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 6; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                    }
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ',', '.');

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round((($cashbackData[count($cashbackData) - 1] / $cashbackData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $cashbackData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCashbacksByMonths($cashbacks, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('m/y'));
            $dataFormated = $dataFormated->addMonths(1);
        }

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $cashbacks
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('cashbacks.value as cashback, DATE(sales.start_date) as date'))
        ->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('m/y') == $label) {
                    $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ',', '.');

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round((($cashbackData[count($cashbackData) - 1] / $cashbackData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $cashbackData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    // SALES --------------------------------------------------------------------------------------
    public function getResumeSales($filters)
    {
       try {
            $transactions = $this->getSalesQueryBuilder($filters);

            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
            $date['startDate'] = $dateRange[0];
            $date['endDate'] = $dateRange[1];

            if ($date['startDate'] == $date['endDate']) {
                return $this->getResumeSalesByHours($transactions, $filters);
            } elseif ($date['startDate'] != $date['endDate']) {
                $startDate  = Carbon::createFromFormat('Y-m-d', $date['startDate'], 'America/Sao_Paulo');
                $endDate    = Carbon::createFromFormat('Y-m-d', $date['endDate'], 'America/Sao_Paulo');
                $diffInDays = $endDate->diffInDays($startDate);

                if ($diffInDays <= 20) {
                    return $this->getResumeSalesByDays($transactions, $filters);
                } elseif ($diffInDays > 20 && $diffInDays <= 40) {
                    return $this->getResumeSalesByTwentyDays($transactions, $filters);
                } elseif ($diffInDays > 40 && $diffInDays <= 60) {
                    return $this->getResumeSalesByFortyDays($transactions, $filters);
                } elseif ($diffInDays > 60 && $diffInDays <= 140) {
                    return $this->getResumeSalesByWeeks($transactions, $filters);
                } elseif ($diffInDays > 140) {
                    return $this->getResumeSalesByMonths($transactions, $filters);
                }
            }
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumeSalesByHours($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        if (Carbon::parse($dateRange[0])->format('m/d/y') == Carbon::now()->format('m/d/y')) {
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

        $transactionStatus = [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ];
        $statusDispute = Sale::STATUS_IN_DISPUTE;

        $resume = $transactions
        ->whereIn('status_enum', $transactionStatus)
        ->where('sales.status', '<>', $statusDispute)
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('sales.id as sale, HOUR(sales.start_date) as hour'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                if ($r->hour == preg_replace("/[^0-9]/", "", $label)) {
                    $saleDataValue += 1;
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = array_sum($saleData);

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeSalesByDays($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $labelList    = [];
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate      = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d-m'));
            $dataFormated = $dataFormated->addDays(1);
        }

        $transactionStatus = [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ];
        $statusDispute = Sale::STATUS_IN_DISPUTE;

        $resume = $transactions
        ->whereIn('status_enum', $transactionStatus)
        ->where('sales.status', '<>', $statusDispute)
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('sales.id as sale, DATE(sales.start_date) as date'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('d-m') == $label) {
                    $saleDataValue += 1;
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = array_sum($saleData);

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeSalesByTwentyDays($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList    = [];

        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $dataFormated = Carbon::parse($dateRange[0])->addDays(1);
        $endDate      = Carbon::parse($dateRange[1]);

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

        $transactionStatus = [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ];
        $statusDispute = Sale::STATUS_IN_DISPUTE;
        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $transactions
        ->whereIn('status_enum', $transactionStatus)
        ->where('sales.status', '<>', $statusDispute)
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('sales.id as sale, DATE(sales.start_date) as date'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                if ((Carbon::parse($r->date)->subDays(1)->format('d/m') == $label) || (Carbon::parse($r->date)->format('d/m') == $label)) {
                    $saleDataValue += 1;
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = array_sum($saleData);

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeSalesByFortyDays($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(2);
        $endDate = Carbon::parse($dateRange[1]);

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

        $transactionStatus = [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ];
        $statusDispute = Sale::STATUS_IN_DISPUTE;
        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $transactions
        ->whereIn('status_enum', $transactionStatus)
        ->where('sales.status', '<>', $statusDispute)
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('sales.id as sale, DATE(sales.start_date) as date'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 3; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $saleDataValue += 1;
                    }
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = array_sum($saleData);

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeSalesByWeeks($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(6);
        $endDate = Carbon::parse($dateRange[1]);

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

        $transactionStatus = [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ];
        $statusDispute = Sale::STATUS_IN_DISPUTE;
        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $transactions
        ->whereIn('status_enum', $transactionStatus)
        ->where('sales.status', '<>', $statusDispute)
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('sales.id as sale, DATE(sales.start_date) as date'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 6; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $saleDataValue += 1;
                    }
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = array_sum($saleData);

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeSalesByMonths($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('m/y'));
            $dataFormated = $dataFormated->addMonths(1);
        }

        $transactionStatus = [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ];
        $statusDispute = Sale::STATUS_IN_DISPUTE;
        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $transactions
        ->whereIn('status_enum', $transactionStatus)
        ->where('sales.status', '<>', $statusDispute)
        ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
        ->select(DB::raw('sales.id as sale, DATE(sales.start_date) as date'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('m/y') == $label) {
                    $saleDataValue += 1;
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = array_sum($saleData);

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    // --------------------------------------------
    public function getResumeTypePayments($filters)
    {
        try {
            $saleModel = new Sale();

            $userId = auth()->user()->account_owner_id;
            $status = Sale::STATUS_APPROVED;
            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

            $query = $saleModel
            ->where('owner_id', $userId)
            ->where('status', $status)
            ->whereBetween('start_date', [ $dateRange[0].' 00:00:00', $dateRange[0].' 23:59:59' ])
            ->selectRaw('SUM((sub_total + shipment_value) - (IFNULL(shopify_discount, 0) + automatic_discount) / 100) as total')
            ->selectRaw('SUM(IF(payment_method = 1, (sub_total + shipment_value) - (IFNULL(shopify_discount, 0) + automatic_discount) / 100, 0)) as total_credit_card')
            ->selectRaw('SUM(IF(payment_method = 2, (sub_total + shipment_value) - (IFNULL(shopify_discount, 0) + automatic_discount) / 100, 0)) as total_boleto')
            ->selectRaw('SUM(IF(payment_method = 4, (sub_total + shipment_value) - (IFNULL(shopify_discount, 0) + automatic_discount) / 100, 0)) as total_pix')
            ->first();

            $total = $query->total;

            $totalCreditCard = $query->total_credit_card;
            $percentageCreditCard = $totalCreditCard > 0 ? number_format(($totalCreditCard * 100) / $total, 2, '.', ',') : 0;

            $totalBoleto = $query->total_boleto;
            $percentageBoleto = $totalBoleto > 0 ? number_format(($totalBoleto * 100) / $total, 2, '.', ',') : 0;

            $totalPix = $query->total_pix;
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

    public function getResumeProducts($filters)
    {
        try {
            $productModel = new Product();

            $userId = auth()->user()->account_owner_id;
            $status = Sale::STATUS_APPROVED;
            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

            $query = $productModel
            ->join('products_plans_sales', 'products.id', 'products_plans_sales.product_id')
            ->join('sales', 'products_plans_sales.id', 'sales.id')
            ->where('sales.owner_id', $userId)
            ->where('sales.status', $status)
            ->whereBetween('start_date', [ $dateRange[0].' 00:00:00', $dateRange[0].' 23:59:59' ])
            ->select(DB::raw('products.name, products.photo as image, COUNT(*) as amount'))
            ->groupBy('products.id')
            ->orderByDesc('amount')
            ->limit(8)
            ->get();

            if (!empty($filters["project"])) {
                $projectId = Hashids::decode($filters["project"]);

                $query->where('project_id', $projectId);
            }

            $total = 0;
            foreach($query as $r)
            {
                $total += $r->amount;
            }

            $index = 0;
            foreach($query as $result)
            {
                $percentage = round(number_format(($result->amount * 100) / $total, 2, '.', ','), 1, PHP_ROUND_HALF_UP);

                $result->image = empty($result->image) ? 'https://cloudfox-files.s3.amazonaws.com/produto.svg' : $result->image;
                $result->percentage = $percentage < 28 ? '28%' : $percentage.'%';
                $result->color = $this->getColors($index);

                $index++;
            }

            $productsArray = $query->toArray();

            foreach($productsArray as $key => $product)
            {
                unset($productsArray[$key]['id_code']);
            }

            array_push($productsArray, (object) [
                'total' => $total
            ]);

            return $productsArray;
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // MARKETING --------------------------------------------------------------------------------------
    public function getResumeCoupons($filters)
    {
        try {
            $userId = auth()->user()->account_owner_id;
            $status = [1, 2, 4, 6, 7, 8, 12, 20, 22];
            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

            $query = Sale::select(DB::raw('sales.cupom_code as coupon, COUNT(*) as amount'))
            ->where('sales.owner_id', $userId)
            ->whereIn('status', $status)
            ->where('sales.cupom_code', '<>', '')
            ->whereBetween('sales.start_date', [ $dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59' ]);

            if (!empty($filters["project"])) {
                $projectId = Hashids::decode($filters["project"]);

                $query->where('project_id', $projectId);
            }

            $coupons = $query
            ->groupBy('sales.cupom_code')
            ->orderByDesc('amount')
            ->limit(4)
            ->get();

            $total = 0;
            foreach($coupons as $coupon)
            {
                $total += $coupon->amount;
            }

            $index = 0;
            foreach($coupons as $coupon)
            {
                $coupon->percentage = round(number_format(($coupon->amount * 100) / $total, 2, '.', ','), 1, PHP_ROUND_HALF_UP).'%';
                $coupon->color = $this->getColors($index);
                $coupon->hexadecimal = $this->getColors($index, true);

                $index++;
            }

            $couponsArray = $coupons->toArray();

            foreach($couponsArray as $key => $coupon)
            {
                unset($couponsArray[$key]['id_code']);
            }

            array_push($couponsArray, [
                'total' => $total
            ]);

            return $couponsArray;
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumeRegions($filters)
    {
        try {
            $checkoutModel = new Checkout();

            $userId = auth()->user()->account_owner_id;
            $status = [Checkout::STATUS_ACCESSED, Checkout::STATUS_SALE_FINALIZED];
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);


            $query = $checkoutModel->select(
                DB::raw('ip, COUNT(DISTINCT CASE WHEN status_enum = 1 then id end) as acessed, COUNT(DISTINCT CASE WHEN status_enum = 4 then id end) as finalized')
            )
            ->whereIn('checkouts.status_enum', $status)
            ->whereBetween('checkouts.created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
            ->groupBy('checkouts.ip');

            if (!empty($filters['project_id'])) {
                $query->where('checkouts.project_id', current(Hashids::decode($filters['project_id'])));
            } else {
                $user_projects = UserProject::where('user_id', $userId)->get()->pluck('id');

                $query->whereIn('checkouts.project_id', $user_projects);
            }

            $regions = $query->get();
            foreach($regions as $region)
            {
                $region->state = json_decode(getRegionByIp($region->ip))->state;
            }

            $regionsArray = $regions->toArray();
            foreach($regionsArray as $key => $region)
            {
                unset($regionsArray[$key]['id_code']);
            }

            return $regionsArray;
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumeOrigins($filters)
    {
        try {
            $saleModel = new Sale();

            $userId = auth()->user()->account_owner_id;
            $status = Sale::STATUS_APPROVED;
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);

            $query = $saleModel->select(DB::raw('count(*) as sales_amount, SUM(transaction.value) as value, checkout.'.$filters['origin'].' as origin'))
            ->leftJoin('transactions as transaction', function ($join) use ($userId) {
                $join->on('transaction.sale_id', '=', 'sales.id');
                $join->where('transaction.user_id', $userId);
            })
            ->leftJoin('checkouts as checkout', function ($join) {
                $join->on('checkout.id', '=', 'sales.checkout_id');
            })
            ->where('sales.status', $status)
            ->whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
            ->whereNotIn('checkout.'.$filters['origin'], ['', 'null'])
            ->whereNotNull('checkout.'.$filters['origin'])
            ->groupBy('checkout.'.$filters['origin'])
            ->orderBy('sales_amount', 'DESC');

            if (!empty($filters['project_id'])) {
                $projectId = current(Hashids::decode($filters['project_id']));

                $query->where('sales.project_id', $projectId);
            } else {
                $query->where('sales.owner_id', $userId);
            }

            $orders = $query->get();

            return $orders;
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // Page finances
    function getFinancesResume($filters)
    {
        try {
            $transactions = $this->getSalesQueryBuilder($filters);

            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

            $transactionStatus = [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ];
            $statusDispute = Sale::STATUS_IN_DISPUTE;

            $queryTransactions = $transactions
            ->whereIn('status_enum', $transactionStatus)
            ->where('sales.status', '<>', $statusDispute)
            ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
            ->select(DB::raw('COUNT(*) as numTransactions, SUM(transactions.value) as valueTransactions'))
            ->first();

            $totalAverageTicket = ($queryTransactions->valueTransactions / $queryTransactions->numTransactions);

            $queryComission = $transactions
            ->whereIn('status_enum', $transactionStatus)
            ->where('sales.status', '<>', $statusDispute)
            ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
            ->sum('transactions.value');

            $queryChargeback = $transactions
            ->where('status_enum', Transaction::STATUS_CHARGEBACK)
            ->where('sales.status', Sale::STATUS_CHARGEBACK)
            ->whereBetween('start_date', [$dateRange[0], date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'))])
            ->sum('transactions.value');

            return [
                'transactions' => $queryTransactions->numTransactions,
                'average_ticket' => foxutils()->formatMoney($totalAverageTicket / 100),
                'comission' => foxutils()->formatMoney($queryComission / 100),
                'chargeback' => foxutils()->formatMoney($queryChargeback / 100)
            ];
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getFinancesCashbacks($filters)
    {
        try {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $userId = auth()->user()->account_owner_id;

            $cachsbackModel = new Cashback();

            $cashbacks = $cachsbackModel
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59']);

            $cashbacksValue = $cashbacks->sum('value');
            $cashbacksCount = $cashbacks->count();

            return [
                'value' => foxutils()->formatMoney($cashbacksValue / 100),
                'quantity' => $cashbacksCount
            ];
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getFinancesPendings($filters)
    {
        try {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $company = Hashids::decode($filters["company"]);

            $gatewayIds = [
                Gateway::SAFE2PAY_PRODUCTION_ID,
                Gateway::SAFE2PAY_SANDBOX_ID,
                Gateway::ASAAS_PRODUCTION_ID,
                Gateway::ASAAS_SANDBOX_ID,
                Gateway::GETNET_PRODUCTION_ID,
                Gateway::GETNET_SANDBOX_ID,
                Gateway::GERENCIANET_PRODUCTION_ID,
                Gateway::GERENCIANET_SANDBOX_ID,
                Gateway::CIELO_PRODUCTION_ID,
                Gateway::CIELO_SANDBOX_ID ,
                // extrato cielo engloba as vendas antigas zoop e pagarme
                Gateway::PAGARME_PRODUCTION_ID,
                Gateway::PAGARME_SANDBOX_ID,
                Gateway::ZOOP_PRODUCTION_ID,
                Gateway::ZOOP_SANDBOX_ID
            ];

            $transactionModel = new Transaction();
            $transactions = $transactionModel
            ->where('company_id', $company)
            ->where('status_enum', Transaction::STATUS_PAID)
            ->whereIn('gateway_id', $gatewayIds)
            ->whereBetween('created_at', [ $dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59' ])
            ->whereDoesntHave('blockReasonSale',function ($query) {
                $query->where('status', BlockReasonSale::STATUS_BLOCKED);
            });

            $transactionsValue = $transactions->sum('value');
            $transactionsAmount = $transactions->count();

            return [
                'value' => foxutils()->formatMoney($transactionsValue / 100),
                'amount' => $transactionsAmount
            ];
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getFinancesBlockeds($filters)
    {
        try {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $company = Hashids::decode($filters["company"]);

            $gatewayIds = [
                Gateway::SAFE2PAY_PRODUCTION_ID,
                Gateway::SAFE2PAY_SANDBOX_ID,
                Gateway::ASAAS_PRODUCTION_ID,
                Gateway::ASAAS_SANDBOX_ID,
                Gateway::GETNET_PRODUCTION_ID,
                Gateway::GETNET_SANDBOX_ID,
                Gateway::GERENCIANET_PRODUCTION_ID,
                Gateway::GERENCIANET_SANDBOX_ID,
                Gateway::CIELO_PRODUCTION_ID,
                Gateway::CIELO_SANDBOX_ID ,
                // extrato cielo engloba as vendas antigas zoop e pagarme
                Gateway::PAGARME_PRODUCTION_ID,
                Gateway::PAGARME_SANDBOX_ID,
                Gateway::ZOOP_PRODUCTION_ID,
                Gateway::ZOOP_SANDBOX_ID
            ];

            $transactionModel = new Transaction();
            $transactions = $transactionModel
            ->where('company_id', $company)
            ->whereIn('gateway_id', $gatewayIds)
            ->whereBetween('transactions.created_at', [ $dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59' ])
            ->where('status_enum', Transaction::STATUS_TRANSFERRED)
            ->join('block_reason_sales', 'block_reason_sales.sale_id', '=', 'transactions.sale_id')
            ->where('block_reason_sales.status', BlockReasonSale::STATUS_BLOCKED);

            $transactionsValue = $transactions->sum('value');
            $transactionsAmount = $transactions->count();

            return [
                'value' => foxutils()->formatMoney($transactionsValue / 100),
                'amount' => $transactionsAmount
            ];
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    function getFinancesDistribuitions($filters)
    {
        try {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $company = Hashids::decode($filters["company"])[0];

            $company = Company::where('id', $company)->first();

            $defaultGateways = [
                Safe2PayService::class,
                AsaasService::class,
                GetnetService::class,
                GerencianetService::class,
                CieloService::class,
            ];

            $balancesAvailable = [];
            $balancesPending = [];
            $balancesBlocked = [];
            $balancesBlockedPending = [];

            foreach($defaultGateways as $gatewayClass) {
                $gateway = app()->make($gatewayClass);
                $gateway->setCompany($company);

                $balancesAvailable[] = $gateway->getAvailableBalance();
                $balancesPending[] = $gateway->getPendingBalance();
                $balancesBlocked[] = $gateway->getBlockedBalance();
                $balancesBlockedPending[] = $gateway->getBlockedBalancePending();
            }

            $availableBalance = array_sum($balancesAvailable);
            $pendingBalance = array_sum($balancesPending);
            $blockedBalance = array_sum($balancesBlocked);
            $blockedBalancePending = array_sum($balancesBlockedPending);
            $totalBalanceBlocked = $blockedBalance + $blockedBalancePending;

            $totalBalance = $availableBalance + $pendingBalance + $blockedBalance + $blockedBalancePending;

            return [
                'totalBalance'              => foxutils()->formatMoney($totalBalance / 100),
                'availableBalance'          => [
                    'value' => foxutils()->formatMoney($availableBalance / 100),
                    'percentage' => round(($availableBalance * 100) / $totalBalance, 1, PHP_ROUND_HALF_UP)
                ],
                'pendingBalance'            => [
                    'value' => foxutils()->formatMoney($pendingBalance / 100),
                    'percentage' => round(($pendingBalance * 100) / $totalBalance, 1, PHP_ROUND_HALF_UP)
                ],
                'blocked'            => [
                    'value' => foxutils()->formatMoney($totalBalanceBlocked / 100),
                    'percentage' => round(($totalBalanceBlocked * 100) / $totalBalance, 1, PHP_ROUND_HALF_UP)
                ]
            ];
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    // End Pages finances

    public function getResumeMarketing($filters)
    {
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $userProjects = UserProject::where('user_id', auth()->user()->account_owner_id)->pluck('project_id')->toArray();

        $checkoutsCount = Checkout::whereIn('project_id', $userProjects)
                                    ->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                                    ->count();

        $salesCount = Sale::where('owner_id', auth()->user()->account_owner_id)
                            ->whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                            ->whereNotIn('status', [
                                Sale::STATUS_CANCELED_ANTIFRAUD,
                                Sale::STATUS_REFUSED,
                                Sale::STATUS_SYSTEM_ERROR
                            ])
                            ->count();

        $salesValue = Sale::where('owner_id', auth()->user()->account_owner_id)
                            ->whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                            ->whereNotIn('status', [
                                Sale::STATUS_CANCELED_ANTIFRAUD,
                                Sale::STATUS_REFUSED,
                                Sale::STATUS_SYSTEM_ERROR
                            ])
                            ->sum('original_total_paid_value');

        return [
            'checkouts_count' => number_format($checkoutsCount, 0, '.', '.'),
            'sales_count' => number_format($salesCount, 0, '.', '.'),
            'sales_value' => foxutils()->formatMoney($salesValue / 100)
        ];
    }

    public function getSalesByState($filters)
    {
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $data = Sale::select(DB::raw('delivery.state, count(*) as sales_amount, SUM(transaction.value) as value'))
                        ->leftJoin('transactions as transaction', function ($join) {
                            $join->on('transaction.sale_id', '=', 'sales.id');
                            $join->where('transaction.user_id', auth()->user()->account_owner_id);
                        })
                        ->leftJoin('deliveries as delivery', function ($join) {
                            $join->on('delivery.id', '=', 'sales.delivery_id');
                        })
                        ->where('sales.status', Sale::STATUS_APPROVED)
                        ->whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                        ->groupBy('delivery.state')
                        ->orderBy('sales_amount', 'DESC')
                        ->get()
                        ->toArray();

        $totalSales = 0;
        foreach($data as $state) {
            $totalSales += $state['sales_amount'];
        }

        foreach($data as &$state) {
            $state['percentage'] = number_format(($state['sales_amount'] * 100) / $totalSales, 2, '.', ',') . '%';
            $state['sales_amount'] = number_format($state['sales_amount'], 0, '.', '.');
            $state['value'] = foxutils()->formatMoney($state['value'] / 100);
        }

        return $data;
    }

    public function getMostFrequentSales($filters)
    {
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $data = ProductPlanSale::select(DB::raw('product.photo, product.name, product.description, count(*) as sales_amount, sum(ifnull(transaction.value, 0)) as value'))
                        ->leftJoin('products as product', function ($join) {
                            $join->on('products_plans_sales.product_id', '=', 'product.id');
                        })
                        ->leftJoin('sales as sale', function ($join) {
                            $join->on('products_plans_sales.sale_id', '=', 'sale.id')
                                    ->where('sale.status', Sale::STATUS_APPROVED);
                        })
                        ->leftJoin('transactions as transaction', function ($join) {
                            $join->on('transaction.sale_id', '=', 'sale.id');
                            $join->where('transaction.user_id', auth()->user()->account_owner_id);
                        })
                        ->whereBetween('products_plans_sales.created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                        ->where('transaction.user_id', auth()->user()->account_owner_id)
                        ->groupBy('product.id')
                        ->orderBy('value', 'DESC')
                        ->limit(10)
                        ->get()
                        ->toArray();

        foreach($data as &$product) {
            $product['sales_amount'] = number_format($product['sales_amount'], 0, '.', '.');
            $product['value'] = foxutils()->formatMoney($product['value'] / 100);
        }

        return $data;
    }

    public function getDevices($filters)
    {
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $data = Sale::selectRaw("COUNT(*) AS total,
                        SUM(CASE WHEN checkout.is_mobile = 1 THEN 1 ELSE 0 END) AS count_mobile,
                        SUM(CASE WHEN checkout.is_mobile = 0 THEN 1 ELSE 0 END) AS count_desktop,
                        SUM(CASE WHEN checkout.is_mobile = 1 THEN transaction.value ELSE 0 END) AS value_mobile,
                        SUM(CASE WHEN checkout.is_mobile = 0 THEN transaction.value ELSE 0 END) AS value_desktop
                    ",)
                    ->whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                    ->leftJoin('checkouts as checkout', function ($join) {
                        $join->on('sales.checkout_id', '=', 'checkout.id');
                    })
                    ->leftJoin('transactions as transaction', function ($join) {
                        $join->on('transaction.sale_id', '=', 'sales.id');
                        $join->where('transaction.user_id', auth()->user()->account_owner_id);
                    })
                    ->where('owner_id', auth()->user()->account_owner_id)
                    ->first()
                    ->toArray();

        if(empty($data['count_mobile'])){
            $data['count_mobile'] = 0;
            $data['percentage_mobile'] = '0%';
        }
        else {
            $data['percentage_mobile'] = number_format(($data['count_mobile'] * 100) / $data['total'], 2, '.', ',') . '%';;
        }
        if(empty($data['count_desktop'])){
            $data['count_desktop'] = 0;
            $data['percentage_desktop'] = '0%';
        }
        else {
            $data['percentage_desktop'] = number_format(($data['count_desktop'] * 100) / $data['total'], 2, '.', ',') . '%';;
        }

        $data['value_mobile'] = $data['value_mobile'] > 0 ? foxutils()->formatMoney($data['value_mobile'] / 100) : 'R$ 0,00';
        $data['value_desktop'] = $data['value_desktop'] > 0 ? foxutils()->formatMoney($data['value_desktop'] / 100) : 'R$ 0,00';

        return $data;
    }
}

