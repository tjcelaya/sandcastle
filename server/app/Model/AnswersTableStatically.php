<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 4/22/17
 * Time: 12:54 PM
 */

namespace App\Model;

use ReflectionException;

trait AnswersTableStatically
{
    public static function databaseTable() {
        if (!method_exists(get_class(), 'getTable')) {
            throw new ReflectionException('AnswersTableStatically mixed into non-eloquent class');
        }

        return (new static)->getTable();
    }
}