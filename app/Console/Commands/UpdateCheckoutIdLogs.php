<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Log;
use DB;

class UpdateCheckoutIdLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:UpdateCheckoutIdLogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza checkout_id da tabela logs onde for null';

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
     * @return mixed
     */
    public function handle()
    {
        try {

            $logs = Log::select('id', DB::Raw('(SELECT id FROM checkouts WHERE id_log_session = logs.id_log_session) as checkout_id'))
                                ->limit(100)->whereNull('checkout_id')->get();

            foreach ($logs as $log) {
                if(!is_null($log->checkout_id)) {
                    DB::unprepared('UPDATE logs SET checkout_id = '.$log->checkout_id.' WHERE id = ' . $log->id );
                }
            }
               
        } catch (Exception $e) {
            report($e);
        }
    }
}
