<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

class DashboardNotification extends Model
{
    /**
     * @property int $id
     * @property int $user_id
     * @property int $subject_id
     * @property int $subject_type
     * @property string $read_at
     * @property string $created_at
     * @property string $updated_at
     * @property string $deleted_at
     */

    protected $fillable = [
        "user_id",
        "subject_id",
        "subject_type",
        "read_at",
        "created_at",
        "updated_at",
        "deleted_at",
    ];
}
