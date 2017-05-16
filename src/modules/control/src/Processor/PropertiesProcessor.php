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

use Antares\Control\Http\Presenters\Properties as Presenter;
use Antares\Control\Contracts\Listener\Properties as Listener;
use Illuminate\Container\Container;

class PropertiesProcessor extends Processor
{

    /**
     * instance of application container
     * 
     * @var \Illuminate\Container\Container 
     */
    protected $container;

    /**
     * Setup a new processor instance.
     * 
     * @param Presenter $presenter
     * @param Container $container
     */
    public function __construct(Presenter $presenter, Container $container)
    {
        $this->presenter = $presenter;
        $this->container = $container;
    }

    /**
     * view resource action property form
     * 
     * @param Listener $listener
     * @param numeric $roleId
     * @param numeric $formId
     * @return type
     */
    public function properties(Listener $listener, $roleId, $formId)
    {

        $collector  = $this->container->make('antares.memory')->make('collector');
        $attributes = $collector->get($collector->getNameById($formId));

        if (empty($attributes)) {
            return $listener->noProperties();
        } else {
            $controls = $attributes['controls'];
            $memory   = $this->container->make('antares.memory');
            $brandId  = $memory->make('primary')->get('brand.default');
            $where    = ['brand_id' => $brandId, 'role_id' => $roleId, 'form_id' => $formId];

            $instance = $this->container->make('antares.forms.config');
            $model    = $instance->where($where)->first();

            if (is_null($model)) {
                $model = $instance->newInstance();
            } else {
                $model->value = $memory->make('forms-config')->getHandler()->reverse($model->value);
            }
            $form = $this->presenter->form($roleId, $formId, $controls, $model);
        }
        return $listener->propertiesSucceed(compact('form', 'attributes'));
    }

    /**
     * updates form configuration
     * 
     * @param numeric $roleId
     * @param numeric $actionId
     * @param array $data
     */
    public function update(Listener $listener, $roleId, $formId, array $data = array())
    {
        $elements    = explode('&', urldecode($data['elements']));
        $editable    = [];
        $displayable = [];
        foreach ($elements as $element) {
            if (starts_with($element, 'editable')) {
                array_push($editable, $this->encodeElement($element));
            }
            if (starts_with($element, 'displayable')) {
                array_push($displayable, $this->encodeElement($element));
            }
        }

        $value        = $this->value(['editable' => $editable, 'displayable' => $displayable]);
        $model        = $this->getResolver($roleId, $formId);
        $model->value = $value;
        return ($model->save()) ? $listener->updateSuccess($roleId, $formId) : $listener->updateError($roleId, $formId);
    }

    /**
     * preparing value to before save
     * 
     * @param array $value
     * @return String
     */
    protected function value($value)
    {
        $memory  = $this->container->make('antares.memory')->make('forms-config');
        $handler = $memory->getHandler();
        return $handler->compile($value);
    }

    /**
     * get instance of eloquent model
     * 
     * @param int $roleId
     * @param int $formId
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function getResolver($roleId, $formId)
    {
        $brandId    = $this->container->make('antares.memory')->make('primary')->get('brand.default');
        $model      = $this->container->make('antares.forms.config');
        $attributes = ['form_id' => $formId, 'role_id' => $roleId, 'brand_id' => $brandId];
        $eloquent   = $model::where($attributes)->first();
        if (is_null($eloquent)) {
            $eloquent = $model->newInstance($attributes);
        }
        return $eloquent;
    }

    /**
     * encodes element attributes
     * 
     * @param String $element
     * @return array
     */
    protected function encodeElement($element)
    {
        preg_match("/\[[^\]]*\]/", $element, $matches);
        $name = ltrim($matches[0], '[');
        if (strpos($name, '[') === false) {
            $name = rtrim($name, ']');
        }
        preg_match("/^.+?\=(.+)$/is", $element, $m);
        $value = isset($m[1]) ? $m[1] : null;
        return ['name' => $name, 'value' => $value];
    }

}
