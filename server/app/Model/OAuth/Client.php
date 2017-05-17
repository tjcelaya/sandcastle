<?php

namespace App\Model\OAuth;

use App\Model\Base;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;

class Client extends Base
{
    use SoftDeletes;
    protected $table = 'oauth_clients';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'secret',
    ];
}
