<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\NotazzService;

class GenerateRetroactiveNotazzInvoices extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'generate:retroactivenotazzinvoices';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Gerar as notas fiscais de vendas aprovadas com data inicial definida na integração com a notazz';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
//        try {
//
//            $notazzService = new NotazzService();
//
//            $notazzService->generateRetroactiveInvoices();
//        } catch (Exception $e) {
//            Log::warning('GenerateRetroactiveNotazzInvoices - Erro no command ');
//            report($e);
//        }
    }
}
