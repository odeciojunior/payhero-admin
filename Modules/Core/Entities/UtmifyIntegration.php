<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $project_id
 * @property string $token
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property Project $project
 */
class UtmifyIntegration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ["user_id", "project_id", "token"];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
