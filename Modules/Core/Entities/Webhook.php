<?php

declare(strict_types=1);

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Webhook
 * @package Modules\Core\Entities
 *
 * @property int $id
 * @property int $user_id
 * @property int $company_id
 * @property string $description
 * @property string $url
 * @property string $signature
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 * @property Company $company
 */
class Webhook extends Model
{
    use SoftDeletes;

    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'description',
        'url',
        'signature',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
