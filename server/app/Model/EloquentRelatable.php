<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 4/19/17
 * Time: 12:40 PM
 */

namespace App\Model;


interface EloquentRelatable
{
    public function getAvailableRelations();

    public function getVisible();
}