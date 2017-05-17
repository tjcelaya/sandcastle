<?php

namespace App\Model;

use Zizaco\Entrust\EntrustPermission;

/**
 * Class Permission
 * @package App\Model
 *
 * @method static create(array $attrs)
 * @method static find($id)
 */
class Permission extends EntrustPermission
{
    public $incrementing = false;
    protected $primaryKey = 'name';
    protected $keyType = 'string';

    // table name must be configured in `config/entrust.php`

    /** @var string name
      * Unique name for the Role, used for looking up role information in the application layer.
      * For example: "admin", "owner", "employee".
      */
    /** @var string|null display_name
      * Human readable name for the Role. Not necessarily unique and optional.
      * For example: "User Administrator", "Project Owner", "Widget Co. Employee".
      */
    /** @var string|null description
      * A more detailed explanation of what the Role does. Also optional.
      */
    const NAME_ISSUE_LIST_ASSIGNED = 'issue.list.assigned';
    const DESC_ISSUE_LIST_ASSIGNED = 'View Assigned Issues';

    const NAME_ISSUE_VERIFY = 'issue.verify';
    const DESC_ISSUE_VERIFY = 'Verify Issue';

    const NAME_ISSUE_STATUS_TRANSITION = 'issue.status.transition';
    const DESC_ISSUE_STATUS_TRANSITION = 'Transition Issue State';

    const NAME_ISSUE_NOTES_APPEND = 'issue.notes.append';
    const DESC_ISSUE_NOTES_APPEND = 'Add Issue Notes';

    const NAME_ISSUE_RESOURCE_ELECT = 'issue.resource.elect';
    const DESC_ISSUE_RESOURCE_ELECT = 'Elect Resource';
}
