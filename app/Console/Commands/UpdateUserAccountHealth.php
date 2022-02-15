<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
        $accountHealthService = new AccountHealthService();

        foreach (User::whereRaw('id = account_owner_id')->get() as $user) {
            $this->line($user->id . ' - ' . $user->account_owner_id . ' - ' . $user->name);
            if (!$accountHealthService->updateAccountScore($user)) {
                $this->line('Não existem transações suficientes até a data de ' . now()->format('d/m/Y') . ' para calcular o score do usuário ' . $user->name . '.');
            }
        }

        return 0;
    }
}
