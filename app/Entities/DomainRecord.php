<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $domain_id
 * @property string $type
 * @property string $name
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 * @property Domain $domain
 */
class DomainRecord extends Model
{
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'domains_records';
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'domain_id',
        'cloudflare_record_id',
        'type',
        'name',
        'content',
        'system_flag',
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain()
    {
        return $this->belongsTo('App\Entities\Domain');
    }
}
