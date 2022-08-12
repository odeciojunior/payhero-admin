<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleContestationFile extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = ["user_id", "contestation_sale_id", "type", "file", "created_at", "updated_at", "deleted_at"];

    /**
     * @return BelongsTo
     */
    public function contestation()
    {
        return $this->belongsTo(SaleContestation::class, "contestation_sale_id", "id");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
