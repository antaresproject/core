<?php

use Antares\Acl\RoleActionList;
use Antares\Model\Role;
use Antares\Acl\Action;

$actions = [
    new Action('admin.customfields.index', 'List Customfields'),
    new Action('admin.customfields.create', 'Add Customfield'),
    new Action('admin.customfields.edit', 'Update Customfield'),
    new Action('admin.customfields.destroy', 'Delete Customfield'),
];

$permissions = new RoleActionList;
$permissions->add(Role::admin()->name, $actions);

return $permissions;