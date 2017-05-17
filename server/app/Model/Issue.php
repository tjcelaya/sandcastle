<?php

namespace App\Model;

use App\Model\Base;
use App\Model\User;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Request
 */
class Issue extends Base
{
    use SoftDeletes;

    protected $table = 'issue';

    public $timestamps = true;

    protected $visible = [
        'id',
        'name',
        'notes',
    ];

    protected $fillable = [
        'name',
        'notes',
    ];

    public function getAvailableRelations()
    {
        return [
            'contact' => [
                'class' => Contact::class,
                'cardinality' => 'one',
                'autoload' => true,
            ]
        ];
    }

    protected $guarded = [];

    public function user()
    {
        return $this->belongsToMany(
            User::class,
            'issue_user',
            'issue_id',
            'user_id')
            ->withTimestamps();
    }

    public function contact()
    {
        return $this->morphOne(Contact::class, 'contactable');
    }

    public function resourceEngagement()
    {
        return $this->belongsToMany(ResourceEngagement::class)
            ->withTimestamps();
    }
}
