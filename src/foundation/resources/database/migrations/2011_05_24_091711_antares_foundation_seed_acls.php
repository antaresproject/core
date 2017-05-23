<?php

/**
 * Part of the Antares package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
use Antares\Model\Role;
use Illuminate\Database\Migrations\Migration;

class AntaresFoundationSeedAcls extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $admin = Role::admin();

        $acl = app('antares.acl')->make('antares');
        $memory = app('antares.memory')->make('component');
        $acl->attach($memory);
        $acl->roles()->attach([$admin->name]);

        $presentationActions = [
            'Admin List', //users
            'Preview Themes', 'Preview Frontend', //themes
            'Roles List', //roles
        ];

        $crudActions = [
            'User Create', 'User Update', 'User Delete', //users
            'Activate Theme', 'Upload Theme', 'Install Theme', //themes
            'Create Role', 'Edit Role', 'Delete Role', //roles            
            'Properties', 'Properties Update', //action properties
            'Manage Roles', 'Manage Acl', 'Manage Settings', //global            
        ];

        $acl->actions()->attach(array_merge($presentationActions, $crudActions));
        $acl->allow($admin->name, array_merge($presentationActions, $crudActions));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Foundation::memory()->forget('acl_antares/foundation');
    }

}
