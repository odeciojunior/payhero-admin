<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Company;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;

class TaskService
{
    const CHECK_COMPLETED_TASK_METHODS = [
        Task::TASK_APPROVED_DOCS      => 'validateApprovedDocsTask',
        Task::TASK_CREATE_FIRST_STORE => 'validateCreateFirstStoreTask',
        Task::TASK_DOMAIN_APPROVED    => 'validateDomainApprovedTask',
        Task::TASK_FIRST_SALE         => 'validateFirstSaleTask',
        Task::TASK_FIRST_1000_REVENUE => 'validateFirst1000RevenueTask',
        Task::TASK_FIRST_WITHDRAWAL   => 'validateFirstWithdrawalTask',
    ];

    private $tasks;

    public function __construct()
    {
        $this->tasks = Task::all();
    }

    public function checkUserCompletedTasks(User $user)
    {
        foreach ($this->tasks as $task) {
            $this->checkCompletedTask($user, $task);
        }
    }

    public function checkCompletedTask(User $user, Task $task): bool
    {
        $userTask = $user->tasks->where('id', $task->id)->first();
        if ($userTask) {
            return true;
        }

        $methodName = TaskService::CHECK_COMPLETED_TASK_METHODS[$task->id];
        if ($this->{$methodName}($user)) {
            return $this->setCompletedTask($user, $task);
        }

        return false;
    }

    public static function setCompletedTask(User $user, Task $task): bool
    {
        try {
            $user->tasks()->attach($task);
            $user->update();
            //TODO: notification here
            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    public function getCurrentUserTasks(User $user): array
    {
        $tasks = (new Task)->select('id', 'name', 'level', 'priority')->where('level', $user->level)->orderBy('priority')->get();
        $userTasks = $user->tasks();
        $completedTasks = [];
        $uncompletedTasks = [];

        foreach ($tasks as $task) {
            if (in_array($task->id, $userTasks->pluck('id')->toArray())) {
                $task->status = 1;
                $completedTasks[] = $task;
            } else {
                $task->status = 0;
                $uncompletedTasks[] = $task;
            }
        }

        $currentTasks = [];
        if (count($completedTasks) > 0 && count($completedTasks) < count($tasks)) {
            if (count($uncompletedTasks) >= 2) {
                $currentTasks[] = $completedTasks[count($completedTasks) - 1];
                $currentTasks[] = $uncompletedTasks[0];
                $currentTasks[] = $uncompletedTasks[1];
            } else {
                $currentTasks[] = $completedTasks[count($completedTasks) - 1];
                $currentTasks[] = $completedTasks[count($completedTasks) - 2];
                $currentTasks[] = $uncompletedTasks[0];
            }
        }
        elseif (empty($uncompletedTasks) ) {
            $currentTasks = $completedTasks;
        }
        else {
            $currentTasks = array_slice($uncompletedTasks, 0, 3);
        }

        return $currentTasks;
    }

    private function validateApprovedDocsTask(User $user): bool
    {
        return $user->account_is_approved;
    }

    private function validateCreateFirstStoreTask(User $user): bool
    {
        return $user->projects()->count() > 0;
    }

    private function validateDomainApprovedTask(User $user): bool
    {
        return $user->projects()->whereHas('domains', function ($query) {
                $query->where('domains.status', (new Domain())->present()->getStatus('approved'));
            })->count() > 0;
    }

    private function validateFirstSaleTask(User $user)
    {
        return Transaction::where('user_id', $user->id)->limit(1)->count() > 0;
    }

    private function validateFirst1000RevenueTask(User $user): bool
    {
        $transactionModel = new Transaction;
        $transactionPresent = $transactionModel->present();
        $revenue = $transactionModel
            ->whereIn('status_enum', [$transactionPresent->getStatusEnum('paid'), $transactionPresent->getStatusEnum('transfered')])
            ->where('user_id', $user->id)
            ->groupBy('user_id')
            ->selectRaw('user_id, SUM(transactions.value) as value')->first();

        return $revenue && $revenue->value >= 100000;
    }

    private function validateFirstWithdrawalTask(User $user): bool
    {
        return Company::whereHas('withdrawals', function ($query) {
                //$query->where('is_released', true);
                $query->where('status', 3);
            })->where('user_id', $user->id)->count() > 0;
    }
}
