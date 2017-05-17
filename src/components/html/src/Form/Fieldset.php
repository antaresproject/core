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

use Antares\Contracts\Html\Form\Fieldset as FieldsetContract;
use Antares\Contracts\Html\Form\Control as ControlContract;
use Antares\Contracts\Html\Form\Field as FieldContract;
use Antares\Form\Controls\AbstractType;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Config\Repository;
use Antares\Contracts\Html\Form\Template;
use Illuminate\Support\Facades\Event;
use Antares\Html\Grid as BaseGrid;
use Illuminate\Support\Fluent;
use Exception;
use Closure;

class Fieldset extends BaseGrid implements FieldsetContract
{

    /**
     * Fieldset name.
     *
     * @var string
     */
    protected $name = null;

    /**
     * Fieldset legend.
     *
     * @var String
     */
    protected $legend = null;

    /**
     * Form name.
     *
     * @var string
     */
    protected $formName = null;

    /**
     * Control group.
     *
     * @var array
     */
    protected $controls = [];

    /**
     * Field control instance.
     *
     * @var ControlContract
     */
    protected $control = null;

    /**
     * Fieldset layout
     *
     * @var String
     */
    protected $layout = null;

    /**
     * Fieldset view params
     *
     * @var array
     */
    protected $params = [];

    protected $orientation = 'horizontal';

    /**
     * {@inheritdoc}
     */
    protected $definition = [
        'name'    => 'controls',
        '__call'  => ['controls', 'name', 'legend', 'fieldset', 'render'],
        '__get'   => ['attributes', 'name', 'legend', 'controls'],
        '__set'   => ['attributes', 'controls', 'layout'],
        '__isset' => ['attributes', 'name', 'legend', 'controls'],
    ];

    /**
     * Create a new Fieldset instance.
     *
     * @param  Container $app
     * @param  string $name
     * @param  \Closure $callback
     */
    public function __construct(Container $app, $name, Closure $callback = null, $formName = null)
    {
        $this->formName = $formName;
        $this->name     = $name;
        parent::__construct($app);
        $this->buildBasic($name, $callback);
    }

    /**
     * Load grid configuration.
     *
     * @param  Repository $config
     * @param  ControlContract $control
     * @param  Template $presenter
     *
     * @return void
     */
    public function initiate(Repository $config, ControlContract $control, Template $presenter)
    {
        $templates = $config->get('antares/html::form.templates', []);
        $control->setTemplates($templates)->setPresenter($presenter);

        $this->control = $control;
    }

    /**
     * Build basic fieldset.
     *
     * @param  string $name
     * @param  \Closure $callback
     *
     * @return void
     */
    protected function buildBasic($name, Closure $callback = null)
    {
        if ($name instanceof Closure) {
            $callback = $name;
            $name     = null;
        }
        //!empty($name) && $this->legend($name);

        call_user_func($callback, $this);
    }

    /**
     * Updates the Fieldset closure.
     *
     * @param Closure|null $callback
     */
    public function update(Closure $callback = null)
    {
        call_user_func($callback, $this);
    }

    /**
     * Append a new control to the form.
     *
     * <code>
     *      // add a new control using just field name
     *      $fieldset->control('input:text', 'username');
     *
     *      // add a new control using a label (header title) and field name
     *      $fieldset->control('input:email', 'E-mail Address', 'email');
     *
     *      // add a new control by using a field name and closure
     *      $fieldset->control('input:text', 'fullname', function ($control)
     *      {
     *          $control->label = 'User Name';
     *
     *          // this would output a read-only output instead of form.
     *          $control->field = function ($row) {
     *              return $row->first_name.' '.$row->last_name;
     *          };
     *      });
     * </code>
     *
     * @param  string $type
     * @param  mixed $name
     * @param  mixed $callback
     *
     * @return Fluent
     */
    public function control($type, $name, $callback = null)
    {

        list($name, $control) = $this->buildControl($name, $callback, $type);

        $primaryEventName = 'forms:' . str_slug($this->formName) . '.controls.' . $name;
        Event::fire($primaryEventName . '.before', $this);

        if (is_null($control->field)) {
            $control->field = $this->control->generate($type);
        }

        $this->controls[]    = $control;
        $this->keyMap[$name] = empty($this->keyMap) ? count($this->controls) - 1 : last($this->keyMap) + 1;


        Event::fire($primaryEventName . '.after', $this);

        return $control;
    }

