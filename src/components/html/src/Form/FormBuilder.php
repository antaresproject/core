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

use Antares\Contracts\Html\Adapter\FieldPermissionAdapter as FieldPermissionContract;
use Antares\Contracts\Html\Form\Builder as BuilderContract;
use Antares\Contracts\Html\Grid as GridContract;
use Antares\Form\Controls\AbstractType;
use Antares\Html\Adapter\CustomfieldAdapter;
use Antares\Html\Builder as BaseBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Validator;
use Antares\Html\Form\ClientScript;
use Antares\Support\Facades\Memory;
use Illuminate\Support\Collection;
use Antares\Html\RulesDispatcher;
use Antares\Html\Form\Grid;
use Closure;

class FormBuilder extends BaseBuilder implements BuilderContract
{

    /**
     * field permission adapter instance
     *
     * @var FieldPermissionContract
     */
    protected $fieldPermissionAdapter;

    /**
     * validator instance
     *
     * @var \Antares\Html\Validation\Validator
     */
    protected $validator;

    /**
     * message bag container
     *
     * @var \Antares\Messages\MessageBag
     */
    protected $messageBag;

    /**
     * hidden form key
     *
     * @var String
     */
    protected $hiddenKey;

    /**
     * custom fields validator attributes
     *
     * @var array 
     */
    protected $customFieldsValidator = [];

    /**
     * CustomfieldAdapter instance
     *
     * @var CustomfieldAdapter 
     */
    protected $customFieldAdapter = null;

    /**
     * {@inheritdoc}
     */
    public function __construct(GridContract $grid = null)
    {
        $this->container              = app();
        $this->grid                   = !is_null($grid) ? $grid : app(Grid::class);
        $this->clientScript           = app(ClientScript::class);
        $this->fieldPermissionAdapter = app('Antares\Html\Adapter\FieldPermissionAdapter');
        $this->validator              = app('Antares\Html\Validation\Validator');
        $this->customFieldAdapter     = app(CustomfieldAdapter::class);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        app('antares.asset')->container('antares/foundation::application')->add('webpack_forms_basic', '/webpack/forms_basic.js', ['app_cache']);
        $grid   = $this->grid;
        $action = '';
        if ($grid->row instanceof Model) {
            $action = $grid->row->exists ? 'edit' : 'create';
        }
        $events = $this->container->make('events');

        $events->fire('antares.form: ' . snake_case($grid->name) . (($action) ? '.' . $action : ''), [$grid->row, $this]);
        $customFieldsActive = app('antares.extension')->isActive('customfields');
        if ($customFieldsActive) {
            $events->fire('antares.form: ready', $this);
        }
        $events->fire('before.form.render', $grid);

        $this->addHiddenKey($grid);
        $isEmpty = $grid->fieldsets()->isEmpty();
        if (($grid->ajaxable !== false && !$isEmpty) or $grid->clientValidation) {
            $clientScript = $this->clientScript->addClientValidation($grid);
            $form         = $clientScript['form'];
            $fieldsets    = $clientScript['fieldsets'];
        } else {
            $fieldsets = $grid->fieldsets();
            $form      = $grid->attributes;
        }

        if (!$isEmpty) {
            $this->fieldPermissionAdapter->resolveFields($fieldsets);
        }
        $buttons = [];
        array_map(function($fieldset) use(&$buttons) {
            $buttons = array_merge($buttons, $fieldset->types('button'));
        }, $fieldsets->toArray());
        $this->customFieldAdapter->adapt($grid);

        $data           = [
            'grid'      => $grid,
            'fieldsets' => $fieldsets,
            'form'      => $form,
            'format'    => $grid->format,
            'hiddens'   => $grid->hiddens,
            'tests'     => $grid->tests,
            'row'       => $grid->row,
            'submit'    => $this->container->make('translator')->get($grid->submit),
            'token'     => $grid->token,
            'buttons'   => $buttons
        ];
        $controlCounter = 0;

        /** we need to know how many fields does the form has * */
        $fieldsets->each(function($fieldset) use(&$controlCounter) {
            $controlCounter += count($fieldset->controls);
        });
        $viewFactory = $this->container->make('view');
        $this->clientSubmit($buttons);
        view()->share('grid_container_class', 'grid-container--footer');
        
        return $viewFactory->make($grid->view)->with($data)->with($grid->params)->render();
    }

    /**
     * Whether form can be submited by Ctrl+Enter
     * 
     * @param array $buttons
     * @return boolean
     */
    protected function clientSubmit($buttons)
    {
        if (empty($buttons)) {
            return false;
        }
        $scripts = false;
        foreach ($buttons as $button) {
            if (array_get($button instanceof AbstractType ? $button->getAttributes() : $button->attributes, 'type')
	            !== 'submit' or array_get($button instanceof AbstractType ? $button->getAttributes()
		            : $button->attributes, 'disable_client_submit', false) == true
            ) {
                continue;
            }
            $atttibutes = $button instanceof AbstractType ? $button->getAttributes() : $button->attributes;
            if (isset($atttibutes['disable_client_submit'])) {
                unset($atttibutes['disable_client_submit']);
            }
            $atttibutes['class'] = array_get($atttibutes, 'class', '') . ' client-submit';


            $atttibutes['data-title'] = trans('antares/foundation::messages.are_you_sure');
            $button->attributes       = $button instanceof AbstractType ? $button->getAttributes() : $button->attributes;
            $scripts                  = true;
        }
        if ($scripts) {
            publish(null, '/packages/core/js/submitter.js');
        }
        return $buttons;
    }

