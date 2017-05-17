<?php

namespace App\Model;

use App\Model\Base;

class Resource extends Base
{
    protected $table = 'resource';

    public $timestamps = true;

    protected $fillable = [
        'name'
    ];

    protected $guarded = [];

    public function locations() {
        return $this->morphMany(Location::class, 'locatable');
    }
}