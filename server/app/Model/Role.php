<?php

namespace App\Model;

use LogicException;
use Zizaco\Entrust\EntrustRole;

/**
 * Class Role
 * @package App\Model
 *
 * @property string name
 * @property string display_name
 *
 * @method static create(array $attrs) static
 * @method static find($id) static
 *
 * @method static admin() static
 * @method static analyst() static
 * @method static peer() static
 */
class Role extends EntrustRole
{
    use AnswersTableStatically;

    public $incrementing = false;
    protected $primaryKey = 'name';
    protected $keyType = 'string';

    const ADMIN = 'admin';
    const ANALYST = 'analyst';
    const PEER = 'peer';

    /** @var string name
     * Unique name for the Role, used for looking up role information in the application layer. For example: "admin", "owner", "employee".
     */
    /** @var string display_name
     * Human readable name for the Role. Not necessarily unique and optional. For example: "User Administrator", "Project Owner", "Widget Co. Employee".
     */
    /** @var string description
     * A more detailed explanation of what the Role does. Also optional.
     */

    public static function __callStatic($method, $parameters)
    {
        if (in_array($method, [self::ADMIN, self::ANALYST, self::PEER])) {
            $r = new Role();
            $r->name = $method;
            $r->display_name = title_case($method);
            $r->exists = true;
            return $r;
        }

        return parent::__callStatic($method, $parameters);
    }
}
