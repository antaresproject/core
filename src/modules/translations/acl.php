<?php

use Antares\Acl\RoleActionList;
use Antares\Model\Role;
use Antares\Acl\Action;

$presentationActions = [
    'Translations List', 'Change Language'
];
$crudActions         = [
    'Add Language',
    'Edit Translation', 'Publish Translations', 'Export Translations', 'Import Translations'
];

$actions = [];
$allActions = array_merge($presentationActions, $crudActions);

foreach($allActions as $actionName) {
    $actions[] = new Action('', $actionName);
}

$permissions = new RoleActionList;
$permissions->add(Role::admin()->name, $actions);

return $permissions;