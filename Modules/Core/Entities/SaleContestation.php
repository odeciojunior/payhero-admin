<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * Class SaleContestation
 *
 * @package Modules\Core\Entities
 * @property integer $id
 * @property integer $sale_id
 * @property json $data
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Sale $sale
 * @property int $status
 * @property string|null $nsu
 * @property string|null $file_date
 * @property string|null $transaction_date
 * @property string|null $request_date
 * @property string|null $expiration_date
 * @property string|null $reason
 * @property int $is_contested
 * @property string|null $observation
 * @property int $file_user_completed
 * @property-read Collection|SaleContestationFile[] $files
 * @property-read int|null $files_count
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation newQuery()
 * @method static Builder|SaleContestation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation query()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereFileDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereFileUserCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereIsContested($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereNsu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereObservation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereRequestDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleContestation whereUpdatedAt($value)
 * @method static Builder|SaleContestation withTrashed()
 * @method static Builder|SaleContestation withoutTrashed()
 */
class SaleContestation extends Model
{
    use SoftDeletes, HasFactory;

    public const STATUS_IN_PROGRESS = 1;
    public const STATUS_LOST = 2;
    public const STATUS_WIN = 3;

    protected $keyType = 'integer';

    protected $fillable = [
        'sale_id',
        'gateway_id',
        'data',
        'nsu',
        'gateway_case_number',
        'file_date',
        'transaction_date',
        'request_date',
        'reason',
        'observation',
        'is_contested',
        'file_user_completed',
        'expiration_date',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(SaleContestationFile::class, 'contestation_sale_id');
    }
}
