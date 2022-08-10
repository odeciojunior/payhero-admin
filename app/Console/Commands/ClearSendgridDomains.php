<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Domain;
use Modules\Core\Services\SendgridService;

class ClearSendgridDomains extends Command
{
    protected $signature = "clear-sendgrid-domains";

    protected $description = "Clear old sendgrid domains";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $lastMonths = 3;

            $domainsWithSales = Domain::select("name")
                ->whereHas("project", function ($query) use ($lastMonths) {
                    $query->whereHas("sales", function ($query) use ($lastMonths) {
                        $query->whereDate("start_date", ">=", now()->subMonths($lastMonths));
                    });
                })
                ->get();

            $sendgrid = new SendgridService();

            $page = 0;
            $limit = 50;
            $totalZones = 0;

            do {
                $offset = $limit * $page;

                $zones = $sendgrid->getZones($limit, $offset, true);

                $totalZones = count($zones);

                foreach ($zones as $key => $zone) {
                    try {
                        $count = $offset + ($key + 1);
                        $total = $offset . "-" . ($offset + $totalZones) . ($totalZones === $limit ? "+" : "");
                        $this->line("Verificando domínio {$count} de {$total}: {$zone->domain}");
                        $domain = $domainsWithSales->where("name", $zone->domain)->first();
                        if ($domain) {
                            $this->info("Tem vendas nos últimos {$lastMonths} meses. Ignorando...");
                        } else {
                            $this->warn("Não tem vendas nos últimos {$lastMonths} meses! Excluindo...");
                            $sendgrid->deleteZone($zone->domain, true);
                            $sendgrid->deleteLinkBrand($zone->domain);
                        }
                    } catch (\Exception $e) {
                        $this->error("ERROR: " . $e->getMessage());
                    }
                }

                $page++;
            } while ($totalZones === $limit);
        } catch (Exception $e) {
            report($e);
        }
    }
}
