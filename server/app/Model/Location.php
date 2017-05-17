<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Location extends Base
{
    protected $table = 'location';

    public function locatable()
    {
        return $this->morphTo();
    }
}
