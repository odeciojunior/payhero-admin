<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Modules\Core\Entities\WhiteBlackList
 *
 * @property int $id
 * @property int $type_enum Tipo (1 - White/ 2 - Black)
 * @property string $rule Regra
 * @property int $rule_enum Enum da regra
 * @property string $rule_type Tipo de regra (Equals, More, Less)
 * @property int $rule_type_enum Enum do tipo da regra (1 - Igual, 2 - Maior/Menor)
 * @property string $value Valor a verificar na regra
 * @property string|null $expires_at
 * @property string|null $description
 * @property int $count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList newQuery()
 * @method static Builder|WhiteBlackList onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList query()
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList whereRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList whereRuleEnum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList whereRuleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList whereRuleTypeEnum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList whereTypeEnum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteBlackList whereValue($value)
 * @method static Builder|WhiteBlackList withTrashed()
 * @method static Builder|WhiteBlackList withoutTrashed()
 */
class WhiteBlackList extends Model
{
    use HasFactory;
    use PresentableTrait;
    use SoftDeletes;

    public const RULE_BROWSER_FINGERPRINT_ENUM = 1;
    public const RULE_BROWSER_TOKEN_ENUM = 2;
    public const RULE_IP_ENUM = 3;
    public const RULE_DOCUMENT_ENUM = 4;
    public const RULE_EMAIL_ENUM = 5;
    public const RULE_TELEPHONE_ENUM = 6;
    public const RULE_CEP_ENUM = 7;
    public const RULE_NAME_ENUM = 8;

    protected $table = "white_black_list";

    protected $fillable = [
        "type_enum",
        "rule",
        "rule_enum",
        "rule_type",
        "rule_type_enum",
        "value",
        "expires_at",
        "description",
    ];
}
