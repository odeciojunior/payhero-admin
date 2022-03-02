<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\User;
use Modules\Core\Services\AccountHealthService;

class UpdateUserAccountHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account-health:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates user\'s account health stats';

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
            $accountHealthService = new AccountHealthService();
            $users = User::whereRaw('id = account_owner_id')->get();
            foreach ($users as $user) {
                $this->line($user->id . ' - ' . $user->account_owner_id . ' - ' . $user->name);
                if (!$accountHealthService->updateAccountScore($user)) {
                    $outputMessage = 'Não existem transações suficientes até a data de '
                        . now()->format('d/m/Y')
                        . ' para calcular o score do usuário ' . $user->name . '.';
                    $this->line($outputMessage);
                }
            }
        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

        return 0;
    }
}
