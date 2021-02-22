<?php

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
            [Task::TASK_APPROVED_DOCS,      'Tenha seus documentos aprovados', 1],
            [Task::TASK_CREATE_FIRST_STORE, 'Cadastre sua primeira loja',      1],
            [Task::TASK_FIRST_SALE,         'Faça sua primeira venda',         1],
            [Task::TASK_FIRST_1000_REVENUE, 'Fature R$1.000',                  1],
            [Task::TASK_500_SALES,          'Faça mais de 500 vendas',         2],
            [Task::TASK_5_INVITATIONS,      'Faça 5 convites',                 2],
        ];

        foreach ($tasks as $taskData) {
            $task = new Task();
            $task->id    = $taskData[0];
            $task->name  = $taskData[1];
            $task->level = $taskData[2];
            $task->save();
        }
    }
}
