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


namespace Antares\Memory\Model;

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
     * @var Antares\Model\Role 
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
     * @param type $model
     * @return type
     */
    protected function complete($model)
    {
        return [
            'path'        => $model->path,
            'source-path' => $model->path,
            'name'        => $model->name,
            'full_name'   => $model->full_name,
            'description' => $model->description,
            'author'      => $model->author,
            'url'         => $model->url,
            'version'     => $model->version,
            'config'      => ($model->handles) ? ['handles' => $model->handles] : [],
            'autoload'    => ($model->autoload) ? [$model->autoload] : [],
            'provides'    => ($model->provides) ? explode(';', $model->provides) : []
        ];
    }

    /**
     * @param type $name
     * @return type
     */
    protected function isCoreComponent($name)
    {
        return $name == 'acl_antares';
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
        $key     = $this->getCachePrefix() . $brandId;
        $columns = ['id', 'brand_id', 'name', 'full_name', 'status', 'description', 'author', 'url', 'path', 'version', 'handles', 'provides', 'actions', 'permissions', 'options'];
        $builder = (!is_null($brandId)) ? static::select($columns)->where('brand_id', '=', $brandId)->orWhere('brand_id') : static::select($columns);
        $models  = $builder->with(['attachedActions'])->get();
        $return  = ['extensions' => ['active' => [], 'available' => [], 'modules' => []],];
        $roles   = $this->role->lists('name', 'id')->toArray();


        foreach ($models as $model) {
            $actions = $model->attachedActions->lists('name', 'id')->toArray();
            $isCore  = $this->isCoreComponent($model->name);

            if (!$isCore) {
                $configuration = $this->complete($model);
                $reversed      = array_reverse(explode('/', $model->path));
                $name          = implode('/', [$reversed[1], $reversed[0]]);

                $return['extensions']['available'][$name] = $configuration;
                if ($model->status) {
                    $return['extensions']['active'][$name] = $configuration;
                }
                if (starts_with($configuration['path'], 'base::src/modules')) {
                    $return['extensions']['modules'][$name] = $configuration;
                }
            }
            $key          = ($isCore) ? $model->name : 'acl_antares/' . $model->name;
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
     * updates component permission settings
     * @param String $name
     * @param array | mixed $values
     * @param boolean $isNew
     */
    public function updatePermissions($name, $values, $isNew = false, $brandId = null)
    {
        try {

            $model = $this->query()->where('name', '=', $name)->first();
            if (is_null($name)) {
                return false;
            }


            $actions = [];
            foreach ($values['actions'] as $actionName) {
                $action = $model->attachedActions()->where(['component_id' => $model->id, 'name' => $actionName])->first();
                if (is_null($action)) {
                    $action = $model->attachedActions()->getModel()->newInstance();
                }
                $action->fill(['component_id' => $model->id, 'name' => $actionName]);
                if (!$action->save()) {
                    throw new PermissionNotSavedException('Unable update module action configuration');
                }
                $actions[$action->id] = $action->name;
            }
            $brands = !is_null($brandId) ? [$brandId] : $this->brands()->getModel()->lists('id')->toArray();


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
