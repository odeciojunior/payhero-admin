<?php

namespace Modules\Sales\Exports\Reports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\CheckoutService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;

class AbandonedCartReportExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    use Exportable;

    private $filters;
    private $user;
    private $filename;
    private $email;

    public function __construct($filters, $user, $filename)
    {
        $this->filters = $filters;
        $this->user = $user;
        $this->filename = $filename;
        $this->email = !empty($filters["email"]) ? $filters["email"] : $user->email;
    }

    public function query()
    {
        $domainModel = new Domain();

        $dateRange = FoxUtils::validateDateRange($this->filters["date_range"]);
        $startDate = null;
        $endDate = null;

        if (!empty($dateRange) && $dateRange) {
            $startDate = $dateRange[0] . " 00:00:00";
            $endDate = $dateRange[1] . " 23:59:59";
        }

        $abandonedCarts = Checkout::whereIn("status_enum", [
            Checkout::STATUS_RECOVERED,
            Checkout::STATUS_ABANDONED_CART,
        ]);

        $parseProjectIds = explode(",", $this->filters["project"]);
        $projectIds = [];
        if (!empty($parseProjectIds) && !in_array("all", $parseProjectIds)) {
            foreach ($parseProjectIds as $projectId) {
                array_push($projectIds, FoxUtils::decodeHash($projectId));
            }
            $abandonedCarts->whereIn("project_id", $projectIds);
        } else {
            $userProjects = UserProject::select("project_id")
                ->where("user_id", $this->user->account_owner_id)
                ->where("type_enum", UserProject::TYPE_PRODUCER_ENUM)
                ->get()
                ->pluck("project_id")
                ->toArray();
            $abandonedCarts->whereIn("project_id", $userProjects);
        }

        if (!empty($this->filters["client"])) {
            $abandonedCarts->where("client_name", "like", "%" . $this->filters["client"] . "%");
        }

        if (!empty($this->filters["client_document"])) {
            $document = $this->filters["client_document"];
            $abandonedCarts->whereHas("logs", function ($query) use ($document) {
                $query->where("document", $document);
            });
        }

        $parsePlanIds = explode(",", $this->filters["plan"]);
        $planIds = [];
        if (!empty($parsePlanIds) && !in_array("all", $parsePlanIds)) {
            foreach ($parsePlanIds as $planId) {
                array_push($planIds, Hashids::decode($planId));
            }
            $abandonedCarts->whereHas("checkoutPlans", function ($query) use ($planIds) {
                $query->whereIn("plan_id", $planIds);
            });
        }

        if (!empty($startDate) && !empty($endDate)) {
            $abandonedCarts->whereBetween("checkouts.created_at", [$startDate, $endDate]);
        } else {
            if (!empty($startDate)) {
                $abandonedCarts->whereDate("checkouts.created_at", ">=", $startDate);
            }
            if (!empty($endDate)) {
                $abandonedCarts->whereDate("checkouts.created_at", "<", $endDate);
            }
        }

        return $abandonedCarts
            ->with([
                "project.domains" => function ($query) use ($domainModel) {
                    $query->where("status", $domainModel->present()->getStatus("approved"));
                },
                "checkoutPlans.plan",
            ])
            ->orderBy("id", "DESC");
    }

    public function map($row): array
    {
        $checkout = $row;
        $checkoutService = new CheckoutService();

        $cart = [
            "date" => with(new Carbon($checkout->created_at))->format("d/m/Y H:i:s"),
            "project" => $checkout->project->name,
            "client" => $checkout->client_name,
            "email" => $checkout->logs->whereNotIn("email", [null, ""])->first()->email ?? "",
            "telephone" => $checkout->client_telephone,
            "status_translate" => $checkout->status == "abandoned cart" ? "Não recuperado" : "Recuperado",
            "value" => number_format(
                intval(preg_replace("/[^0-9]/", "", $checkoutService->getSubTotal($checkout->checkoutPlans))) / 100,
                2,
                ",",
                "."
            ),
            "link" => $checkout
                ->present()
                ->getCheckoutLink($checkout->project->domains->first(), $this->user->company_default),
            "whatsapp_link" =>
                "https://api.whatsapp.com/send?phone=+55" .
                preg_replace("/[^0-9]/", "", $checkout->client_telephone) .
                "&text=Olá " .
                explode(" ", $checkout->name)[0],
        ];

        //remove caracteres indesejados em todos os campos
        return array_map(function ($item) {
            return preg_replace('/[^\w\s\p{P}\p{Latin}$]+/u', "", $item);
        }, $cart);
    }

    public function headings(): array
    {
        return ["Data", "Projeto", "Cliente", "Email", "Telefone", "Status", "Valor", "Link", "Link Whatsapp"];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = "A1:AS1"; // All headers
                $event->sheet
                    ->getDelegate()
                    ->getStyle($cellRange)
                    ->getFill()
                    ->setFillType("solid")
                    ->getStartColor()
                    ->setRGB("E16A0A");
                $event->sheet
                    ->getDelegate()
                    ->getStyle($cellRange)
                    ->getFont()
                    ->applyFromArray([
                        "color" => ["rgb" => "ffffff"],
                        "size" => 16,
                    ]);

                $lastRow = $event->sheet->getDelegate()->getHighestRow();
                $setGray = false;
                $lastSale = null;
                for ($row = 2; $row <= $lastRow; $row++) {
                    $currentSale = $event->sheet
                        ->getDelegate()
                        ->getCellByColumnAndRow(1, $row)
                        ->getValue();
                    if ($currentSale != $lastSale && isset($lastSale)) {
                        $setGray = !$setGray;
                    }
                    if ($setGray) {
                        $event->sheet
                            ->getDelegate()
                            ->getStyle("A" . $row . ":AS" . $row)
                            ->getFill()
                            ->setFillType("solid")
                            ->getStartColor()
                            ->setRGB("e5e5e5");
                    }
                    $lastSale = $currentSale;
                }

                $sendGridService = new SendgridService();
                $userName = $this->user->name;
                $userEmail = $this->email;
                $downloadLink = getenv("APP_URL") . "/sales/download/" . $this->filename;

                $data = [
                    "name" => $userName,
                    "report_name" => "Relatório de Recuperação",
                    "download_link" => $downloadLink,
                ];

                $sendGridService->sendEmail(
                    "noreply@azcend.com.br",
                    "Azcend",
                    $userEmail,
                    $userName,
                    "d-b999b01727f14c84adf4fceab77c5d3d", /// done
                    $data
                );
            },
        ];
    }
}
