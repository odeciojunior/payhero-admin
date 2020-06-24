<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\UserInformationPresenter;
use Spatie\Activitylog\Models\Activity;

/**
 * Class UserInformation
 * @package Modules\Core\Entities
 * @property int $id
 * @property int $user_id
 * @property string $sex
 * @property int $marital_status
 * @property string $nationality
 * @property string $mother_name
 * @property string $father_name
 * @property string $spouse_name
 * @property string $birth_place
 * @property string $birth_city
 * @property string $birth_state
 * @property string $birth_country
 * @property int $monthly_income
 * @property int $document_type
 * @property string $document_number
 * @property string $document_issue_date
 * @property string $document_expiration_date
 * @property string $document_issuer
 * @property string $document_issuer_state
 * @property string $document_serial_number
 * @method UserInformationPresenter present()
 */
class UserInformation extends Model
{
    use FoxModelTrait;
    use LogsActivity;
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = UserInformationPresenter::class;

    protected $table = 'user_informations';

    /**
     * @var string[]
     */
    protected $appends = ['id_code'];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'sex',
        'marital_status',
        'nationality',
        'mother_name',
        'father_name',
        'spouse_name',
        'birth_place',
        'birth_city',
        'birth_state',
        'birth_country',
        'monthly_income',
        'document_type',
        'document_number',
        'document_issue_date',
        'document_expiration_date',
        'document_issuer',
        'document_issuer_state',
        'document_serial_number',
    ];

    /**
     * @var array
     */
    protected static $logAttributes = ['*'];
    /**
     * @var bool
     */
    protected static $logUnguarded = true;
    /**
     * Registra apenas os atributos alterados no log
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * Impede que armazene logs vazios
     * @var bool
     */
    protected static $submitEmptyLogs = false;

    /**
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName == 'deleted') {
            $activity->description = 'Informação do usuário ' . $this->name . ' foi deletedo.';
        } elseif ($eventName == 'updated') {
            $activity->description = 'Informação do usuário ' . $this->name . ' foi atualizado.';
        } elseif ($eventName == 'created') {
            $activity->description = 'Informação do usuário ' . $this->name . ' foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User');
    }

}
