<?php

use Antares\Acl\RoleActionList;
use Antares\Model\Role;
use Antares\Acl\Action;

$resellerActions = [
    'Sandbox Dashboard', 'Sandbox Run', 'Sandbox Delete',
    'Updater Dashboard', 'Hide Version Alert',
];
$adminActions    = [
    'Create Backup',
    'Backups List',
    'Restore Backup',
    'Update System',
    'Update Module',
    'Production Set',
    'Versions List'
];

$actions = [];
$allActions = array_merge($resellerActions, $adminActions);

foreach($allActions as $actionName) {
    $actions[] = new Action('', $actionName);
}

$permissions = new RoleActionList;
$permissions->add(Role::admin()->name, $actions);

return $permissions;