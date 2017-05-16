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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Control\Processor;

use Antares\Control\Http\Presenters\Role as RolePresenter;
use Antares\Contracts\Foundation\Foundation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Antares\Model\Role as Eloquent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Antares\Support\Str;
use Exception;

class Role extends Processor
{

    /**
     * control scripts config container
     * 
     * @var array
     */
    protected $config;

    /**
     * memory provider
     *
     * @var \Antares\Form\Provider\Provider 
     */
    protected $collector;

    /**
     * Setup a new processor instance.
     *
     * @param  \Antares\Control\Http\Presenters\Role  $presenter
     * @param  \Antares\Contracts\Foundation\Foundation  $foundation
     */
    public function __construct(RolePresenter $presenter, Foundation $foundation)
    {
        $this->presenter  = $presenter;
        $this->foundation = $foundation;
        $this->model      = $foundation->make('antares.role');
    }

    /**
     * view list action
     * 
     * @return \Illuminate\Http\Response|\Illuminate\View\View 
     */
    public function index()
    {
        return $this->presenter->table();
    }

    /**
     * View create a role page.
     *
     * @param  object  $listener
     *
     * @return mixed
     */
    public function create($listener)
    {

        $eloquent = $this->model;
        $form     = $this->presenter->form($eloquent);
        return $listener->createSucceed(compact('eloquent', 'form'));
    }

    /**
     * View edit a role page.
     *
     * @param  object  $listener
     * @param  string|int  $id
     *
     * @return mixed
     */
    public function edit($listener, $id)
    {
        $eloquent = $this->model->findOrFail($id);
        $data     = $this->presenter->edit($eloquent);
        return $listener->editSucceed($data);
    }

    /**
     * Store a role.
     *
     * @param  object  $listener
     * @param  array   $input
     *
     * @return mixed
     */
    public function store($listener, array $input)
    {
        $role = $this->model;
        $form = $this->presenter->form($role);
        if (!$form->isValid()) {
            return $listener->storeValidationFailed($form->getMessageBag());
        }
        try {
            $this->saving($role, $input, 'create');
        } catch (Exception $e) {
            Log::warning($e);
            return $listener->storeFailed(['error' => $e->getMessage()]);
        }
        return $listener->storeSucceed($role);
    }

    /**
     * Update a role.
     *
     * @param  object  $listener
     * @param  array   $input
     * @param  int     $id
     *
     * @return mixed
     */
    public function update($listener, array $input, $id)
    {

        if ((int) $id !== (int) $input['id']) {
            return $listener->userVerificationFailed();
        }
        $role = $this->model->findOrFail($id);
        $form = $this->presenter->form($role);
        if (!$form->isValid()) {
            return $listener->updateValidationFailed($form->getMessageBag(), $id);
        }

        try {
            $this->saving($role, $input, 'update');
        } catch (Exception $e) {

            Log::warning($e);
            return $listener->updateFailed(['error' => $e->getMessage()]);
        }

        return $listener->updateSucceed();
    }

    /**
     * Delete a role.
     *
     * @param  object  $listener
     * @param  string|int  $id
     *
     * @return mixed
     */
    public function destroy($listener, $id)
    {
        $role = $this->model->findOrFail($id);
        try {
            if ($role->users->count() > 0) {
                throw new Exception('Unable to delete group with assigned users.');
            }
            DB::transaction(function () use ($role) {
                $role->delete();
            });
        } catch (Exception $e) {
            Log::warning($e);
            return $listener->destroyFailed(['error' => $e->getMessage()]);
        }

        return $listener->destroySucceed($role);
    }

    /**
     * Save the role.
     *
     * @param  \Antares\Model\Role  $role
     * @param  array  $input
     * @param  string  $type
     *
     * @return bool
     */
    protected function saving(Eloquent $role, $input = [], $type = 'create')
    {
        $beforeEvent = ($type === 'create' ? 'creating' : 'updating');
        $afterEvent  = ($type === 'create' ? 'created' : 'updated');

        $name = $input['name'];
        $role->fill([
            'name'        => snake_case($name, '-'),
            'full_name'   => $name,
            'area'        => array_get($input, 'area'),
            'description' => $input['description']
        ]);
        if (!$role->exists && isset($input['roles'])) {
            $role->parent_id = $input['roles'];
        }
        $this->fireEvent($beforeEvent, [$role]);
        $this->fireEvent('saving', [$role]);
        DB::transaction(function() use($role, $input) {
            $role->save();
            $this->import($input, $role);
        });
        $this->fireEvent($afterEvent, [$role]);
        $this->fireEvent('saved', [$role]);

        return true;
    }

    /**
     * import permissions when copy
     * 
     * @param array $input
     * @param Model $role
     */
    protected function import(array $input, Model $role)
    {
        if (isset($input['import']) && !is_null($from = $input['roles'])) {
            $permission = $this->foundation->make('antares.auth.permission');

            $permissions = $permission->where('role_id', $from)->get();

            $permissions->each(function(Model $model) use($permission, $role) {
                $attributes = $model->getAttributes();
                $insert     = array_except($attributes, ['id', 'role_id']) + ['role_id' => $role->id];
                $permission->newInstance($insert)->save();
            });
        }
        return true;
    }

    /**
     * Fire Event related to eloquent process.
     *
     * @param  string  $type
     * @param  array   $parameters
     *
     * @return void
     */
    protected function fireEvent($type, array $parameters = [])
    {
        Event::fire("antares.control.{$type}: roles", $parameters);
    }

    /**
     * Acl rules form
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function acl($id)
    {
        $eloquent = $this->model->findOrFail($id);
        return $this->presenter->acl($eloquent);
    }

    /**
     * Modules structure as tree
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree($id)
    {
        $eloquent  = $this->model->findOrFail($id);
        $instances = app('antares.acl')->all();
        $return    = [
            'name'    => 'Root',
            'open'    => true,
            'checked' => false,
        ];
        $modules   = app(\Antares\Control\Contracts\ModulesAdapter::class)->modules();

        foreach ($modules as $module) {
            $item        = [
                'saveName'      => array_get($module, 'full_name', '---'),
                'name'          => array_get($module, 'full_name', '---'),
                'indeterminate' => false,
                'checked'       => false,
                'open'          => true,
            ];
            if (!is_null($description = array_get($module, 'description'))) {
                array_set($item, 'description', $description);
            }
            $actions = array_get($module, 'actions', []);
            if (empty($actions) or ! isset($instances[$module['namespace']])) {
                continue;
            }
            $children = [];
            $checked  = true;

            foreach ($actions as $key => $action) {
                $checked    = $instances[$module['namespace']]->check($eloquent->name, $action);
                $children[] = [
                    'saveName'      => "acl[{$id}][{$key}]",
                    'name'          => Str::humanize($action),
                    'indeterminate' => false,
                    'checked'       => $checked,
                    'value'         => $action
                ];
                if (!$checked) {
                    $checked = false;
                }
            }
            $item['checked'] = $checked;

            $item['children']     = $children;
            $return['children'][] = $item;
        }
        return new \Illuminate\Http\JsonResponse(['tree' => $return]);
    }

}
