<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $project_id
 * @property string $description
 * @property int $discount
 * @property string $apply_on_plans
 * @property string $offer_plans
 * @property boolean $active_flag
 * @property string $created_at
 * @property string $updated_at
 * @property Project $project
 */
class OrderBumpRule extends Model
{
    protected $fillable = [
        'project_id',
        'description',
        'discount',
        'apply_on_plans',
        'offer_plans',
        'active_flag',
        'created_at',
        'updated_at'
    ];

    /**
     * Mutators
     */
    public function getApplyOnPlansAttribute($value)
    {
        return json_decode($value);
    }

    public function getOfferPlansAttribute($value)
    {
        return json_decode($value);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
