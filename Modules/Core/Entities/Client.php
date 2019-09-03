<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property string $name
 * @property string $document
 * @property string $email
 * @property string $telephone
 * @property integer $id_kapsula_client
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Sale[] $sales
 */
class Client extends Model
{

    use SoftDeletes;

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
        'name', 
        'document', 
        'email', 
        'telephone', 
        'id_kapsula_client', 
        'created_at', 
        'updated_at', 
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales()
    {
        return $this->hasMany('App\Entities\Sale');
    }
}