    /**
     * Add customfield to form
     *
     * @param Grid $grid
     * @param String $name
     */
    public function customfield($grid, $name)
    {
        if (!extension_active('customfields')) {
            return;
        }
        $category     = strtolower(last(explode('\\', get_class($grid->row))));
        $customfields = app('customfields')->get();

        foreach ($customfields as $classname => $fields) {
            if (get_class($grid->row) !== $classname) {
                continue;
            }
            foreach ($fields as $field) {
                if ($field->getName() === $name) {
                    return $this->addCustomfield($grid, $field);
                }
            }
        }

        $fieldView = \Antares\Customfields\Model\FieldView::query()->where([
            'name'          => $name,
            'brand_id'      => brand_id(),
            'category_name' => $category,
            'imported'      => 0])->first();
        if (is_null($fieldView)) {
            return;
        }
        return $this->addCustomfield($grid, $fieldView);
    }

    /**
     * Add single customfield
     *
     * @param Grid $grid
     * @param \Antares\Customfield\CustomField $field
     */
    protected function addCustomfield($grid, $field)
    {
        if (!$field instanceof \Antares\Customfield\CustomField) {
            $customfield = with(new \Antares\Customfield\CustomField())->attributes($field);
        } else {
            $customfield = $field;
        }
        $customfield->setModel($grid->row);
        $this->add($customfield);
        if (is_null($grid->rules)) {
            $grid->rules([]);
        }
        $grid->rules(array_merge($grid->rules, $customfield->getRules()));

        $grid->row->saved(function ($row) use ($customfield) {
            $customfield->onSave($row);
        });
    }

    /**
     * Adds customfields by fieldset name
     *
     * @param Grid $grid
     * @param String $name
     * @return $this
     */
    public function customfieldsByFieldset($grid, $name)
    {
        if (!extension_active('customfields')) {
            return;
        }
        $customfields = app('customfields')->get();
        $items        = [];
        $reserved     = [];
        foreach ($customfields as $classname => $fields) {
            if (get_class($grid->row) !== $classname) {
                continue;
            }
            foreach ($fields as $field) {
                if ($field->getFieldset() === $name) {
                    $reserved[] = $field->getName();
                    $items[]    = $field;
                }
            }
        }
        foreach ($items as $item) {
            $this->addCustomfield($grid, $item);
        }
        $query = \Antares\Customfields\Model\FieldView::query();
        if (!empty($reserved)) {
            $query->whereNotIn('name', $reserved);
        }

        $fields = $query->whereHas('fieldFieldset', function ($query) use ($name) {
            $query->whereHas('fieldset', function ($subquery) use ($name) {
                $subquery->where('name', $name);
            });
        })->get();
        foreach ($fields as $field) {
            $this->addCustomfield($grid, $field);
        }
        return $this;
    }

    /**
     * @param AbstractType $type
     */
    public function addType(AbstractType $type)
    {
        $this->controls[] = $type;
    }

    /**
     * Add control to controls collection
     *
     * @param \Antares\Html\Form\Field $control
     * @return $this
     */
    public function add(Field $control)
    {
        $renderable = $control instanceof \Illuminate\Contracts\Support\Renderable;


        $control->setField(function ($row, $cont, $templates) use ($control, $renderable) {

            $control = app(Control::class)
                ->setTemplates($this->control->getTemplates())
                ->setPresenter($this->control->getPresenter());

            $field = $control->buildFieldByType($cont->type, $row, $cont);
            $cont->setModel($row);
            if (($value = $cont->getValue()) !== false) {
                $field->value($value);
            }
            if ($renderable) {
                return $cont;
            }

            return $control->render([], $field);
        });


        $this->controls[] = $control;
        return $this;
    }

