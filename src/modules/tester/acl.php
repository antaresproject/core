<?php

use Antares\Acl\RoleActionList;
use Antares\Model\Role;
use Antares\Acl\Action;

$actions = [
    new Action('', 'Tools Tester'),
];

$permissions = new RoleActionList;
$permissions->add(Role::admin()->name, $actions);

return $permissions;