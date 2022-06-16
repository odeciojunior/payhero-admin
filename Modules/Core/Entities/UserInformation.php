<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    public $table = 'user_informations';

    protected $keyType = 'integer';

    protected $fillable = [
        'last_step',
        'status',
        'email',
        'name',
        'document',
        'phone',
        'monthly_income',
        'niche',
        'website_url',
        'gateway',
        'ecommerce',
        'cloudfox_referer',
        'zip_code',
        'country',
        'state',
        'city',
        'district',
        'street',
        'number',
        'complement',
        'company_document',
        'company_zip_code',
        'company_country',
        'company_state',
        'company_city',
        'company_district',
        'company_street',
        'company_number',
        'company_complement',
        'bank',
        'agency',
        'agency_digit',
        'account',
        'account_digit',
        'created_at',
        'updated_at'
    ];

    public function userInformations()
    {
        return $this->belongsTo(User::class, 'document', 'document');
    }
}
