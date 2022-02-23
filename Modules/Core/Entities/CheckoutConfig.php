<?php

namespace Modules\Core\Entities;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property integer $project_id
 * @property integer $checkout_type_enum
 * @property boolean $checkout_logo_enabled
 * @property string $checkout_logo
 * @property boolean $checkout_favicon_enabled
 * @property integer $checkout_favicon_type
 * @property string $checkout_favicon
 * @property boolean $checkout_banner_enabled
 * @property integer $checkout_banner_type
 * @property string $checkout_banner
 * @property boolean $countdown_enabled
 * @property integer $countdown_time
 * @property string $countdown_description
 * @property string $countdown_finish_message
 * @property boolean $topbar_enabled
 * @property string $topbar_content
 * @property boolean $notifications_enabled
 * @property integer $notifications_interval
 * @property boolean $notification_buying_enabled
 * @property integer $notification_buying_minimum
 * @property boolean $notification_bought_30_minutes_enabled
 * @property integer $notification_bought_30_minutes_minimum
 * @property boolean $notification_bought_last_hour_enabled
 * @property integer $notification_bought_last_hour_minimum
 * @property boolean $notification_just_bought_enabled
 * @property integer $notification_just_bought_minimum
 * @property boolean $social_proof_enabled
 * @property string $social_proof_message
 * @property integer $social_proof_minimum
 * @property string $invoice_description
 * @property integer $company_id
 * @property boolean $cpf_enabled
 * @property boolean $cnpj_enabled
 * @property boolean $credit_card_enabled
 * @property boolean $bank_slip_enabled
 * @property boolean $pix_enabled
 * @property boolean $quantity_selector_enabled
 * @property boolean $email_required
 * @property integer $installments_limit
 * @property integer $interest_free_installments
 * @property integer $preselected_installment
 * @property integer $bank_slip_due_days
 * @property integer $automatic_discount_credit_card
 * @property integer $automatic_discount_bank_slip
 * @property integer $automatic_discount_pix
 * @property boolean $post_purchase_message_enabled
 * @property string $post_purchase_message_title
 * @property string $post_purchase_message_content
 * @property boolean $whatsapp_enabled
 * @property string $support_phone
 * @property string $support_phone_verified
 * @property string $support_email
 * @property string $support_email_verified
 * @property integer $theme_enum
 * @property string $color_primary
 * @property string $color_secondary
 * @property string $color_buy_button
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 * @property Company $company
 */

class CheckoutConfig extends Model
{
    use LogsActivity;
    use SoftDeletes;

    public const CHECKOUT_TYPE_THREE_STEPS = 1;
    public const CHECKOUT_TYPE_ONE_STEP = 2;

    public const CHECKOUT_FAVICON_TYPE_LOGO = 1;
    public const CHECKOUT_FAVICON_TYPE_FILE = 2;

    public const CHECKOUT_BANNER_TYPE_FULL = 1;
    public const CHECKOUT_BANNER_TYPE_CENTER= 2;

    public const CHECKOUT_THEME_SPACESHIP = 1;
    public const CHECKOUT_THEME_PURPLE_SPACE = 2;
    public const CHECKOUT_THEME_CLOUD_STD = 3;
    public const CHECKOUT_THEME_SUNNY_DAY = 4;
    public const CHECKOUT_THEME_BLUE_SKY = 5;
    public const CHECKOUT_THEME_ALL_BLACK = 6;
    public const CHECKOUT_THEME_RED_MARS = 7;
    public const CHECKOUT_THEME_PINK_GALAXY = 8;
    public const CHECKOUT_THEME_TURQUOISE = 9;
    public const CHECKOUT_THEME_GREENER = 10;

    protected static $logFillable = true;
    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    protected $fillable = [
        'id',
        'project_id',
        'checkout_type_enum',
        'checkout_logo_enabled',
        'checkout_logo',
        'checkout_favicon_enabled',
        'checkout_favicon_type',
        'checkout_favicon',
        'checkout_banner_enabled',
        'checkout_banner_type',
        'checkout_banner',
        'countdown_enabled',
        'countdown_time',
        'countdown_description',
        'countdown_finish_message',
        'topbar_enabled',
        'topbar_content',
        'notifications_enabled',
        'notifications_interval',
        'notification_buying_enabled',
        'notification_buying_minimum',
        'notification_bought_30_minutes_enabled',
        'notification_bought_30_minutes_minimum',
        'notification_bought_last_hour_enabled',
        'notification_bought_last_hour_minimum',
        'notification_just_bought_enabled',
        'notification_just_bought_minimum',
        'social_proof_enabled',
        'social_proof_message',
        'social_proof_minimum',
        'invoice_description',
        'company_id',
        'cpf_enabled',
        'cnpj_enabled',
        'credit_card_enabled',
        'bank_slip_enabled',
        'pix_enabled',
        'quantity_selector_enabled',
        'email_required',
        'installments_limit',
        'interest_free_installments',
        'preselected_installment',
        'bank_slip_due_days',
        'automatic_discount_credit_card',
        'automatic_discount_bank_slip',
        'automatic_discount_pix',
        'post_purchase_message_enabled',
        'post_purchase_message_title',
        'post_purchase_message_content',
        'whatsapp_enabled',
        'support_phone',
        'support_phone_verified',
        'support_email',
        'support_email_verified',
        'color_primary',
        'color_secondary',
        'color_buy_button',
        'theme_enum',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function tapActivity(Activity $activity, string $eventName)
    {
        switch ($eventName) {
            case 'updated':
                $activity->description = 'Configuração do Checkout foi atualizada.';
                break;
            default:
                $activity->description = $eventName;
        }
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
