<?php

namespace App\Model\OAuth;

use App\Model\Base;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;

class Scope extends Base
{
    use SoftDeletes;
    protected $table = 'oauth_scopes';

    protected $fillable = [
        'name',
        'description',
    ];

    protected $hidden = [
        'secret',
    ];
}
