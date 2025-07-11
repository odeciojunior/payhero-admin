<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\CompanyDocumentPresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

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
    use PresentableTrait, FoxModelTrait, LogsActivity;

    public const STATUS_PENDING = 1;
    public const STATUS_ANALYZING = 2;
    public const STATUS_APPROVED = 3;
    public const STATUS_REFUSED = 4;

    public const DOCUMENT_TYPE_BANK_ENUM = 1;
    public const DOCUMENT_TYPE_ADDRESS_ENUM = 2;
    public const DOCUMENT_TYPE_CONTRACT_ENUM = 3;

    public const DOCUMENT_TYPE_BANK = "bank_document";
    public const DOCUMENT_TYPE_ADDRESS = "address_document";
    public const DOCUMENT_TYPE_CONTRACT = "contract_document";

    /**
     * @var string
     */
    protected $presenter = CompanyDocumentPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "company_id",
        "document_url",
        "document_type_enum",
        "status",
        "refused_reason",
        "created_at",
        "updated_at",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo("Modules\Core\Entities\Company");
    }
}
