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

namespace Antares\Memory\Model;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Memory\Exception\PermissionNotSavedException;
use Antares\Model\Permission as PermissionModel;
use Antares\Support\Facades\Foundation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Antares\Brands\Model\Brands;
use Antares\Model\Component;
use Antares\Model\Eloquent;
use Antares\Model\Action;
use Antares\Model\Role;
use Exception;

class Permission extends Eloquent
{

    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The class name to be used in polymorphic relations.
     * @var string
     */
    protected $morphClass = 'Permission';

    /**
     * @var \Antares\Model\Role
     */
    protected $role;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->role = Foundation::make(Role::class);
    }

    /**
     * fetching all permissions
     */
    public static function fetchAll($brandId = null)
    {
        $models = (!is_null($brandId)) ? static::query()->where('brand_id', '=', $brandId)->orWhere('brand_id')->get() : static::query()->get();
        return $models->toArray();
    }

    /**
     * @return type
     */
    public function attachedActions()
    {
        return $this->hasMany(Action::class, 'component_id', 'id');
    }

    /**
     * @return type
     */
    public function brands()
    {
        return $this->hasMany(Brands::class, 'component_id', 'id');
    }

    /**
     * @return type
     */
    public function permission()
    {
        return $this->hasMany(PermissionModel::class, 'id', 'id');
    }

    /**
     * @return type
     */
    public function component()
    {
        return $this->hasOne(Component::class, 'component_id', 'id');
    }

    /**
     * @param type $permissions
     * @return array
     */
    protected function permissions($permissions = null)
    {
        if (is_null($permissions) OR strlen($permissions) <= 0) {
            return [];
        }
        $exploded = explode(';', $permissions);
        $maps     = [];
        foreach ($exploded as $current) {
            if (!strlen($current)) {
                continue;
            }
            $permission = explode('=', $current);
            $maps[]     = [$permission[0] => (boolean) $permission[1]];
        }
        $return = [];
        array_walk($maps, function($item) use(&$return) {
            $return[key($item)] = current($item);
        });
        return $return;
    }

    /**
     * @param $model
     * @return array
     */
    protected function complete($model)
    {
        return [
            'vendor'   => $model->vendor,
            'name'     => $model->name,
            'fullname' => $model->vendor . '/' . $model->name
        ];
    }

    /**
     * @param $name
     * @return bool
     */
    protected function isCoreComponent($name)
    {
        return $name === 'core';
    }

    /**
     * cache prefix getter
     *
     * @return String
     */
    protected function getCachePrefix()
    {
        return config('antares/memory::permission.cache_prefix');
    }

    /**
     * fetching all permissions
     */
    public function getAll($brandId = null)
    {
        $columns = ['id', 'vendor', 'name', 'status', 'actions', 'permissions', 'options'];
        $builder = (!is_null($brandId)) ? static::select($columns)->where('brand_id', '=', $brandId)->orWhere('brand_id') : static::select($columns);
        $models  = $builder->with(['attachedActions'])->get();
        $return  = ['extensions' => ['active' => [], 'available' => []],];
        $roles   = $this->role->pluck('name', 'id')->toArray();

        foreach ($models as $model) {
            $actions = $model->attachedActions->pluck('name', 'id')->toArray();
            $isCore  = $this->isCoreComponent($model->name);

            if (!$isCore) {
                $configuration = $this->complete($model);
                $name          = $configuration['fullname'];

                $return['extensions']['available'][$name] = $configuration;

                if ($model->status === ExtensionContract::STATUS_ACTIVATED) {
                    $return['extensions']['active'][$name] = $configuration;
                }
            }


            $key          = $isCore ? 'acl_antares' : 'acl_' . $this->getNormalizedName($configuration['fullname']);
            $return[$key] = [
                'acl'     => $this->permissions($model->permissions),
                'actions' => $actions,
                'roles'   => $roles
            ];
        }

        ksort($return['extensions']['active']);
        ksort($return['extensions']['available']);
        return $return;
    }

    /**
     * Returns normalized component name. It is used for backward compatibility.
     *
     * @param string $name
     * @return string
     */
    protected function getNormalizedName(string $name): string
    {
        $name = str_replace(['antaresproject/component-', 'antaresproject/module-'], 'antares/', $name);

        return str_replace('-', '_', $name);
    }

    /**
     * updates component permission settings
     *
     * @param $name
     * @param $values
     * @param bool $isNew
     * @param null $brandId
     * @return bool
     */
    public function updatePermissions($name, $values, $isNew = false, $brandId = null)
    {
        try {
            if ($name === null) {
                return false;
            }
            $model = null;

            if (str_contains($name, '/')) {
                list($vendor, $name) = explode('/', $name);
                $model = $this->query()->where(['vendor' => $vendor, 'name' => $name])->first();
            } elseif ($name !== 'core' && !str_contains($name, 'component-')) {
                $name = 'component-' . $name;
            }
            $name = str_replace('module_', 'module-', $name);
            if (is_null($model)) {
                $model = $this->query()->where('name', '=', $name)->first();
            }
            if ($model === null) {
                return false;
            }


            $actions = [];
            foreach ($values['actions'] as $actionName) {
                $action = $model->attachedActions()->where(['component_id' => $model->id, 'name' => $actionName])->first();
                if (is_null($action)) {
                    $action = $model->attachedActions()->getModel()->newInstance();
                }
                $actionParams = ['component_id' => $model->id, 'name' => $actionName, 'description' => array_get($values, 'descriptions.' . $actionName)];
                if (!is_null($category     = array_get($values, 'categories.' . $actionName))) {
                    array_set($actionParams, 'category_id', \Antares\Model\ActionCategories::query()->firstOrCreate(['name' => $category])->id);
                }

                $action->fill($actionParams);


                if (!$action->save()) {
                    throw new PermissionNotSavedException('Unable update module action configuration');
                }
                $actions[$action->id] = $action->name;
            }

            $brands = !is_null($brandId) ? [$brandId] : $this->brands()->getModel()->pluck('id')->toArray();


            foreach ($values['acl'] as $rule => $isAllowed) {
                $rules    = explode(':', $rule);
                $roleId   = $rules[0];
                $actionId = array_search($values['actions'][$rules[1]], $actions);

                foreach ($brands as $brand) {
                    $permissionModel = $this->permission()->getModel()
                            ->where('brand_id', '=', $brand)
                            ->where('action_id', '=', $actionId)
                            ->where('component_id', '=', $model->id)
                            ->where('role_id', '=', $roleId)
                            ->get()
                            ->first();

                    $exists = (is_null($permissionModel)) ? false : $permissionModel->exists;
                    if ($exists) {
                        $permissionModel->allowed = (int) $isAllowed;
                    } else {
                        $permissionModel = $this->permission()->getModel()->newInstance()->fill([
                            'brand_id'     => $brand,
                            'component_id' => $model->id,
                            'role_id'      => $roleId,
                            'action_id'    => $actionId,
                            'allowed'      => (int) $isAllowed
                        ]);
                    }

                    if (!$permissionModel->save()) {
                        throw new PermissionNotSavedException('Unable update module permission');
                    }
                }
            }
            Cache::forget($this->getCachePrefix());
            return true;
        } catch (Exception $e) {
            Log::emergency($e);
            return false;
        }
    }

}
