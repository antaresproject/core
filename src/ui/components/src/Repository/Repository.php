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
 * @package    UI\UIComponents
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Repository;

use Antares\Foundation\Repository\AbstractRepository;
use Illuminate\Cache\Repository as CacheRepository;
use Antares\UI\UIComponents\Model\ComponentParams;
use Antares\UI\UIComponents\Model\ComponentTypes;
use Antares\UI\UIComponents\Model\Components;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container;
use Antares\Support\Collection;

class Repository extends AbstractRepository
{

    /**
     * cache repository instance
     *
     * @var CacheRepository
     */
    protected $cache;

    /*
     * constructing
     * 
     * @param Container $app
     * @param CacheRepository $cache
     */

    public function __construct(Container $app, CacheRepository $cache)
    {
        $this->cache = $cache;
        parent::__construct($app);
    }

    /**
     * name of repositroy model
     *
     * @return UiComponents
     */
    public function model()
    {
        return Components::class;
    }

    /**
     * Saves ui component
     * 
     * @param String $name
     * @param array $attributes
     * @return WidgetParams
     */
    public function save($name, $attributes = [])
    {
        if (auth()->guest()) {
            return;
        }
        $params          = [
            'uid'      => auth()->user()->id,
            'brand_id' => brand_id(),
            'resource' => uri(),
            'name'     => snake_case($name),
            'data'     => $attributes,
        ];
        $this->cache->forget($this->cacheKey($params));
        $wid             = $this->getComponentId($name);
        $params['wid']   = $wid;
        $componentParams = ComponentParams::firstOrNew($params);
        $componentParams->save();
        return $componentParams;
    }

    /**
     * Gets existing ui component id or add new
     * 
     * @param String $name
     * @return mixed
     */
    protected function getComponentId($name)
    {
        $where  = [
            'name'    => $name,
            'type_id' => app(ComponentTypes::class)->newInstance()->getDefault()->id
        ];
        $exists = $this->makeModel()->where($where)->first();
        if (!is_null($exists)) {
            return $exists->id;
        }
        $component = $this->makeModel()->getModel()->newInstance($where);
        $component->save();
        return $component->id;
    }

    /**
     * Finds all widgets by resource
     * 
     * @param String $resource
     * @return array
     */
    public function findAllByResource($resource)
    {

        $where = $this->getWhere(['resource' => $resource]);
        return $this->findByParams($where);
    }

    /**
     * Creates where
     * 
     * @param array $with
     * @return array
     */
    protected function getWhere(array $with = [])
    {
        return array_merge(['brand_id' => brand_id(), 'uid' => auth()->user()->id, 'resource' => uri()], $with);
    }

    /**
     * Finds all ui components by resource and additional params
     * 
     * @param String $resource
     * @param array $params
     * @return array
     */
    public function findAllByResourceAndNames($resource, array $params)
    {
        $where = $this->getWhere(['resource' => $resource]);
        $model = $this->makeModel()->getModel()->widgetParams()->getModel()->newQuery();
        return $model->where($where)->whereIn('name', $params)->get()->toArray();
    }

    /**
     * Find items by params
     * 
     * @param array $where
     * @return array
     */
    protected function findByParams($where)
    {
        $model = $this->makeModel()->getModel()->widgetParams()->getModel()->newQuery();
        return $model->where($where)->get()->toArray();
    }

    /**
     * Finds one widget by id
     * 
     * @param mixed $id
     * @return Model
     */
    public function findOneById($id)
    {
        return ComponentParams::whereId($id)->first();
    }

    /**
     * Finds entity attributes 
     * 
     * @param String $name
     * @return array
     */
    public function findAttributes($name)
    {
        $first = ComponentParams::where([
                    'name'     => $name,
                    'resource' => uri(),
                    'brand_id' => brand_id(),
                    'uid'      => auth()->user()->id
                ])->first();
        return is_null($first) ? false : $first->data;
    }

    /**
     * Finds entity attributes 
     * 
     * @param String $name
     * @return array
     */
    public function findForced($name)
    {
        $first = ComponentParams::where([
                    'name'     => $name,
                    'brand_id' => brand_id(),
                    'uid'      => auth()->user()->id
                ])->first();
        return is_null($first) ? false : $first->data;
    }

    /**
     * Saves single entity
     * 
     * @param Model $model
     * @return boolean
     */
    public function saveEntity(Model $model)
    {
        $cacheKey = $this->cacheKey($model->toArray());
        $this->cache->forget($cacheKey);
        return $model->save();
    }

    /**
     * Creates cache key
     * 
     * @param array $params
     * @return type
     */
    protected function cacheKey(array $params = [])
    {
        $prefix   = config('antares/ui-components::cache');
        $required = ['resource', 'uid', 'brand_id', 'name'];
        if (count(array_only($params, $required)) != count($required)) {
            return $prefix;
        }
        $ordered = [];
        foreach ($required as $key) {
            $ordered[$key] = array_get($params, $key);
        }
        return implode('.', array_merge([config('antares/ui-components::cache')], $ordered));
    }

    /**
     * Loads all ui components
     * 
     * @return \Illuminate\Support\Collection
     */
    public function load()
    {
        if (auth()->guest()) {
            return new Collection();
        }
        return ComponentParams::withoutGlobalScopes()
                        ->select(['tbl_widgets_params.id', 'tbl_widgets_params.wid', 'tbl_widgets_params.resource', 'tbl_widgets_params.name', 'tbl_widgets_params.data'])
                        ->where('brand_id', brand_id())
                        ->where('uid', auth()->user()->id)
                        ->get();
    }

}
