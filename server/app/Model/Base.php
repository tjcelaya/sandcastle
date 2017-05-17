<?php

namespace App\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Base
 * @package App\Model
 *
 * @method create(array $attrs) static
 * @method static find($id) static
 */
abstract class Base extends Model implements EloquentRelatable
{
<<<<<<< Updated upstream
    use AnswersTableStatically;

    public function getAvailableRelations()
    {
        return [];
    }
=======
    public static $availableRelations = [];
>>>>>>> Stashed changes
}
