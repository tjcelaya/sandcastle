<?php

namespace App\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

abstract class AuthenticatableBase extends Authenticatable
{
    // Authenticatable extends laravel model
    public static $availableRelations = [];
}
