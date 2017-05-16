<?php

use Antares\Acl\RoleActionList;
use Antares\Model\Role;
use Antares\Acl\Action;

$actionNames = [
    'Activity Dashboard', 'Activity Delete Log', 'Activity Show Details',
    'Report Generate', 'Report View', 'Report Delete', 'Report Html', 'Report Download',
    'Analyzer Dashboard', 'Analyzer Run', 'Analyzer Server', 'Analyzer System',
    'Analyzer Modules', 'Analyzer Version', 'Analyzer Database', 'Analyzer Logs',
    'Analyzer Components', 'Analyzer Checksum',
    'Report Generate', 'Report View', 'Report Download', 'Report Delete', 'Report Html', 'Report Send', 'Report Generate Standalone',
    'View Logs',
    'Request List', 'Request Clear', 'Request Show',
    'Error List', 'Error Details', 'Error Delete', 'Error Download',
    'History List', 'History Show', 'History Delete'
];

$actions = [];

foreach($actionNames as $actionName) {
    $actions[] = new Action('', $actionName);
}

$permissions = new RoleActionList;
$permissions->add(Role::admin()->name, $actions);

return $permissions;