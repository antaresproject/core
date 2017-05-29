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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Processor;

use Antares\UI\UIComponents\Exception\ComponentNotFoundException;
use Antares\UI\UIComponents\Model\ComponentParams;
use Antares\Foundation\Processor\Processor;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class DefaultProcessor extends Processor
{

    /**
     * Ui components repository
     *
     * @var \Antares\UI\UIComponents\Repository\Repository 
     */
    protected $repository;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->repository = app('ui-components');
    }

    /**
     * Functionality of preview widget
     * 
     * @param numeric $id
     * @return mixed | array
     */
    public function show($id)
    {
        $component = $this->component($id)->templateLayout();
        $html      = $component->__toString();
        if (!strlen($html)) {
            return $component->render();
        }
        return $html;
    }

    /**
     * Renders ui component content
     * 
     * @param mixed $id
     * @return JsonResponse
     */
    protected function component($id)
    {
        $model = $this->repository->findOneById($id);
        if (is_null($model)) {
            return new JsonResponse(['message' => ''], 200);
        }
        $classname = $model->data['classname'];
        $instance  = new $classname;

        if ($instance->getAttribute('ajaxable') === false) {
            return new JsonResponse('', 302);
        }
        $inputs = Input::get('attributes');

        if (!is_null($inputs) and method_exists($instance, 'hydrate')) {
            $args = empty($inputs) ? [] : unserialize($inputs);
            $instance->hydrate($args);
        }
        return $instance;
    }

    /**
     * Saves current widget positions
     * 
     * @param array $data
     */
    public function positions(array $data = null)
    {
        if (!$this->savePosition($data)) {
            return new JsonResponse(['message' => 'Unable to save ui component positions', 500]);
        }
        $id        = array_get($data, 'current');
        $component = $this->component($id);

        return ($component instanceof JsonResponse) ? $component : $component->render();
    }

    /**
     * Saves ui component position change
     * 
     * @param array $data
     * @return mixed
     */
    protected function savePosition(array $data)
    {
        DB::transaction(function() use($data) {
            $widgets = $data['widgets'];
            foreach ($widgets as $item) {
                $id    = $item['widgetId'];
                $model = ComponentParams::where('id', $id)->first();
                if (is_null($model)) {
                    continue;
                }
                $position    = array_only($item, ['x', 'y', 'width', 'height']);
                $data        = $model->data;
                $model->data = array_merge($data, $position);
                $this->repository->saveEntity($model);
            }
        });
        return true;
    }

    /**
     * Creates instance of ui component
     * 
     * @param numeric $id
     * @return array
     */
    public function view($id)
    {
        $component = null;
        DB::transaction(function() use($id, &$component) {
            $resource = Input::get('from');
            $model    = $this->repository->findOneById($id);
            if (is_null($model)) {
                throw new ComponentNotFoundException('Component not found');
            }
            $data             = $model->data;
            $data['disabled'] = false;
            $model->data      = $data;
            $model->resource  = strlen($resource) > 0 ? $resource : uri();
            $this->repository->saveEntity($model);


            $component  = app()->make($data['classname']);
            $attributes = array_only($data, ['x', 'y', 'width', 'height', 'disabled']) + ['id' => $id];
            $component->setAttributes($attributes);
            $inputs     = Input::get('attributes');

            if (!is_null($attributes) and method_exists($component, 'hydrate')) {
                $args = empty($inputs) ? [] : unserialize($inputs);
                $component->hydrate($args);
            }
            $component->setView('antares/ui-components::admin.partials._base');
        });



        return ['component' => $component];
    }

}
