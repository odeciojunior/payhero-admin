<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property string $name
 * @property string $site
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Delivery[] $deliveries
 * @property Project[] $projects
 */
class Carrier extends Model
{
    use SoftDeletes, LogsActivity;
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'site',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * @var bool
     */
    protected static $logFillable = true;
    /**
     * @var bool
     */
    protected static $logUnguarded = true;
    /**
     * Registra apenas os atributos alterados no log
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * Impede que armazene logs vazios
     * @var bool
     */
    protected static $submitEmptyLogs = false;

    /**
     * @return HasMany
     */
    public function deliveries()
    {
        return $this->hasMany('Modules\Core\Entities\Delivery');
    }

    /**
     * @return HasMany
     */
    public function projects()
    {
        return $this->hasMany('Modules\Core\Entities\Project');
    }
}
