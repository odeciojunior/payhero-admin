<?php

declare(strict_types=1);

namespace App\Console\Commands\CreateSellerAccount;

use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Modules\Core\Exceptions\GatewayNotFound;
use Modules\Core\Factories\GatewayFactory;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class CreateSellerAccount extends Command
{
    protected $signature = 'gateway:create-subseller {gatewayName}';
    protected $description = 'Create subseller accounts';

    private const CHUNK_SIZE = 60;

    /**
     * @throws GatewayNotFound
     */
    public function handle(): int
    {
        $gatewayService = GatewayFactory::make($this->argument('gatewayName'));
        $gatewayId = GatewayFactory::getGatewayId($this->argument('gatewayName'));

        $bankAccountsQuery = $this->getBankAccountsQuery($gatewayId);
        $progress = $this->getOutput()->createProgressBar($bankAccountsQuery->count());

        $bankAccountsQuery->chunk(self::CHUNK_SIZE, function ($bankAccounts) use ($gatewayService, $progress) {
            foreach ($bankAccounts as $bankAccount) {
                try {
                    $result = $gatewayService->createSubSellerAccount([
                        'companyId' => $bankAccount->company_id,
                    ]);
                    $this->info(json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
                } catch (Throwable $e) {
                    report($e);

                    $this->error(sprintf('Error: %s', $e->getMessage()));
                }

                sleep(1);
                $progress->advance();
            }
        });

        $progress->finish();
        $this->info('Subseller accounts created successfully');

        return CommandAlias::SUCCESS;
    }

    private function getBankAccountsQuery(int $gatewayId): Builder
    {
        return DB::table("company_bank_accounts as cba")
            ->select("cba.company_id")
            ->join("companies as c", "cba.company_id", "=", "c.id")
            ->leftJoin("gateways_companies_credentials as gcc", function (JoinClause $join) use ($gatewayId) {
                $join->on("cba.company_id", "=", "gcc.company_id")
                    ->where("gcc.gateway_id", $gatewayId);
            })
            ->whereNull("gcc.company_id")
            ->whereNotNull(DB::raw("json_extract(c.situation, '$.company_data')"))
            ->where("cba.transfer_type", "TED")
            ->where("cba.status", "VERIFIED")
            ->orderBy("cba.company_id");
    }
}
