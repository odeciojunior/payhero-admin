<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Entities\UserInformation;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class FixUsersAccountIsApproved extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "command:is-approved";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

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
        $users = User::orderBy("id", "desc")->get();

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, count($users));
        $progress->start();

        foreach ($users as $user) {
            $this->checkApprovedAccount($user);

            $progress->advance();
        }

        $progress->finish();

        $output->writeln(" Fim!!");
    }

    public function checkApprovedAccount(User $user)
    {
        if (
            $user->address_document_status == UserDocument::STATUS_APPROVED &&
            $user->personal_document_status == UserDocument::STATUS_APPROVED
        ) {
            $hasCompanyPfApproved = Company::where("user_id", $user->account_owner_id)
                ->where("active_flag", true)
                ->where("company_type", Company::PHYSICAL_PERSON)
                ->exists();

            $hasCompanyPjApproved = Company::where("user_id", $user->account_owner_id)
                ->where("active_flag", true)
                ->where("company_type", Company::JURIDICAL_PERSON)
                ->where("address_document_status", CompanyDocument::STATUS_APPROVED)
                ->where("contract_document_status", CompanyDocument::STATUS_APPROVED)
                ->exists();

            if ($user->id === $user->account_owner_id) {
                $userInformations = UserInformation::where("document", $user->document)->exists();
            } else {
                $userInformations = true;
            }

            if (($hasCompanyPjApproved || $hasCompanyPfApproved) && $userInformations) {
                DB::table("users")
                    ->where("id", $user->id)
                    ->update(["account_is_approved" => 1]);

                $this->line("  " . $user->id . "  -  " . $user->name . " atualizado - account_is_approved = 1");
            } else {
                DB::table("users")
                    ->where("id", $user->id)
                    ->update(["account_is_approved" => 0]);

                $this->line("  " . $user->id . "  -  " . $user->name . " atualizado - account_is_approved = 0");
            }
        } else {
            DB::table("users")
                ->where("id", $user->id)
                ->update(["account_is_approved" => 0]);

            $this->line("  " . $user->id . "  -  " . $user->name . " atualizado - account_is_approved = 0");
        }
    }
}
