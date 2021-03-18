<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\User;
use Modules\Core\Services\AchievementService;

class UpdateUserAchievements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'achievements:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates user\'s achievements';

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
        $achievementService = new AchievementService();
        $now = now();
        $users = User::with('achievements')
            ->whereRaw('id = account_owner_id')
            ->whereNull('deleted_at')
            ->get();

        foreach ($users as $user) {
            $this->line($user->id . ' - ' . $user->name);
            $achievementService->checkUserAchievements($user);
        }

        $this->line($now);
        $this->line(now());
        return 0;
    }
}
