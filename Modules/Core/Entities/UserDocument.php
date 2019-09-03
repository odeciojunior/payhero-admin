<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $user_id
 * @property string $document_url
 * @property boolean $document_type_enum
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 */
class UserDocument extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 
        'document_url', 
        'document_type_enum', 
        'status', 
        'created_at', 
        'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User');
    }
}
