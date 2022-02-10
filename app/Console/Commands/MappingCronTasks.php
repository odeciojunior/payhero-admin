<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Services\FoxUtils;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

class MappingCronTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudfox:report-cron-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lista carga de tarefas por hora';

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

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            $cronTasks = $this->getCronTask();

            $total = 0;
            $dateFormat = '00:00';
            $commands = [];
            for ($hour=0; $hour < 24; $hour++) {
                $total = 0;
                $commands = [];
                for ($min=0; $min < 60; $min++) {
                    $total = 0;
                    $commands = [];
                    $dateFormat = str_pad($hour,2,'0',STR_PAD_LEFT).':'.str_pad($min,2,'0',STR_PAD_LEFT);
                    foreach($cronTasks as $task){
                        switch($task['frequently']){
                            case 'Min':
                                if($task['interval']>0){
                                    if($min % $task['interval'] == 0){
                                        $total++;
                                        $commands[] = $task['command'];
                                    }
                                }elseif($dateFormat == str_pad($task['hour'],2,'0',STR_PAD_LEFT).':'.str_pad($task['min'],2,'0',STR_PAD_LEFT)){
                                    $total++;
                                    $commands[] = $task['command'];
                                }
                                break;
                            case 'Hour':
                                if($task['interval']>0){
                                    if($hour % $task['interval'] == 0 && $min == $task['min']){
                                        $total++;
                                        $commands[] = $task['command'];
                                    }
                                }elseif($dateFormat == str_pad($task['hour'],2,'0',STR_PAD_LEFT).':'.str_pad($task['min'],2,'0',STR_PAD_LEFT)){
                                    $total++;
                                    $commands[] = $task['command'];
                                }
                                break;
                            case 'Day':
                                if($dateFormat == str_pad($task['hour'],2,'0',STR_PAD_LEFT).':'.str_pad($task['min'],2,'0',STR_PAD_LEFT)){
                                    $total++;
                                    $commands[] = $task['command'];
                                }
                                break;
                        }
                    }
                    if($hour==5){
                        Log::info(['time'=>$dateFormat,'total'=>$total,'commands'=>$commands]);
                    }
                }
            }

        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }

    public function getCronTask()
    {
        $schedulesTasks = $this->getScheduledJobs()->sortByDesc('expression');

        $cronTasks = [];
        $i = 0;
        foreach($schedulesTasks as $task){
            $i++;
            // \Log::info(
            //     str_pad($task->expression,15,' ',STR_PAD_RIGHT).
            //     $task->command
            // );

            $cron = explode(' ',$task->expression);

            $cronTask = [
                'command'=>explode(' ',$task->command)['2'],
                'min'=>0,
                'hour'=>0,
                'interval'=>1,
                'frequently'=>'Day'
            ];

            if($cron['0']=='*' && $cron['1'] == '*'){
                $cronTask[ 'interval'] = 1;
                $cronTask[ 'frequently'] = 'Min';
            }else{
                if(is_numeric($cron['0'])){
                    $cronTask[ 'min'] = (int) $cron['0'];
                    if($cronTask[ 'min'] == 0){
                        $cronTask[ 'frequently'] = 'Hour';
                    }
                }else{
                    if(str_contains($cron['0'],'*/')){
                        $cronTask[ 'interval'] = (int) FoxUtils::onlyNumbers($cron['0']);
                        $cronTask[ 'frequently'] = 'Min';
                    }elseif(str_contains($cron['0'],',')){
                        $cronTask[ 'interval'] = (int) explode(',',$cron['0'])['1'];
                        $cronTask[ 'frequently'] = 'Min';
                    }
                }

                if(is_numeric($cron['1'])){
                    $cronTask[ 'hour'] = (int) $cron['1'];
                    $cronTask[ 'frequently'] = 'Day';
                }else{
                    if(str_contains($cron['1'],'*/')){
                        $cronTask[ 'interval'] = (int) FoxUtils::onlyNumbers($cron['1']);
                        $cronTask[ 'frequently'] = 'Hour';
                    }elseif(str_contains($cron['1'],',')){
                        $cronTask[ 'interval'] = (int) explode(',',$cron['1'])['1'];
                        $cronTask[ 'frequently'] = 'Day';
                    }
                }
            }

            $cronTasks[] = $cronTask;

        }
        return $cronTasks;
    }

    public function getScheduledJobs()
    {
        new \App\Console\Kernel(app(), new Dispatcher());
        $schedule = app(Schedule::class);
        $scheduledCommands = collect($schedule->events());

        return $scheduledCommands;
    }


    public function mapManuallyCronTasks(){
        $tasks = $this->getManuallyCronTasks();
        $total = 0;
        $dateFormat = '00:00';
        $commands = [];
        for ($hour=0; $hour < 24; $hour++) {
            $total = 0;
            $commands = [];
            for ($min=0; $min < 60; $min++) {
                $total = 0;
                $commands = [];
                $dateFormat = str_pad($hour,2,'0',STR_PAD_LEFT).':'.str_pad($min,2,'0',STR_PAD_LEFT);
                foreach($tasks as $task){
                    switch($task['frequently']){
                        case 'min':
                            if($task['repeat']>=60){
                                $inteiro = (int) $task['repeat']/60;
                                if($hour % $inteiro == 0 && $min == (int) $inteiro - ($task['repeat']/60)){
                                    $total++;
                                    $commands[] = $task['command'];
                                }
                            }else{
                                if($min % $task['repeat'] == 0){
                                    $total++;
                                    $commands[] = $task['command'];
                                }
                            }
                        break;
                        case 'day':
                            if( $dateFormat == $task['time']){
                                $total++;
                                $commands[] = $task['command'];
                            }
                        break;
                    }
                }
                if($total>10){
                    \Log::info(['time'=>$dateFormat,'total'=>$total,'commands'=>$commands]);
                }
            }

        }

    }

    public function getManuallyCronTasks()
    {
        return [
            ['command'=>'command:WoocommerceReorderSales','time'=>'03:45','repeat'=>1,'frequently'=>'day'],
            ['command'=>'user:benefits:update','time'=>'09:30','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:release-unblocked-balance','time'=>'02:30','repeat'=>1,'frequently'=>'day'],
            ['command'=>'tasks:check-completed-sales-tasks','time'=>'00:30','repeat'=>1,'frequently'=>'day'],
            ['command'=>'tasks:check-completed-sales-tasks','time'=>'06:30','repeat'=>1,'frequently'=>'day'],
            ['command'=>'tasks:check-completed-sales-tasks','time'=>'10:30','repeat'=>1,'frequently'=>'day'],
            ['command'=>'tasks:check-completed-sales-tasks','time'=>'14:30','repeat'=>1,'frequently'=>'day'],
            ['command'=>'tasks:check-completed-sales-tasks','time'=>'21:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'tasks:check-completed-sales-tasks','time'=>'22:30','repeat'=>1,'frequently'=>'day'],
            ['command'=>'change:boletopendingtocanceled','time'=>'06:30','repeat'=>1,'frequently'=>'day'],
            ['command'=>'check:gateway-tax-company-after-month','time'=>'06:30','repeat'=>1,'frequently'=>'day'],
            ['command'=>'verify:promotional-tax','time'=>'23:30','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:check-withdrawals-released','time'=>'09:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:check-withdrawals-released','time'=>'12:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:check-withdrawals-released','time'=>'16:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:check-withdrawals-released','time'=>'22:30','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:check-withdrawals-liquidated-cloudfox','time'=>'22:30','repeat'=>1,'frequently'=>'day'],
            ['command'=>'user:benefits:update','time'=>'22:30','repeat'=>1,'frequently'=>'day'],
            ['command'=>'verify:boletopaid','time'=>'10:30','repeat'=>1,'frequently'=>'day'],
            ['command'=>'asaas:transfers-chargebacks','time'=>'00:20','repeat'=>1,'frequently'=>'day'],
            ['command'=>'available-balance:update','time'=>'06:15','repeat'=>1,'frequently'=>'day'],
            ['command'=>'command:WoocommerceRetryFailedRequests','time'=>'04:15','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:check-refunded','time'=>'03:15','repeat'=>1,'frequently'=>'day'],
            ['command'=>'command:update-user-level','time'=>'11:15','repeat'=>1,'frequently'=>'day'],
            ['command'=>'verify:boleto2','time'=>'11:15','repeat'=>1,'frequently'=>'day'],
            ['command'=>'check:automatic-withdrawals','time'=>'03:10','repeat'=>1,'frequently'=>'day'],
            ['command'=>'withdrawals:release-get-faster','time'=>'00:00','repeat'=>30,'frequently'=>'min'],
            ['command'=>'generate:notazzinvoicessalesapproved','time'=>'00:00','repeat'=>30,'frequently'=>'min'],
            ['command'=>'verify:pendingnotazzinvoices','time'=>'00:00','repeat'=>30,'frequently'=>'min'],
            ['command'=>'check:underattack','time'=>'00:00','repeat'=>30,'frequently'=>'min'],
            ['command'=>'account-health:update','time'=>'09:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'account-health:update','time'=>'22:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'achievements:update','time'=>'09:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'achievements:update','time'=>'21:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'asaas:transfers-surplus-balance','time'=>'08:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'woocommerce:check-tracking-codes','time'=>'07:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:get-all-statement-chargebacks','time'=>'07:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'under-attack:update-card-declined','time'=>'05:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'command:validateLastDomains','time'=>'04:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'command:deleteTemporaryFiles','time'=>'04:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:update-confirm-date-debt-pending','time'=>'04:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'command:ShopifyReorderSales','time'=>'03:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:check-withdrawals-released-cloudfox','time'=>'22:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:check-withdrawals-liquidated','time'=>'10:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:check-withdrawals-liquidated','time'=>'13:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:check-withdrawals-liquidated','time'=>'17:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:check-withdrawals-liquidated','time'=>'21:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:check-withdrawals-liquidated','time'=>'22:30','repeat'=>1,'frequently'=>'day'],
            ['command'=>'account-health:user:update-average-response-time','time'=>'02:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:import-sale-contestations','time'=>'17:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'check:menv-tracking','time'=>'17:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'getnet:import-sale-contestations-txt-format','time'=>'16:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'asaas:anticipations-pending','time'=>'16:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'verify:trackingWithoutInfo','time'=>'15:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'update:currencyquotation','time'=>'14:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'verify:abandonedcarts2','time'=>'12:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'verify:boletoexpiring','time'=>'11:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'verify:boletowaitingpayment','time'=>'10:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'check:has-valid-tracking','time'=>'01:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'verify:inviteexpired','time'=>'01:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'whiteblacklist:verifyexpires','time'=>'00:00','repeat'=>1,'frequently'=>'day'],
            ['command'=>'command:checkUpdateCompanyGetnet','time'=>'00:00','repeat'=>240,'frequently'=>'min'],
            ['command'=>'command:UpdateListsFoxActiveCampaign','time'=>'00:00','repeat'=>720,'frequently'=>'min'],
            ['command'=>'antifraud:backfill-asaas-chargebacks','time'=>'00:00','repeat'=>60,'frequently'=>'min'],
            ['command'=>'redis:update-sale-tracking','time'=>'00:00','repeat'=>60,'frequently'=>'min'],
            ['command'=>'verify:pendingdomains','time'=>'00:00','repeat'=>60,'frequently'=>'min'],
            ['command'=>'updateTransactionsReleaseDate','time'=>'00:00','repeat'=>60,'frequently'=>'min'],
            ['command'=>'gatewaypostbacks:process','time'=>'00:00','repeat'=>5,'repeat'=>1,'frequently'=>'min'],
            ['command'=>'verify:abandonedcarts','time'=>'00:00','repeat'=>15,'repeat'=>1,'frequently'=>'min'],
            ['command'=>'horizon:snapshot','time'=>'00:00','repeat'=>15,'repeat'=>1,'frequently'=>'min'],
            ['command'=>'check:systems','time'=>'00:00','repeat'=>10,'repeat'=>1,'frequently'=>'min'],
            ['command'=>'change:pix-to-canceled','time'=>'00:00','repeat'=>1,'frequently'=>'min']
        ];
    }
}
