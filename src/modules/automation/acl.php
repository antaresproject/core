<?php

use Antares\Acl\RoleActionList;
use Antares\Model\Role;
use Antares\Acl\Action;

$actions = [
    new Action('automation.list.index', 'Automation List'),
    new Action('automation.list.details', 'Automation Details'),
    new Action('automation.list.edit', 'Automation Edit'),
    new Action('automation.list.run', 'Automation Run'),
];

$permissions = new RoleActionList;
$permissions->add(Role::admin()->name, $actions);

return $permissions;