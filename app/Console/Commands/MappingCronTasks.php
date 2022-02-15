<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\FoxUtils;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Events\Dispatcher;

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
                if($hour==18){
                    \Log::info(['time'=>$dateFormat,'total'=>$total,'commands'=>$commands]);
                }
            }
        }
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


    
}
