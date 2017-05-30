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

namespace Antares\Html\Form;

use Antares\Contracts\Html\Form\Grid as GridContract;
use Illuminate\Contracts\Config\Repository;
use Antares\Contracts\Html\Form\Presenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\Validator;
use Antares\Html\Grid as BaseGrid;
use Antares\Support\Collection;
use Illuminate\Support\Fluent;
use InvalidArgumentException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Exception;
use stdClass;
use Closure;

class Grid extends BaseGrid implements GridContract
{

    /**
     * Test fields.
     *
     * @var array
     */
    protected $tests = [];

    /**
     * Enable CSRF token.
     *
     * @var bool
     */
    public $token = false;

    /**
     * Hidden fields.
     *
     * @var array
     */
    protected $hiddens = [];

    /**
     * List of row in array.
     *
     * @var array
     */
    protected $row = null;

    /**
     * All the fieldsets.
     *
     * @var Collection
     */
    public $fieldsets;

    /**
     * Set submit button message.
     *
     * @var string
     */
    public $submit = null;

    /**
     * Set the no record message.
     *
     * @var string
     */
    public $format = null;

    /**
     * Selected view path for form layout.
     *
     * @var array
     */
    protected $view = null;

    /**
     * Does the form has ajax validation
     * 
     * @var boolean
     */
    protected $ajaxable = false;

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var array
     */
    protected $phrases = [];

    /**
     * @var array
     */
    protected $params;

    /**
     * @var String
     */
    protected $name;

    /**
     * custom validator instance
     *
     * @var Validator
     */
    protected $customValidator;

    /**
     * whether form uses client side validation
     *
     * @var boolean
     */
    public $clientValidation = false;

    /**
     * {@inheritdoc}
     */
    protected $definition = [
        'name'    => null,
        '__call'  => ['fieldsets', 'view', 'hiddens', 'ajaxable', 'tests', 'rules', 'params', 'button', 'name'],
        '__get'   => ['attributes', 'row', 'view', 'hiddens', 'ajaxable', 'tests', 'rules', 'params', 'button', 'name', 'customValidator', 'phrases'],
        '__set'   => ['attributes', 'rules', 'ajaxable', 'button', 'name'],
        '__isset' => ['attributes', 'row', 'view', 'hiddens', 'ajaxable', 'tests', 'rules', 'params', 'button', 'name'],
    ];