    /**
     * Build control.
     *
     * @param  mixed $name
     * @param  mixed $callback
     *
     * @return array
     */
    protected function buildControl($name, $callback = null, $type = null)
    {
        list($label, $name, $callback) = $this->buildFluentAttributes($name, $callback);

        $control = new Field([
            'id'         => $name,
            'name'       => $name,
            'value'      => null,
            'label'      => $label,
            'attributes' => [],
            'options'    => [],
            'checked'    => false,
            'type'       => $type,
            'field'      => null,
        ]);
        is_callable($callback) && call_user_func($callback, $control);

        return [$name, $control];
    }

    /**
     * Set Fieldset Legend name.
     *
     * <code>
     *     $fieldset->legend('User Information');
     * </code>
     *
     * @param  string $name
     *
     * @return string
     */
    public function legend($name = null)
    {
        if (!is_null($name)) {
            $this->legend = $name;
        }

        return $this->legend;
    }

    /**
     * Get fieldset name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * detach control from controls collection
     *
     * @param FieldContract $control
     * @return Fieldset
     */
    public function detachControl(FieldContract $control)
    {
        $name  = $control->name;
        $value = $control->value;
        foreach ($this->controls as $index => $field) {
            if ($field->name == $name && $field->value == $value) {
                unset($this->controls[$index]);
                unset($this->keyMap[$name]);
            }
        }
        return $this;
    }

    /**
     * controls setter
     *
     * @param array $controls
     * @return Fieldset
     */
    public function setControls(array $controls = [])
    {
        $this->controls = $controls;
        return $this;
    }

    /**
     * gets field by name
     *
     * @param String $name
     * @return Field
     * @throws Exception
     */
    public function field($name)
    {

        if (!isset($this->keyMap[$name])) {
            throw new Exception(sprintf('Unable to find %s named field.', $name));
        }
        return $this->controls[$this->keyMap[$name]];
    }

    /**
     * get control list by type
     *
     * @param String $name
     * @return array
     */
    public function types($name)
    {
        $return = [];
        foreach ($this->controls as $control) {
            if ((method_exists($control, 'getType') ? $control->getType() : $control->type) == $name) {
                array_push($return, $control);
            }
        }
        return $return;
    }

    /**
     * whether fieldset has control specified by name
     *
     * @param String $name
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->keyMap[$name]);
    }

    /**
     * fieldset layout setter
     *
     * @param String $layout
     * @param array $params
     * @return \Antares\Html\Form\Fieldset
     */
    public function layout($layout, $params = [])
    {
        $this->layout = $layout;
        $this->params = $params;
        return $this;
    }

    /**
     * renders custom fieldset view
     *
     * @return \Illuminate\View\View
     */
    public function render($row = null)
    {
        if (is_null($this->layout)) {
            throw new Exception('Unable to render fieldset layout. Layout is empty.');
        }
        $attributes = array_merge([
            'controls'   => $this->controls,
            'name'       => $this->name,
            'attributes' => $this->attributes,
            'row'        => $row,
            'legend'     => $this->legend], $this->params);

        return view($this->layout)->with($attributes);
    }

    /**
     * retrives all controls from fieldsets
     *
     * @return array
     */
    public function controls()
    {
        $return = [];
        foreach ($this->controls as $control) {
            if (in_array((method_exists($control, 'getType') ? $control->getType() : $control->type),
                ['button', 'submit']
            )) {
                continue;
            }
            array_push($return, $control);
        }
        return $return;
    }

    /**
     * whether fieldset has layout
     *
     * @return boolean
     */
    public function hasLayout()
    {
        return !is_null($this->layout);
    }

    /**
     * @return string
     */
    public function getOrientation(): string
    {
        return $this->orientation;
    }

    /**
     * Orientation can be 'horizontal' or 'vertical'
     *
     * @param string $orientation
     */
    public function setOrientation(string $orientation)
    {
        $this->orientation = $orientation;
    }

}
