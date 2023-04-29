<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SecurityReserve extends Model
{
    const STATUS_PENDING = 1;
    const STATUS_TRANSFERRED = 2;

    protected $keyType = "integer";

    protected $dates = ["release_date", "released_at", "created_at", "updated_at", "deleted_at"];

    protected $fillable = [
        "company_id",
        "sale_id",
        "transaction_id",
        "transfer_id",
        "user_id",
        "value",
        "status",
        "release_date",
        "released_at",
        "tax",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