    /**
     * Load grid configuration.
     *
     * @param  Repository  $config
     *
     * @return void
     */
    public function initiate(Repository $config)
    {
        if (extension_active('tester')) {
            $this->generator = app(\Antares\Tester\Builder\Generator::class);
        }
        $this->fieldsets = new Collection();
        foreach ($config->get('antares/html::form', []) as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        $this->row = [];
    }

    /**
     * Set fieldset layout (view).
     *
     * <code>
     *      // use default horizontal layout
     *      $fieldset->layout('horizontal');
     *
     *      // use default vertical layout
     *      $fieldset->layout('vertical');
     *
     *      // define fieldset using custom view
     *      $fieldset->layout('path.to.view');
     * </code>
     *
     * @param  string  $name
     *
     * @return $this
     */
    public function layout($name, array $params = null)
    {

        if (in_array($name, ['horizontal', 'vertical', 'vertical_compact'])) {
            $this->view = "antares/foundation::layouts.antares.partials.form.{$name}";
        } else {
            $this->view = $name;
        }
        $this->params = $params;

        return $this;
    }

    /**
     * set form as ajaxable
     * 
     * @param array $options
     * @return Grid
     */
    public function ajaxable(array $options = array())
    {
        $this->ajaxable = $options;
        return $this;
    }

    /**
     * set form with client validation
     * 
     * @return Grid
     */
    public function addClientValidation()
    {
        $this->clientValidation = true;
        return $this;
    }

    /**
     * Attach rows data instead of assigning a model.
     *
     * <code>
     *      // assign a data
     *      $form->with(DB::table('users')->get());
     * </code>
     *
     * @param  array|stdClass|Model  $row
     *
     * @return mixed
     */
    public function with($row = null)
    {
        is_array($row) && $row = new Fluent($row);

        if (!is_null($row)) {
            $this->row = $row;
        }

        return $this->row;
    }

    /**
     * Attach rows data instead of assigning a model.
     *
     * @param  array  $row
     *
     * @return mixed
     *
     * @see    $this->with()
     */
    public function row($row = null)
    {
        return $this->with($row);
    }

    /**
     * Create a new Fieldset instance.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     *
     * @return Fieldset
     */
    public function fieldset($name, Closure $callback = null)
    {
        $lastFieldset = $this->fieldsets->isEmpty() ? 0 : last($this->fieldsets->keys()->toArray()) + 2;

        if (!strlen($this->name)) {
            throw new Exception('Invalid form name.');
        }

        $primaryEventName = 'forms:' . str_slug($this->name) . '.fieldsets.' . $lastFieldset;
        Event::fire($primaryEventName . '.before', $this);


        $fieldset = new Fieldset($this->app, $name, $callback, $this->name, $this->name);

        if (is_null($fieldset->getName())) {
            $name = sprintf('fieldset-%d', $this->fieldsets->count());
        } else {
            if ($fieldset->getName() instanceof \Closure) {
                $name = sprintf('fieldset-%d', $this->fieldsets->count());
            }
            $name = Str::slug($name);
        }

        $this->keyMap[$name] = $fieldset;
        $return              = $this->fieldsets->push($fieldset);
        Event::fire($primaryEventName . '.after', $this);

        return $return;
    }

    /**
     * Find the existing Fieldset. It not exists then create a new instance.
     *
     * @param string $name
     * @param Closure|null $callback
     * @return \Antares\Contracts\Html\Form\Fieldset
     */
    public function findFieldsetOrCreateNew($name, Closure $callback = null)
    {
        foreach ($this->fieldsets as $fieldset) {
            if ($fieldset->getName() === $name) {
                $fieldset->update($callback);

                return $this->fieldsets;
            }
        }

        return $this->fieldset($name, $callback);
    }

    /**
     * Find control that match the given id.
     *
     * @param  string  $name
     *
     * @return Field|null
     *
     * @throws \InvalidArgumentException
     */
    public function find($name)
    {
        if (Str::contains($name, '.')) {
            list($fieldset, $control) = explode('.', $name, 2);
        } else {
            $fieldset = 'fieldset-0';
            $control  = $name;
        }

        if (!array_key_exists($fieldset, $this->keyMap)) {
            throw new InvalidArgumentException("Name [{$name}] is not available.");
        }

        return $this->keyMap[$fieldset]->of($control);
    }

    /**
     * Add hidden field.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     *
     * @return void
     */
    public function hidden($name, $callback = null)
    {
        $value = data_get($this->row, $name);

        $field = new Fluent([
            'name'       => $name,
            'value'      => $value ?: '',
            'attributes' => [],
        ]);

        if ($callback instanceof Closure) {
            call_user_func($callback, $field);
        }

        $this->hiddens[$name] = $this->app->make('form')->hidden($name, $field->get('value'), $field->get('attributes'));
    }

    /**
     * Setup form configuration.
     *
     * @param  Presenter  $listener
     * @param  string  $url
     * @param  Model  $model
     * @param  array  $attributes
     *
     * @return $this
     */
    public function resource(Presenter $listener, $url, Model $model, array $attributes = [])
    {
        $method = 'POST';
        if ($model->exists) {
            $url    = "{$url}/{$model->getKey()}";
            $method = 'PUT';
        }

        $attributes['method'] = $method;

        return $this->setup($listener, $url, $model, $attributes);
    }

    /**
     * form params builder without listener
     * 
     * @param String $url
     * @param Model $model
     * @param array $attributes
     * @return \Antares\Html\Form\Grid
     */
    public function resourced($url, Model $model, array $attributes = [])
    {
        $method = 'POST';
        if ($model->exists) {
            $url    = "{$url}/{$model->getKey()}";
            $method = 'PUT';
        }
        array_set($attributes, 'method', $method);
        $this->with($model);
        $this->attributes(array_merge($attributes, [
            'url'    => handles($url),
            'method' => $method,
        ]));
        return $this;
    }

    /**
     * Setup simple form configuration.
     *
     * @param  Presenter  $listener
     * @param  string  $url
     * @param  Model  $model
     * @param  array  $attributes
     *
     * @return $this
     */
    public function setup(Presenter $listener, $url, $model, array $attributes = [])
    {
        $method = Arr::get($attributes, 'method', 'POST');
        $this->with($model);
        $this->attributes(array_merge($attributes, [
            'url'    => method_exists($listener, 'handles') ? $listener->handles($url) : handles($url),
            'method' => $method,
        ]));
        !method_exists($listener, 'setupForm') ?: $listener->setupForm($this);
        return $this;
    }

    /**
     * attach simple form configuration
     * 
     * @param String $url
     * @param array $attributes
     * @param Model $model
     * @return Grid
     */
    public function simple($url, array $attributes = [], $model = null)
    {
        $method = Arr::get($attributes, 'method', 'POST');
        $this->attributes(array_merge($attributes, [
            'url'    => $url,
            'method' => $method,
        ]));
        $this->with($model);
        return $this;
    }

    /**
     * Add tester field.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     *
     * @return void
     */
    public function tester($name, array $attributes = [], $callback = null)
    {
        if (is_null($this->generator)) {
            return false;
        }
        $this->tests[$name] = $this->generator->build($name, $attributes, $callback);
    }

    /**
     * rules setter
     * 
     * @param array $rules
     * @return Grid
     */
    public function rules(array $rules = null)
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * phrases setter
     * 
     * @param array $phrases
     * @return Grid
     */
    public function phrases(array $phrases = null)
    {
        $this->phrases = $phrases;
        return $this;
    }

    /**
     * name of form grid
     * 
     * @param String $name
     * @return Grid
     */
    public function name($name)
    {
        if (is_null($name)) {
            throw new Exception('Form name cannot be empty.');
        }
        $this->name = $name;
        return $this;
    }

    /**
     * custom validator setter
     * 
     * @param \Antares\Html\Form\Validator $validator
     * @return Grid
     */
    public function setCustomValidator(Validator $validator)
    {
        $this->customValidator = $validator;
        return $this;
    }

}
