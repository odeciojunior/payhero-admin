<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\User;
use Modules\Core\Services\UserService;

class CheckDocumentOnBureau extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "antifraud:check-document-on-bureau";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $usersQuery = User::query()
                ->select(["id", "document"])
                ->whereRaw('(JSON_CONTAINS(bureau_result,\'[]\') OR bureau_result IS NULL)')
                ->where("email", "NOT LIKE", "%@cloudfox.net%")
                ->where("created_at", ">=", "2022-01-01 00:00:00")
                ->where("bureau_check_count", "<=", 3)
                ->where(function ($query) {
                    return $query
                        ->whereRaw("DATEDIFF(now(), bureau_data_updated_at) > 0")
                        ->orWhereNull("bureau_data_updated_at");
                })
                ->limit(50);

            $userService = new UserService();
            foreach ($usersQuery->get() as $user) {
                $this->line($user->id . " / " . $user->document);
                $userService->updateUserDataFromBureau($user->document);
            }
        } catch (Exception $e) {
            report($e);
            return 1;
        }
        return 0;
    }
}
