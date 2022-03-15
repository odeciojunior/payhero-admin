<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Sale;

class TestRedisStatementCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TestRedisStatement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa se o redis do REDIS_STATEMENT_HOST está ok';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        try {

            $this->info('Redis: ' . env('REDIS_STATEMENT_HOST'));
            $sale_id = $this->ask('Qual o sale_id?');
            $sale = Sale::find($sale_id);

            if ($sale) {

                $this->info('Verificando o $sale_id = ' . $sale_id);
                $this->info('  has_valid_tracking => ' . $sale->has_valid_tracking);

                $hasValidTracking = Redis::connection('redis-statement')->get("sale:has:tracking:{$sale->id}");
                $this->info('  No Redis está => ' . $hasValidTracking);

                $this->info('  Tentando setar para o mesmo valor = ' . $sale->has_valid_tracking);
                Redis::connection('redis-statement')->set("sale:has:tracking:{$sale->id}", $sale->has_valid_tracking);

                $hasValidTracking = Redis::connection('redis-statement')->get("sale:has:tracking:{$sale->id}");
                $this->info('  Lendo novamente do Redis => ' . $hasValidTracking);
            } else {

                $this->error('Venda não encontrada');
            }

        } catch (Exception $e) {
            report($e);
        }

    }
}
