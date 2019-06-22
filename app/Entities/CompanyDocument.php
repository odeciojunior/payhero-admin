<?php

namespace App\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $company_id
 * @property string $document_url
 * @property boolean $document_type_enum
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property Company $company
 */
class CompanyDocument extends Model
{
    use FoxModelTrait;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'company_id',
        'document_url',
        'document_type_enum',
        'status',
        'created_at',
        'updated_at',
    ];
    /**
     * @var array
     */
    private $enum = [
        'status' => [
            1 => 'pending',
            2 => 'analyzing',
            3 => 'approved',
            4 => 'refused',
        ],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Entities\Company');
    }
}
