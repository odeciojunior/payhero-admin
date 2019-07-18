<?php

namespace App\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user
 * @property int $project
 * @property string $token
 * @property string $url_store
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 * @property User $user
 */
class ShopifyIntegration extends Model
{
    use FoxModelTrait;
    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * @var array
     */
    protected $fillable = [
        'user',
        'project',
        'token',
        'url_store',
        'theme_type',
        'theme_name',
        'theme_file',
        'theme_html',
        'layout_theme_html',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * @var array
     */
    private $enum = [
        'theme_type' => [
            1 => 'basic_theme',
            2 => 'ajax_theme',
        ],
        'status'     => [
            1 => 'pending',
            2 => 'approved',
            3 => 'disabled',
        ],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Entities\Project', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Entities\User', 'user');
    }
}