    /**
     * append hidden key with resource info
     * 
     * @param GridContract $grid
     * @return boolean
     */
    protected function addHiddenKey(GridContract $grid)
    {
        $fieldsets = $grid->fieldsets();
        if ($fieldsets->isEmpty()) {
            return false;
        }
        $name    = $fieldsets->first()->name;
        $name instanceof Closure and $name    = sprintf('fieldset-%d', $fieldsets->count());
        $slugged = str_slug($name);
        $grid->hidden('key', function($field) use($slugged) {
            $control         = Memory::make('runtime')->get('control');
            $field->value    = $this->hiddenKey = Crypt::encrypt(implode('::', [$control['component'], $control['action'], $control['method'], $slugged]));
            return $field;
        });
        return true;
    }

    /**
     * field permission adapter getter
     * 
     * @return FieldPermissionContract
     */
    public function getFieldPermissionAdapter()
    {
        return $this->fieldPermissionAdapter;
    }

    /**
     * custom fields validator setter
     * 
     * @param String $on
     * @param String $event
     * @param array $attributes
     * @return \Antares\Html\Form\FormBuilder
     */
    public function setCustomFieldsValidator($on, $event, array $attributes = array())
    {
        $this->customFieldsValidator[$on] = [$event => $attributes];
        return $this;
    }

    /**
     * does the form is valid
     * 
     * @return mixed
     */
    public function isValid($sendHeaders = true)
    {
        app(\Antares\Html\Adapter\CustomfieldAdapter::class)->adapt($this->grid);
        $decryptedKey = is_null($key          = Input::get('key')) ? null : Crypt::decrypt($key);

        if ($this->grid->ajaxable !== false && !$this->container->make('antares.request')->shouldMakeApiResponse()) {
            $clientScript = $this->clientScript->addClientValidation($this->grid);
            $fieldsets    = $clientScript['fieldsets'];
            $this->fieldPermissionAdapter->resolveFields($fieldsets, $decryptedKey);
            $this->rules($fieldsets);
        } else {

            $fieldsets = $this->grid->fieldsets();
            $this->fieldPermissionAdapter->resolveFields($fieldsets, $decryptedKey);
            $this->rules($fieldsets);
        }


        $validator        = $this->validator->with($this->grid);
        $validator->withCustomFields($this->customFieldsValidator);
        $result           = $validator->validate($sendHeaders);
        $this->messageBag = $validator->getMessageBag();
        return $result;
    }

    /**
     * message bag getter
     * 
     * @return \Antares\Messages\MessageBag
     */
    public function getMessageBag()
    {
        return $this->messageBag;
    }

    /**
     * verify rules settings depends on controls visibility
     * 
     * @param Collection $fieldsets
     */
    protected function rules(Collection $fieldsets)
    {
        $grid = $this->grid;
        if (!empty($grid->rules)) {
            $controls = [];
            foreach ($fieldsets as $fieldset) {
                foreach ($fieldset->controls() as $control) {
                    array_push(
                        $controls,
                        method_exists($control, 'getName') ? $control->getName() : $control->name
                    );
                }
            }
            $rulesDispatcher = new RulesDispatcher($grid->rules);
            $this->grid->rules($rulesDispatcher->getSupported($controls));
        }
    }

    /**
     * gets data from post by form controls
     * 
     * @return array
     */
    public function getData()
    {
        $fieldsets = $this->grid->fieldsets;
        $controls  = [];
        foreach ($fieldsets as $fieldset) {
            foreach ($fieldset->controls() as $control) {
                array_push($controls, $control->name);
            }
        }
        $keys = $this->resolveWithMultiples($controls);
        return array_only(Input::all(), $keys);
    }

    /**
     * resolve form keys with multiple fields
     * 
     * @param array $controlKeys
     * @return array
     */
    protected function resolveWithMultiples(array $controlKeys = array())
    {
        $keys   = array_keys(Input::all());
        $return = [];
        foreach ($controlKeys as $key) {
            if (str_contains($key, '[]') and array_search(str_replace(['[', ']'], '', $key), $keys)) {
                array_push($return, str_replace(['[', ']'], '', $key));
                continue;
            }
            array_push($return, $key);
        }
        return $return;
    }

    /**
     * set custom validator
     * 
     * @param Validator $validator
     * @return \Antares\Html\Form\FormBuilder
     */
    public function with(Validator $validator)
    {
        $this->grid->setCustomValidator($validator);
        return $this;
    }

    public function getRawResponse()
    {
        $grid               = $this->grid;
        $customFieldsActive = app('antares.extension')->isActive('customfields');
        $events             = $this->container->make('events');
        if ($customFieldsActive) {
            $events->fire('antares.form: ready', $this);
        }
        $events->fire('before.form.render', $grid);

        $this->addHiddenKey($grid);
        $isEmpty = $grid->fieldsets()->isEmpty();

        $fieldsets = $grid->fieldsets();
        $form      = $grid->attributes;

        if (!$isEmpty) {
            $this->fieldPermissionAdapter->resolveFields($fieldsets);
        }

        $data = [
            'grid'      => $grid,
            'fieldsets' => $fieldsets,
            'form'      => $form,
            'format'    => $grid->format,
            'hiddens'   => $grid->hiddens,
            'tests'     => $grid->tests,
            'row'       => $grid->row,
            'token'     => $grid->token,
        ];

        return $data;
    }

}
