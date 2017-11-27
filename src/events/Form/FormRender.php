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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Events\Form;

use Antares\Html\Form\FormBuilder;
use Illuminate\Database\Eloquent\Model;
use Antares\Foundation\Events\AbstractEvent;

class FormRender extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Form: rendered';

    /** @var string */
    protected static $description = 'Runs when form is rendered';

    /** @var string */
    public $formName;

    /** @var FormBuilder */
    public $form;

    /** @var mixed */
    public $model;

    /** @var string|null */
    public $action;

    /**
     * Form constructor
     *
     * @param string      $formName
     * @param FormBuilder $form
     * @param string|null $action
     * @param mixed  $model
     */
    public function __construct(string $formName, FormBuilder $form, string $action = null, $model = null)
    {
        $this->formName = $formName;
        $this->form     = $form;
        $this->action   = $action;
        $this->model    = $model;

        parent::__construct();
    }

}
