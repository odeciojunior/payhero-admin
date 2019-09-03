<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $project_id
 * @property string $cloudflare_domain_id
 * @property string $name
 * @property int $status
 * @property string $sendgrid_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 * @property DomainsRecord[] $domainsRecords
 */
class Domain extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['project_id', 'cloudflare_domain_id', 'name', 'status', 'sendgrid_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Entities\Project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domainsRecords()
    {
        return $this->hasMany('App\Entities\DomainsRecord');
    }
}
