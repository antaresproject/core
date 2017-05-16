<?php

use Antares\Acl\RoleActionList;
use Antares\Model\Role;
use Antares\Acl\Action;

$presentationActions = [
    'Notifications List', 'Notifications Details', 'Notifications Preview'
];

$crudActions         = [
    'Notifications Edit', 'Notifications Duplicate', 'Notifications Test', 'Notifications Change Status', 'Notifications Create',
    'Widget Send Notification', 'Notifications Delete'
];

$actions = [];
$allActions = array_merge($presentationActions, $crudActions);

foreach($allActions as $actionName) {
    $actions[] = new Action('', $actionName);
}

$permissions = new RoleActionList;
$permissions->add(Role::admin()->name, $actions);

return $permissions;