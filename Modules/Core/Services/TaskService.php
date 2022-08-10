<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\User;

class TaskService
{
    private $tasks;

    public function __construct()
    {
        $this->tasks = Task::all();
    }

    public function checkUserCompletedTasks(User $user)
    {
        foreach (Task::TASKS_CLASS as $id => $taskClass) {
            $task = $taskClass::find($id);
            $this->checkCompletedTask($user, $task);
        }
    }

    public function checkCompletedTask(User $user, Task $task): bool
    {
        $hasTask = $user->tasks->contains("id", $task->id);
        if ($hasTask) {
            return true;
        }

        if ($task->userCompletedTask($user)) {
            return $this->setCompletedTask($user, $task);
        }

        return false;
    }

    public static function setCompletedTask(User $user, Task $task): bool
    {
        try {
            /**
             * Task ids less than 5 (Task::TASK_FIRST_1000_REVENUE) will be filled as completed when
             * another task with id greater than its is completed
             */
            $user->load("tasks");
            for ($id = $task->id <= Task::TASK_FIRST_1000_REVENUE ? $task->id : 0; $id > 0; $id--) {
                if (!$user->tasks->contains($id)) {
                    $user->tasks()->attach(Task::find($id));
                }
            }
            $user->update();

            //Necessary tasks reloading
            $user->load("tasks");
            if (!$user->tasks->contains($task)) {
                $user->tasks()->attach($task);
                $user->update();
            }
            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    public function getCurrentUserTasks(User $user): array
    {
        $tasks = (new Task())
            ->select("id", "name", "level", "priority")
            ->where("level", $user->level)
            ->orderBy("priority")
            ->get();
        $userTasks = $user->tasks();
        $completedTasks = [];
        $uncompletedTasks = [];
        $userTasksIds = $userTasks->pluck('id')->toArray();

        foreach ($tasks as $task) {
            if (in_array($task->id, $userTasksIds)) {
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
        } elseif (empty($uncompletedTasks)) {
            $currentTasks = $completedTasks;
        } else {
            $currentTasks = array_slice($uncompletedTasks, 0, 3);
        }

        return $currentTasks;
    }
}
