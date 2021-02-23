<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;

class TaskService
{
    const CHECK_COMPLETED_TASK_METHODS = [
        Task::TASK_APPROVED_DOCS      => 'validateApprovedDocsTask',
        Task::TASK_CREATE_FIRST_STORE => 'validateCreateFirstStoreTask',
        Task::TASK_FIRST_SALE         => 'validateFirstSaleTask',
        Task::TASK_FIRST_1000_REVENUE => 'validateFirst1000RevenueTask',
        Task::TASK_500_SALES          => 'validateFirst500SalesTask',
        Task::TASK_5_INVITATIONS      => 'validate5InvitationsSentTask',
    ];

    public function checkUserCompletedTasks(User $user)
    {
        foreach (Task::all() as $task) {
            $this->checkCompletedTask($user, $task);
        }
    }

    public function checkCompletedTask(User $user, Task $task): bool
    {
        $userTask = $user->tasks->where(['task_id', $task->id])->first();
        if ($userTask) {
            return true;
        }

        $methodName = TaskService::CHECK_COMPLETED_TASK_METHODS[$task->id];
        if ($this->{$methodName}($user) && !$user->tasks()->where('id', $task->id)->count()) {
            $user->tasks()->attach($task);
            $user->update();
            return true;
        }

        return false;
    }

    public function getUserTasks(User $user)
    {
        $userTasks = $user->tasks();
        $completedTasks = [];
        $uncompletedTasks = [];

        foreach ((new Task)->where('level', $user->level)->get() as $task) {
            if (in_array($task->id, $userTasks->pluck('id')->toArray())) {
                $task->status = 1;
                $completedTasks[] = $task;
            } else {
                $task->status = 0;
                $uncompletedTasks[] = $task;
            }
        }

        return array_merge($uncompletedTasks, $completedTasks);
    }

    private function validateApprovedDocsTask(User $user): bool
    {
        return $user->account_is_approved;
    }

    private function validateCreateFirstStoreTask(User $user): bool
    {
        return $user->companies()->with('usersProjects', 'projects')->whereHas('usersProjects', function ($q) {
            $q->where('status', (new Project)->present()->getStatus('active'));
        })->count();
    }

    private function validateFirstSaleTask(User $user)
    {
        $gatewayIds = FoxUtils::isProduction() ? [15] : [14, 15];
        return Sale::whereIn('gateway_id', $gatewayIds)
                ->whereIn('status', [
                    Sale::STATUS_APPROVED,
                    Sale::STATUS_CHARGEBACK,
                    Sale::STATUS_REFUNDED,
                    Sale::STATUS_IN_DISPUTE
                ])->where('owner_id', $user->id)->count() > 0;
    }

    private function validateFirst1000RevenueTask(User $user): bool
    {
        $transactionModel = new Transaction;
        $transactionPresent = $transactionModel->present();
        $revenue = $transactionModel->join('companies', 'companies.id', 'transactions.company_id')
            ->whereIn('transactions.status_enum', [$transactionPresent->getStatusEnum('paid'), $transactionPresent->getStatusEnum('transfered')])
            ->where('companies.user_id', $user->id)
            ->groupBy('companies.user_id')
            ->selectRaw('companies.user_id, SUM(transactions.value) as value')->first();

        return $revenue && $revenue->value >= 100000;
    }

    private function validateFirst500SalesTask(User $user): bool
    {
        $gatewayIds = FoxUtils::isProduction() ? [15] : [14, 15];
        return Sale::whereIn('gateway_id', $gatewayIds)
                ->whereIn('status', [
                    Sale::STATUS_APPROVED,
                    Sale::STATUS_CHARGEBACK,
                    Sale::STATUS_REFUNDED,
                    Sale::STATUS_IN_DISPUTE
                ])->where('owner_id', $user->id)->count() >= 500;
    }

    private function validate5InvitationsSentTask(User $user): bool
    {
        return Invitation::with('company')->whereIn('company_id', $user->companies()->pluck('id'))->count() >= 5;
    }
}
