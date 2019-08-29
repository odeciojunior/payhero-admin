<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property int $project_id
 * @property string $link
 * @property boolean $boleto_generated
 * @property boolean $boleto_paid
 * @property boolean $credit_card_refused
 * @property boolean $credit_card_paid
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 */
class ConvertaxIntegration extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'convertax_integrations';
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'project_id',
        'user_id',
        'link',
        'value',
        'boleto_generated',
        'boleto_paid',
        'credit_card_refused',
        'credit_card_paid',
        'abandoned_cart',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Entities\Project');
    }
}
