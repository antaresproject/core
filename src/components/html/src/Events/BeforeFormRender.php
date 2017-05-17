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


namespace Antares\Html\Events;

use Antares\Contracts\Http\Middleware\ModuleNamespaceResolver;
use Antares\Contracts\Html\Form\Field as FieldContract;
use Antares\Contracts\Html\Form\Grid as GridContract;
use Antares\Form\Controls\AbstractType;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Antares\Support\Facades\Memory;
use Antares\Memory\MemoryManager;
use Antares\Model\Action;
use Closure;

class BeforeFormRender
{

    /**
     * application container
     * 
     * @var Illuminate\Contracts\Foundation\Application 
     */
    protected $app;

    /**
     * module namespace resolver
     *
     * @var Antares\Contracts\Http\Middleware\ModuleNamespaceResolver 
     */
    protected $resolver;

    /**
     * memory manager
     *
     * @var Antares\Memory\MemoryManager 
     */
    protected $memory;

    /**
     * constructing
     * 
     * @param Application $app
     * @param ModuleNamespaceResolver $resolver
     * @param MemoryManager $memory
     */
    public function __construct(Application $app, ModuleNamespaceResolver $resolver, MemoryManager $memory)
    {
        $this->app      = $app;
        $this->resolver = $resolver;
        if ($app['antares.installed']) {
            $this->memory = $memory->make('collector');
        }
    }

    /**
     * event handler
     * 
     * @param GridContract $grid
     * @return boolean
     */
    public function handle(GridContract $grid)
    {
        $runtime = Memory::make('runtime')->get('control');
        if (is_null($runtime)) {
            return false;
        }
        $name      = array_get($runtime, 'component');
        $fieldsets = $grid->fieldsets;
        $controls  = [];
        $formName  = null;
        foreach ($fieldsets as $fieldset) {
            $formName = is_null($formName) ? $fieldset->name : $formName;
            foreach ($fieldset->controls as $control) {
                if($control instanceof AbstractType) {
                    continue;
                }
                $controls[] = $this->attributes($control);
            }
        }
        $action = Action::select(['id', 'component_id', 'name'])->where('name', array_get($runtime, 'action'))->whereHas('extension', function($query) use($name) {
                    $query->where('name', $name);
                })->first();
        $configuration = array_merge(['cid' => $action->component_id, 'aid' => $action->id, 'name' => $formName, 'component' => $name, 'controls' => $controls], array_only($runtime, ['controller', 'action', 'method', 'namespace']));
        if (!is_string($formName)) {
            return;
        }
        $key = implode('::', [$name, array_get($runtime, 'action'), array_get($runtime, 'method'), str_slug($formName)]);
        $this->memory->push($key, $configuration);
        $this->memory->finish();
    }

    /**
     * form attributes resolver
     * 
     * @param ControlContract $control
     * @return type
     */
    protected function attributes(FieldContract $control)
    {
        $attributes          = array_only($control->getAttributes(), ['id', 'name', 'value', 'label', 'type']);
        $attributes['value'] = ($attributes['value'] instanceof Closure) ? '' : $attributes['value'];
        switch ($attributes['type']) {
            case 'select':
                $attributes['options'] = $this->getOptions($control->options);
                break;
        }
        return $attributes;
    }

    /**
     * recursive field options getter
     * 
     * @param mixed $options
     * @param array $default
     * @return Closure
     */
    protected function getOptions($options = null, $default = [])
    {
        if (empty($options)) {
            return $default;
        }
        if ($options instanceof Collection) {
            return $options->toArray();
        }
        if ($options instanceof Closure) {
            return $this->getOptions(call_user_func($options));
        }
        if (is_array($options)) {
            return $options;
        }
        return $options->toArray();
    }

}
