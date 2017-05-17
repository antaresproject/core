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

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Support\Fluent;
use Antares\Html\HtmlBuilder;
use Antares\Contracts\Html\Form\Template;
use Illuminate\Contracts\Container\Container;
use Antares\Contracts\Html\Form\Control as ControlContract;

class Control implements ControlContract
{

    /**
     * Container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Html builder instance.
     *
     * @var \Antares\Html\HtmlBuilder
     */
    protected $html;

    /**
     * Presenter instance.
     *
     * @var \Antares\Contracts\Html\Form\Template
     */
    protected $presenter;

    /**
     * Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Fieldset templates configuration.
     *
     * @var  array
     */
    protected $templates = [];

    /**
     * Create a new Field instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     * @param  \Antares\Html\HtmlBuilder  $html
     * @param  \Illuminate\Http\Request  $request
     */
    public function __construct(Container $app, HtmlBuilder $html, Request $request)
    {
        $this->app     = $app;
        $this->html    = $html;
        $this->request = $request;
    }

    /**
     * Set presenter instance.
     *
     * @param  \Antares\Contracts\Html\Form\Template  $presenter
     *
     * @return $this
     */
    public function setPresenter(Template $presenter)
    {
        $this->presenter = $presenter;

        return $this;
    }

    /**
     * Get presenter instance.
     *
     * @return \Antares\Contracts\Html\Form\Template
     */
    public function getPresenter()
    {
        return $this->presenter;
    }

    /**
     * Set template.
     *
     * @param  array  $templates
     *
     * @return $this
     */
    public function setTemplates(array $templates = [])
    {
        $this->templates = $templates;

        return $this;
    }

    /**
     * Get template.
     *
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * Generate Field.
     *
     * @param  string  $type
     *
     * @return \Closure
     */
    public function generate($type)
    {
        return function ($row, $control, $templates = []) use ($type) {
            $data = $this->buildFieldByType($type, $row, $control);
            return $this->render($templates, $data);
        };
    }

    /**
     * Build field by type.
     *
     * @param  string  $type
     * @param  mixed  $row
     * @param  \Illuminate\Support\Fluent  $control
     *
     * @return \Illuminate\Support\Fluent
     */
    public function buildFieldByType($type, $row, Fluent $control)
    {


        $attributes = (array) $control->attributes;

        $html              = $this->html;
        $templates         = $this->templates;
        $data              = $this->buildFluentData($type, $row, $control);
        $method            = $data->get('method');
        $data->options($this->getOptionList($row, $control));
        $data->checked($control->get('checked'));
        $data->optionsData = $control->get('optionsData') ?: [];

        $data->attributes($html->decorate($attributes, Arr::get($templates, $method)));
        if ($type === 'dropzone') {
            $data->label = $control->get('label');
        }
        return $data;
    }

    /**
     * Build data.
     *
     * @param  string  $type
     * @param  mixed  $row
     * @param  \Illuminate\Support\Fluent  $control
     *
     * @return \Illuminate\Support\Fluent
     */
    public function buildFluentData($type, $row, Fluent $control)
    {
        $name  = $control->get('name');
        $value = $this->resolveFieldValue($name, $row, $control);

        $data = new Field([
            'method'     => '',
            'type'       => $type,
            'options'    => [],
            'checked'    => false,
            'attributes' => [],
            'name'       => $name,
            'value'      => $value,
        ]);

        return $this->resolveFieldType($type, $data);
    }

    /**
     * Get options from control.
     *
     * @param  mixed  $row
     * @param  \Illuminate\Support\Fluent  $control
     *
     * @return array
     */
    protected function getOptionList($row, Fluent $control)
    {
        $options = $control->get('options');

        if ($options instanceof Closure) {
            $options = call_user_func($options, $row, $control);
        }

        return $options;
    }

    /**
     * Render the field.
     *
     * @param  array  $templates
     * @param  \Illuminate\Support\Fluent  $field
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function render($templates, Fluent $field)
    {
        $method   = $field->get('method');
        $template = Arr::get($templates, $method, [$this->presenter, $method]);
        if (!is_callable($template)) {
            throw new InvalidArgumentException("Form template for [{$method}] is not available.");
        }

        return call_user_func($template, $field);
    }

    /**
     * Resolve method name and type.
     *
     * @param  string  $value
     * @param  \Illuminate\Support\Fluent  $data
     *
     * @return \Illuminate\Support\Fluent
     */
    protected function resolveFieldType($value, Fluent $data)
    {

        $filterable = array_keys($this->templates);
        $value      = ($value == 'switch') ? 'switch_field' : $value;

        if (preg_match('/^(input):([a-zA-Z]+)$/', $value, $matches)) {
            $value = $matches[2];
        } elseif (preg_match('/^(button):([a-zA-Z]+)$/', $value, $matches)) {
            $value        = $matches[1];
            $data['type'] = $matches[2];
        } elseif (!in_array($value, $filterable)) {
            $value = 'text';
        }
        if (in_array($value, $filterable)) {
            $data->method($value);
        } else {
            $data->method('input')->type($value);
        }

        return $data;
    }

    /**
     * Resolve field value.
     *
     * @param  string  $name
     * @param  mixed  $row
     * @param  \Illuminate\Support\Fluent  $control
     *
     * @return mixed
     */
    protected function resolveFieldValue($name, $row, Fluent $control)
    {
        if (ends_with($name, '[]')) {
            $fieldnameInRequest = str_replace('[]', '', $name);
            $oldRequest         = $this->request->old($fieldnameInRequest);
            if (!is_null($oldRequest)) {

                if (($keyField = array_search($control->get('value'), $oldRequest)) !== false) {
                    $name = $fieldnameInRequest . '.' . $oldRequest[$keyField];
                    if ($control->offsetExists('checked')) {
                        $control->attributes = ['checked' => 'checked'];
                    }
                }
            }
        }

        $value = $this->request->old($name);
        if (!is_null($value) && $control->offsetExists('checked')) {
            $control->attributes = ['checked' => 'checked'];
        }


        $model = data_get($row, $name);

        if (!is_null($model) && is_null($value)) {
            $value = $model;
        }

        if (is_null($control->get('value'))) {
            return $value;
        }

        $value = $control->get('value');

        if ($value instanceof Closure) {
            $value = $value($row, $control);
        }

        return $value;
    }

}
