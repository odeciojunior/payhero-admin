<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UtmifyLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "utmify_logs";

    protected $fillable = ["sale_id", "request", "response"];

    protected $casts = [
        "request" => "array",
        "response" => "array",
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
