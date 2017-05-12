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

use Antares\Brands\Model\Brands;
use Antares\Contracts\Installation\Installation as InstallationContract;
use Antares\Extension\Repositories\ComponentsRepository;
use Antares\Extension\Jobs\BulkExtensionsBackgroundJob;
use Antares\Extension\Contracts\ExtensionContract;
use Illuminate\Contracts\Container\Container;
use Antares\Extension\Processors\Activator;
use Antares\Extension\Processors\Installer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Antares\Model\Permission;
use InvalidArgumentException;
use Antares\Model\Component;
use Antares\Model\UserRole;
use Faker\Factory as Faker;
use Antares\Model\Action;
use Antares\Model\Role;
use Antares\Model\User;
use Carbon\Carbon;
use Exception;

class Installation implements InstallationContract
{

    /**
     * Container instance.
     *
     * @var Container
     */
    protected $app;

    /**
     * Installation constructor.
     * @param Container $app
     */
    public function __construct(Container $app)
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
        $tables = [
            'tbl_permissions',
            'tbl_actions',
            'tbl_components',
            'tbl_user_role',
            'tbl_users',
            'tbl_antares_options',
        ];

        return DB::transaction(function() use($tables) {
                    foreach ($tables as $table) {
                        DB::delete('delete from ' . $table);
                    }
                });
    }

    /**
     * Run application setup.
     *
     * @param  array  $input
     *
     * @return void
     * @throws Exception
     */
    protected function runApplicationSetup($input)
    {
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
            $memory->put(config('antares/notifications::default.notifications_remove_after_days', 90));
            $memory->put('email', $this->app->make('config')->get('mail'));
            $memory->put('email.from', [
                'name'    => $input['site_name'],
                'address' => $input['email'],
            ]);
            $memory->finish();
            $this->createFakeUsers($input['password']);
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

        DB::commit();
    }

    /**
     * @return Component
     */
    protected function importDefaultComponent()
    {
        return Component::create([
                    'vendor'   => 'antaresproject',
                    'name'     => 'core',
                    'status'   => ExtensionContract::STATUS_ACTIVATED,
                    'required' => true,
        ]);
    }

    /**
     * imports default admin user
     *
     * @param User $user
     * @return UserRole
     */
    private function importAdminUserRole(User $user)
    {
        return UserRole::create([
                    'user_id'    => $user->id,
                    'role_id'    => Role::admin()->id,
                    'created_at' => Carbon::now()
        ]);
    }

    /**
     * Imports default users permissions
     * 
     * @param array $actions
     * @throws Exception
     */
    private function importDefaultUsersPermissions(array $actions)
    {
        $defaults = (array) config('antares/installer::permissions.roles', []);

        foreach ($defaults as $role => $permissions) {
            /* @var $model Role */
            $model = $this->app->make('antares.role')->newQuery()->where('name', $role)->first();

            if ($model === null) {
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
                    $model = new Permission();
                    $model->fill([
                        'brand_id'     => $brand->id,
                        'component_id' => $action->component_id,
                        'role_id'      => $roleId,
                        'action_id'    => $action->id,
                        'allowed'      => 1,
                    ]);

                    if (!$model->save()) {
                        throw new Exception('Unable to set default user permissions');
                    }
                }
            }
        }
    }

    /**
     * Brands getter
     * 
     * @return Collection
     */
    protected function getBrands()
    {
        return Brands::all();
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
                    $permissionModel = new Permission();
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
     * @throws InvalidArgumentException
     */
    private function importDefaultComponentActions(Component $component)
    {
        $componentId = $component->id;

        if (!$componentId) {
            throw new InvalidArgumentException('Invalid component id');
        }

        $componentName  = $component->name;
        $defaultActions = (array) config('antares/installer::permissions.components.' . $componentName, []);

        $actions = array_map(function($value) use($componentId) {
            return Action::create([
                        'component_id' => $componentId,
                        'name'         => $value,
            ]);
        }, $defaultActions);

        return $actions;
    }

    /**
     * Create user account.
     *
     * @param  array  $input
     *
     * @return User
     */
    protected function createUser(array $input)
    {
        User::unguard();

        $user = new User();

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
        if (User::count() === 0) {
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
        $count = config('antares/installer::fake_users_count', 1);
        for ($i = 0; $i < $count; $i++) {
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
            $role = Role::query()->where('name', 'member')->firstOrFail();
            if ($user->save()) {
                $id       = $user->id;
                $userRole = new UserRole([
                    'user_id'    => $id,
                    'role_id'    => $role->id,
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

    /**
     * Sets queue for installation of components.
     *
     * @return void
     */
    public function runComponentsInstallation(array $extensions = [])
    {

        $componentsRepository = app(ComponentsRepository::class);
        $progress             = app(Progress::class);
        $extensions           = empty($extensions) ? array_keys($componentsRepository->getRequired()) : $extensions;


        // Steps are the sum of extensions and composer command.
        $progress->setSteps(1 + count($extensions));
        $progress->start();

        $components = $componentsRepository->getWithBranches($extensions);
        $extensions = [];

        foreach ($components as $component => $branch) {
            $extensions[] = $component . ':' . $branch;
        }

        $operationClasses = [
            Installer::class,
            Activator::class,
        ];

        $installJob = new BulkExtensionsBackgroundJob($extensions, $operationClasses, $progress->getFilePath());
        $installJob->onQueue('install');

        dispatch($installJob);
    }

}
