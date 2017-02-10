<?php

/**
 * Part of the Antares Project package.
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
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Installation;

use Antares\Contracts\Installation\Installation as InstallationContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Antares\Brands\Model\Brands;
use Antares\Model\Component;
use Antares\Model\UserRole;
use Faker\Factory as Faker;
use Antares\Model\User;
use Carbon\Carbon;
use Exception;

class Installation implements InstallationContract
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Construct a new instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application   $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Boot installer files.
     *
     * @return void
     */
    public function bootInstallerFiles()
    {
        $paths = ['path.database', 'path'];
        $files = $this->app->make('files');
        foreach ($paths as $path) {
            $file = rtrim($this->app->make($path), '/') . '/antares/installer.php';

            if ($files->exists($file)) {
                $files->requireOnce($file);
            }
        }
    }

    /**
     * Migrate Antares schema.
     *
     * @return bool
     */
    public function migrate()
    {
        $this->app->make('antares.publisher.migrate')->foundation();
        $this->app->make('events')->fire('antares.install.schema');
        return true;
    }

    /**
     * Create adminstrator account.
     *
     * @param  array  $input
     * @param  bool   $allowMultiple
     *
     * @return bool
     */
    public function createAdmin($input, $allowMultiple = true)
    {

        $rules = [
            'email'     => ['required', 'email'],
            'password'  => ['required'],
            'firstname' => ['required'],
            'lastname'  => ['required'],
            'site_name' => ['required'],
        ];

        $validation = $this->app->make('validator')->make($input, $rules);

        if ($validation->fails()) {
            $this->app->make('session')->flash('errors', $validation->messages());
            return false;
        }

        try {
            !$allowMultiple && $this->hasNoExistingUser();
            $this->runApplicationSetup($input);
            return true;
        } catch (Exception $e) {
            Log::emergency($e);
            $this->app->make('antares.messages')->add('error', $e->getMessage());
            return false;
        }
    }

    /**
     * clear all tables before run seeds
     */
    protected function clear()
    {
        return DB::transaction(function() {
                    DB::delete('delete from tbl_permissions');
                    DB::delete('delete from tbl_component_config');
                    DB::delete('delete from tbl_actions');
                    DB::delete('delete from tbl_components');
                    DB::delete('delete from tbl_user_role');
                    DB::delete('delete from tbl_users');
                    DB::delete('delete from tbl_antares_options');
                });
    }

    /**
     * Run application setup.
     *
     * @param  array  $input
     *
     * @return void
     */
    protected function runApplicationSetup($input)
    {
        $exception = false;
        DB::beginTransaction();
        try {
            $this->clear();
            $user      = $this->createUser($input);
            $userRole  = $this->importAdminUserRole($user);
            $component = $this->importDefaultComponent();
            $actions   = $this->importDefaultComponentActions($component);
            $this->importDefaultAdminPermissions($userRole, $actions);
            $this->importDefaultUsersPermissions($actions);
            $memory    = $this->app->make('antares.memory')->make('primary');
            $theme     = [
                'frontend' => 'client',
                'backend'  => 'default',
            ];
            $memory->put('site.name', $input['site_name']);
            $memory->put('site.theme', $theme);
            $memory->put('email', $this->app->make('config')->get('mail'));
            $memory->put('email.from', [
                'name'    => $input['site_name'],
                'address' => $input['email'],
            ]);
            $this->createFakeUsers($input['password']);
        } catch (Exception $ex) {
            $exception = true;
            DB::rollback();
            throw new $ex;
        }
        DB::commit();
        return $exception === false;
    }

    /**
     * imports default components settings
     * @return Antares\Model\Component
     */
    protected function importDefaultComponent()
    {
        $component = $this->app->make('antares.component')->newInstance();

        $component->fill([
            'name'   => 'acl_antares',
            'status' => 1
        ]);
        $component->save();

        return $component;
    }

    /**
     * imports default admin user
     * @param User $user
     * @return \Antares\Model\UserRole
     */
    private function importAdminUserRole(User $user)
    {

        $role        = $this->app->make('antares.role');
        $adminRoleId = $role::admin()->id;
        $userRole    = $this->app->make('antares.user.role')->newInstance();
        $userRole->fill([
            'user_id'    => $user->id,
            'role_id'    => $adminRoleId,
            'created_at' => Carbon::now()
        ]);
        $userRole->save();
        return $userRole;
    }

    /**
     * Imports default users permissions
     * 
     * @param array $actions
     * @throws Exception
     */
    private function importDefaultUsersPermissions(array $actions)
    {
        $defaults = config('antares/installer::permissions');
        foreach ($defaults as $role => $permissions) {

            $model = $this->app->make('antares.role')->newQuery()->where('name', $role)->first();
            if (is_null($model)) {
                continue;
            }
            $roleId = $model->id;
            $result = array_filter(array_map(function($action) use($permissions) {
                        if (in_array($action->name, $permissions)) {
                            return $action;
                        }
                    }, $actions));

            $brands = $this->getBrands();
            foreach ($result as $action) {
                foreach ($brands as $brand) {
                    if (!$this->app->make('antares.component.permission')->newInstance([
                                'brand_id'     => $brand->id,
                                'component_id' => $action->component_id,
                                'role_id'      => $roleId,
                                'action_id'    => $action->id,
                                'allowed'      => 1,
                            ])->save()) {
                        throw new Exception('Unable to set default user permissions');
                    }
                }
            }
        }
    }

    /**
     * Brands getter
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getBrands()
    {
        return Brands::query()->get()->all();
    }

    /**
     * imports default user permissions
     * @param UserRole $userRole
     * @param array $actions
     * @throws Exception
     */
    private function importDefaultAdminPermissions(UserRole $userRole, array $actions)
    {
        $roleId = $userRole->role_id;

        if (!empty($actions)) {
            $brands = $this->getBrands();
            foreach ($actions as $action) {
                foreach ($brands as $brand) {
                    $fill            = [
                        'brand_id'     => $brand->id,
                        'component_id' => $action->component_id,
                        'role_id'      => $roleId,
                        'action_id'    => $action->id,
                        'allowed'      => 1,
                    ];
                    $permissionModel = $this->app->make('antares.component.permission')->newInstance();
                    $permissionModel->fill($fill);
                    if (!$permissionModel->save()) {
                        throw new Exception('Unable to set default user permissions');
                    }
                }
            }
        }
    }

    /**
     * imports default actions
     * @param Component $component
     * @return array
     * @throws Exception
     */
    private function importDefaultComponentActions(Component $component)
    {
        $componentId = $component->id;
        if (!$componentId) {
            throw new Exception('Invalid component id');
        }

        $defaultActions = [
            'manage-antares',
            'manage-users',
            'manage-roles',
            'manage-acl',
            'clients-list',
            'client-create',
            'client-update',
            'client-delete',
            'change-app-settings',
            'show-dashboard',
            'component-activate',
            'component-migrate',
            'component-deactivate',
            'component-uninstall',
            'component-delete',
            'configure-component',
            'module-configure',
            'module-create',
            'modules-list',
            'module-details',
            'module-activate',
            'module-deactivate',
            'module-migrate',
            'module-uninstall',
            'module-delete',
            'brand-update',
            'brand-email'
        ];
        $actions        = array_map(function($value) use($componentId) {
            $action = $this->app->make('antares.component.action')->newInstance();
            $action->fill([
                'component_id' => $componentId,
                'name'         => $value,
            ]);
            $action->save();
            return $action;
        }, $defaultActions);
        return $actions;
    }

    /**
     * Create user account.
     *
     * @param  array  $input
     *
     * @return \Antares\Model\User
     */
    protected function createUser($input)
    {
        User::unguard();
        $user = $this->app->make('antares.user')->newInstance();

        $user->fill([
            'email'     => $input['email'],
            'password'  => $input['password'],
            'firstname' => $input['firstname'],
            'lastname'  => $input['lastname'],
            'status'    => 1,
        ]);

        $this->app->make('events')->fire('antares.install: user', [$user, $input]);

        $user->save();

        return $user;
    }

    /**
     * Check for existing User.
     *
     * @return bool
     *
     * @throws \Exception
     */
    protected function hasNoExistingUser()
    {
        $users = $this->app->make('antares.user')->newQuery()->all();

        if (empty($users)) {
            return true;
        }

        throw new Exception(trans('antares/foundation::install.user.duplicate'));
    }

    /**
     * @todo remove
     * creating fake users
     * @throws Exception
     */
    public function createFakeUsers($password = null)
    {

        $faker = Faker::create();
        for ($i = 0; $i < 200; $i++) {
            User::unguard();
            $user = $this->app->make('antares.user')->newInstance();
            $fill = [
                'email'     => $i . $faker->email,
                'password'  => $password,
                'firstname' => $faker->firstName,
                'lastname'  => $faker->lastName,
                'status'    => 1,
            ];
            $user->fill($fill);

            $this->app->make('events')->fire('antares.install: user', [$user, $fill]);

            if ($user->save()) {
                $id       = $user->id;
                $roleId   = rand(2, 4);
                $userRole = new UserRole([
                    'user_id'    => $id,
                    'role_id'    => $roleId,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);
                if (!$userRole->save()) {
                    throw new Exception('Unable to save fake user role');
                }
            } else {
                throw new Exception('Unable to save fake user');
            }
        }
    }

}
