<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\ProductPresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property integer $category_id
 * @property int $user_id
 * @property string $name
 * @property string $description
 * @property string $guarantee
 * @property boolean $format
 * @property string $photo
 * @property string $height
 * @property string $width
 * @property string $length
 * @property string $weight
 * @property boolean $shopify
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $digital_product_url
 * @property string $price
 * @property string $shopify_id
 * @property string $shopify_variant_id
 * @property Category $category
 * @property User $user
 * @property Collection $productPlanSales
 * @property Collection $productPlans
 * @property Collection $variants
 * @method ProductPresenter present()
 */
class Product extends Model
{
    use SoftDeletes, PresentableTrait, FoxModelTrait, LogsActivity;

    public const TYPE_PHYSICAL = 1;
    public const TYPE_DIGITAL = 2;

    public const STATUS_ENUM_ANALYZING = 1;
    public const STATUS_ENUM_APPROVED = 2;
    public const STATUS_ENUM_REFUSED = 3;

    public const ACTIVE_FLAG_ACTIVE = 1;
    public const ACTIVE_FLAG_DESABLE = 0;

    /**
     * @var array
     */
    protected $dates = ["created_at", "updated_at", "deleted_at"];
    /**
     * @var array
     */
    protected $appends = ["id_code"];
    /**
     * @var string
     */
    protected $presenter = ProductPresenter::class;
    /**
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "category_id",
        "user_id",
        "name",
        "description",
        "guarantee",
        "format",
        "photo",
        "height",
        "width",
        "length",
        "weight",
        "shopify",
        "created_at",
        "updated_at",
        "deleted_at",
        "digital_product_url",
        "url_expiration_time",
        "price",
        "shopify_id",
        "shopify_variant_id",
        "project_id",
        "type_enum",
        "status_enum",
        "active_flag",
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
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function productsPlanSales()
    {
        return $this->hasMany(ProductPlanSale::class);
    }

    /**
     * @return HasMany
     */
    public function productsPlans()
    {
        return $this->hasMany(ProductPlan::class);
    }

    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return HasMany
     */
    public function variants()
    {
        return $this->hasMany(Product::class, "shopify_id", "shopify_id")->where(function ($query) {
            $query
                ->where("type_enum", Product::TYPE_PHYSICAL)
                ->orWhere("type_enum", Product::TYPE_DIGITAL)
                ->where("status_enum", Product::STATUS_ENUM_APPROVED);
        });
    }
}
