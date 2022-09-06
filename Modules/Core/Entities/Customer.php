<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\CustomerPresenter;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property string $name
 * @property string $document
 * @property string $email
 * @property string $telephone
 * @property integer $balance
 * @property boolean $blocked_withdrawal
 * @property string $birthday
 * @property integer $id_kapsula_client
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Collection $sales
 * @property Collection $deliveries
 */
class Customer extends Model
{
    use SoftDeletes, PresentableTrait, FoxModelTrait, LogsActivity, HasFactory;
    /**
     * @var string
     */
    protected $presenter = CustomerPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "name",
        "document",
        "email",
        "telephone",
        "balance",
        "blocked_withdrawal",
        "birthday",
        "id_kapsula_client",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    /**
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany("Modules\Core\Entities\Sale");
    }

    /**
     * @return HasMany
     */
    public function clientCards()
    {
        return $this->hasMany("App\Entities\ClientCard");
    }

    /**
     * @return HasMany
     */
    public function bankAccounts()
    {
        return $this->hasMany(CustomerBankAccount::class, "customer_id");
    }

    /**
     * @return HasMany
     */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * @return HasMany
     */
    public function customerWithdrawals()
    {
        return $this->hasMany("Modules\Core\Entities\CustomerWithdrawal");
    }
}
