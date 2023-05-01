<?php

namespace Modules\Core\Services\Antifraud;

use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\AntifraudWarning;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleInformation;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Services\CheckoutService;

class CloudfoxAntifraudService
{
    public const SALE_APPROVE = "confirm";
    public const SALE_CANCEL = "cancel";

    private $importantSaleInformationData = [
        "ip" => "low",
        "zip_code" => "low",
        "customer_name" => "mid",
        "customer_phone" => "mid",
        "customer_email" => "high",
        "browser_fingerprint" => "high",
        "customer_identification_number" => "high",
        "card_token" => "high",
    ];

    private $columnNames = [
        "ip" => "IP",
        "customer_name" => "Nome",
        "customer_phone" => "Telefone",
        "customer_email" => "Email",
        "browser_fingerprint" => "Impressão Digital do Navegador",
        "customer_identification_number" => "CPF",
        "zip_code" => "CEP",
        "card_token" => "Token do Cartão",
    ];

    private $levelNames = [
        "low" => "Baixa",
        "mid" => "Média",
        "high" => "Alta",
    ];

    private $colors = [
        "low" => "info",
        "mid" => "warning",
        "high" => "danger",
    ];

    public function createFraudWarnings(Carbon $startDate, Carbon $endDate)
    {
        $saleIds = SaleLog::query()
            ->join("sales", "sales.id", "sale_logs.sale_id")
            ->whereIn("sale_logs.status", ["canceled_antifraud", "charge_back", "black_list"])
            ->whereNull("antifraud_warning_level")
            ->whereBetween("sales.start_date", [$startDate->startOfDay(), $endDate->endOfDay()])
            ->get()
            ->pluck("sale_id")
            ->toArray();

        $saleInformations = SaleInformation::whereIn("sale_id", array_unique($saleIds))->get();

        foreach ($saleInformations as $saleInformation) {
            foreach ($this->importantSaleInformationData as $column => $warningLevel) {
                $saleInformation->sale->update(["antifraud_warning_level" => $warningLevel]);
                if ($saleInformation->$column !== null) {
                    try {
                        AntifraudWarning::firstOrCreate(
                            [
                                "sale_id" => $saleInformation->sale_id,
                                "column" => $column,
                                "value" => $saleInformation->$column,
                            ],
                            [
                                "sale_id" => $saleInformation->sale_id,
                                "status" => AntifraudWarning::STATUS_FRAUD_CONFIRMED,
                                "column" => $column,
                                "value" => $saleInformation->$column,
                                "level" => $warningLevel,
                            ]
                        );
                    } catch (Exception $e) {
                        report($e);
                    }
                }
            }
        }
    }

    public function processPayment(Sale $sale, string $statusType): array
    {
        try {
            $saleHashId = hashids_encode($sale->id, "sale_id");

            if (!in_array($statusType, [self::SALE_APPROVE, self::SALE_CANCEL])) {
                return [
                    "status" => "error",
                    "message" => "Erro no antifraude.",
                ];
            }

            if (foxutils()->isProduction()) {
                $urlCancelPayment =
                    "https://checkout.nexuspay.vip/api/payment/antifraud/" . $statusType . "/" . $saleHashId;
            } else {
                $urlCancelPayment =
                    getenv("CHECKOUT_URL") . "/api/payment/antifraud/" . $statusType . "/" . $saleHashId;
            }

            $response = (new CheckoutService())->runCurl($urlCancelPayment, "POST");

            if ($response->status == "error") {
                return [
                    "status" => "error",
                    "message" => "Erro no antifraude.",
                ];
            }

            return [
                "status" => "success",
                "message" =>
                    "Venda " .
                    ($statusType === self::SALE_CANCEL ? "cancelada" : "autorizada") .
                    " pelo antifraude com sucesso!",
            ];
        } catch (Exception $ex) {
            report($ex);

            return [
                "status" => "error",
                "message" => "Erro ao " . ($statusType === self::SALE_CANCEL ? "cancelar" : "autorizar") . " venda!",
                "error" => $ex->getMessage(),
            ];
        }
    }

    public function getRelatedFraudedSales(Sale $sale): array
    {
        $relatedSales = [];

        foreach ($sale->antifraudWarnings as $antifraudWarning) {
            $warnings = AntifraudWarning::where("sale_id", "!=", $sale->id)
                ->where("column", $antifraudWarning->column)
                ->where("value", $antifraudWarning->value)
                ->orderBy("sale_id", "desc")
                ->get();
            foreach ($warnings as $warning) {
                if (!isset($relatedSales[$warning->sale_id])) {
                    $relatedSales[$warning->sale_id] = [];
                }
                $relatedSales[$warning->sale_id][$warning->column] = $this->columnNames[$warning->column];
            }
        }

        return array_map(
            function ($saleId, $warningData) {
                $sale = Sale::find($saleId . "");
                $holderName = "";
                $gatewayRequests = $sale
                    ->saleGatewayRequests()
                    ->orderBy("id", "desc")
                    ->get();
                foreach ($gatewayRequests as $gatewayRequest) {
                    $gatewayRequestData = json_decode($gatewayRequest->send_data ?? "{}");
                    $holderName = $gatewayRequestData->credit->card->cardholder_name ?? "";
                    if ($holderName) {
                        break;
                    }
                }
                return [
                    "sale_id" => $saleId,
                    "sale_code" => hashids_encode($saleId, "sale_id"),
                    "date" => $sale->start_date,
                    "card_holder" => $holderName,
                    "columns" => array_values($warningData),
                ];
            },
            array_keys($relatedSales),
            $relatedSales
        );
    }

    public function updateConfirmedFraudData(Sale $sale)
    {
        if (!$sale) {
            return;
        }
        try {
            foreach ($sale->saleInformations as $saleInformation) {
                foreach ($this->importantSaleInformationData as $column => $warningLevel) {
                    $sale->update(["antifraud_warning_level" => $warningLevel]);
                    if ($saleInformation->$column !== null) {
                        $confirmedFraud = AntifraudWarning::firstOrCreate(
                            [
                                "sale_id" => $saleInformation->sale_id,
                                "column" => $column,
                                "value" => $saleInformation->$column,
                            ],
                            [
                                "sale_id" => $saleInformation->sale_id,
                                "status" => AntifraudWarning::STATUS_FRAUD_CONFIRMED,
                                "column" => $column,
                                "value" => $saleInformation->$column,
                                "level" => $warningLevel,
                            ]
                        );
                        $confirmedFraud->save();
                    }
                }
            }

            AntifraudWarning::where("sale_id", $sale->id)->update([
                "status" => AntifraudWarning::STATUS_FRAUD_CONFIRMED,
            ]);
        } catch (Exception $e) {
            report($e);
        }
    }
}
