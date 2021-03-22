<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Core\Entities\Tasks;

/**
 * @property integer $id
 * @property string $name
 * @property integer $level
 * @property integer $priority
 * @property User $users
 * @property string $created_at
 * @property string $updated_at
 */
class Task extends Model
{
    const TASK_APPROVED_DOCS      = 1;
    const TASK_CREATE_FIRST_STORE = 2;
    const TASK_DOMAIN_APPROVED    = 3;
    const TASK_FIRST_SALE         = 4;
    const TASK_FIRST_1000_REVENUE = 5;
    const TASK_FIRST_WITHDRAWAL   = 6;

    const TASKS_CLASS = [
        Task::TASK_APPROVED_DOCS      => Tasks\ApprovedDocuments::class,
        Task::TASK_CREATE_FIRST_STORE => Tasks\CreateFirstStore::class,
        Task::TASK_DOMAIN_APPROVED    => Tasks\ApprovedDomain::class,
        Task::TASK_FIRST_SALE         => Tasks\FirstSale::class,
        Task::TASK_FIRST_1000_REVENUE => Tasks\First1000Revenue::class,
        Task::TASK_FIRST_WITHDRAWAL   => Tasks\FirstWithdrawal::class
    ];

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    protected $table = 'tasks';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'level',
        'priority',
        'created_at',
        'updated_at'
    ];

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'tasks_users',
            'task_id',
            'user_id'
        );
    }
}
