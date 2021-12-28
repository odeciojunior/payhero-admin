<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $source_table
 * @property int $source_id
 * @property string $request
 * @property string $response
 * @property string $created_at
 * @property string $updated_at 
 */
class IntegrationLog extends Model
{
    protected $fillable = [
        'source_table',
        'source_id',
        'request',
        'response',
        'api',
        'created_at',
        'updated_at'        
    ];    
}
