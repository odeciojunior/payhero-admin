<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "api_logs";

    protected $fillable = ["sale_id", "request", "response", "error"];

    protected $casts = [
        "request" => "array",
        "response" => "array",
        "error" => "array",
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
