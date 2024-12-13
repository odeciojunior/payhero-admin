<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\Task;

class TasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Task::all() as $task) {
            $task->delete();
        }

        $tasks = [
            [Task::TASK_APPROVED_DOCS, "Tenha seus documentos aprovados", 1, 1],
            [Task::TASK_CREATE_FIRST_STORE, "Cadastre sua primeira loja", 1, 2],
            [Task::TASK_DOMAIN_APPROVED, "Aprove seu primeiro domínio", 1, 3],
            [Task::TASK_FIRST_SALE, "Faça sua primeira venda", 1, 4],
            [Task::TASK_FIRST_1000_REVENUE, 'Fature R$1.000,00', 1, 5],
            [Task::TASK_FIRST_WITHDRAWAL, "Faça seu primeiro saque", 1, 6],
        ];

        foreach ($tasks as $taskData) {
            $task = new Task();
            $task->id = $taskData[0];
            $task->name = $taskData[1];
            $task->level = $taskData[2];
            $task->priority = $taskData[3];
            $task->save();
        }
    }
}
