<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $sale_id
 * @property mixed $question
 * @property boolean $correct_answer
 * @property boolean $client_answer
 * @property boolean $correct_flag
 * @property string $created_at
 * @property string $updated_at
 * @property Sale $sale
 */
class SaleIdwallQuestions extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'question',
        'correct_answer',
        'client_answer',
        'correct_flag',
        'expire_at',
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo('Modules\Core\Entities\Sale');
    }
}
